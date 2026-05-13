<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    private const ACTIVE_STATUSES = [
        'active',
        'trialing',
        'past_due',
        'incomplete',
    ];
    
    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret'),
            );
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return response('Invalid webhook.', 400);
        }
        
        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event->data->object),
            'customer.subscription.created',
            'customer.subscription.updated' => $this->syncSubscription($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            default => null,
        };
        
        return response('Webhook handled.', 200);
    }
    
    private function handleCheckoutSessionCompleted(object $session): void
    {
        if (($session->mode ?? null) !== 'subscription') {
            return;
        }
        
        if (empty($session->subscription)) {
            return;
        }
        
        $stripe = new StripeClient(config('services.stripe.secret'));
        
        $subscription = $stripe->subscriptions->retrieve(
            $session->subscription,
            []
        );
        
        $this->syncSubscription($subscription, $session);
    }
    
    private function syncSubscription(object $stripeSubscription, ?object $session = null): void
    {
        $stripePriceId = $stripeSubscription->items->data[0]->price->id ?? null;
        
        if (! $stripePriceId) {
            return;
        }
        
        $plan = Plan::query()
            ->where('stripe_price_id', $stripePriceId)
            ->first();
        
        if (! $plan) {
            return;
        }
        
        $user = $this->resolveUser($stripeSubscription, $session);
        
        if (! $user) {
            return;
        }
        
        if (! empty($stripeSubscription->customer)) {
            $user->forceFill([
                'stripe_id' => $stripeSubscription->customer,
            ])->save();
        }
        
        $this->cancelOtherLocalSubscriptions($user, $stripeSubscription);
        
        $currentPeriodEnd = $this->resolveCurrentPeriodEnd($stripeSubscription);
        
        Subscription::query()->updateOrCreate(
            [
                'stripe_id' => $stripeSubscription->id,
            ],
            [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => $this->resolveSubscriptionStatus($stripeSubscription),
                'stripe_status' => $stripeSubscription->status,
                'stripe_price' => $stripePriceId,
                'starts_at' => $this->timestampToDate($stripeSubscription->start_date ?? null),
                'trial_ends_at' => $this->timestampToDate($stripeSubscription->trial_end ?? null),
                'ends_at' => $this->resolveSubscriptionEndsAt($stripeSubscription, $currentPeriodEnd),
                'current_period_end' => $currentPeriodEnd,
            ],
        );
    }
    
    private function resolveCurrentPeriodEnd(object $stripeSubscription): ?string
    {
        $timestamp = $stripeSubscription->current_period_end
            ?? $stripeSubscription->items->data[0]->current_period_end
            ?? null;
        
        return $this->timestampToDate($timestamp);
    }
    
    private function handleSubscriptionDeleted(object $stripeSubscription): void
    {
        $subscription = Subscription::query()
            ->where('stripe_id', $stripeSubscription->id)
            ->first();
        
        if (! $subscription) {
            return;
        }
        
        $subscription->update([
            'status' => 'canceled',
            'stripe_status' => $stripeSubscription->status,
            'ends_at' => $this->timestampToDate($stripeSubscription->ended_at ?? time()),
            'current_period_end' => $this->timestampToDate($stripeSubscription->current_period_end ?? null),
        ]);
    }
    
    private function cancelOtherLocalSubscriptions(User $user, object $stripeSubscription): void
    {
        Subscription::query()
            ->where('user_id', $user->id)
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->where(function ($query) use ($stripeSubscription) {
                $query
                    ->whereNull('stripe_id')
                    ->orWhere('stripe_id', '!=', $stripeSubscription->id);
            })
            ->update([
                'status' => 'canceled',
                'stripe_status' => 'canceled',
                'ends_at' => now(),
            ]);
    }
    
    private function resolveUser(object $stripeSubscription, ?object $session = null): ?User
    {
        $userId = $session?->metadata?->user_id
            ?? $session?->client_reference_id
            ?? $stripeSubscription->metadata?->user_id
            ?? null;
        
        if ($userId) {
            return User::query()->find($userId);
        }
        
        if (! empty($stripeSubscription->customer)) {
            return User::query()
                ->where('stripe_id', $stripeSubscription->customer)
                ->first();
        }
        
        return null;
    }
    
    private function resolveSubscriptionStatus(object $stripeSubscription): string
    {
        if (($stripeSubscription->cancel_at_period_end ?? false) === true) {
            return 'canceled';
        }
        
        return $stripeSubscription->status;
    }
    
    private function resolveSubscriptionEndsAt(object $stripeSubscription): ?string
    {
        if (! empty($stripeSubscription->ended_at)) {
            return $this->timestampToDate($stripeSubscription->ended_at);
        }
        
        if (! empty($stripeSubscription->cancel_at)) {
            return $this->timestampToDate($stripeSubscription->cancel_at);
        }
        
        if (($stripeSubscription->cancel_at_period_end ?? false) === true) {
            return $this->timestampToDate($stripeSubscription->current_period_end ?? null);
        }
        
        if (($stripeSubscription->status ?? null) === 'canceled') {
            return now()->toDateTimeString();
        }
        
        return null;
    }
    
    private function timestampToDate(null|int|string $timestamp): ?string
    {
        if (! $timestamp) {
            return null;
        }
        
        return now()
            ->setTimestamp((int) $timestamp)
            ->toDateTimeString();
    }
}