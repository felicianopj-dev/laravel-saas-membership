<?php

namespace App\Jobs;

use App\Models\Plan;
use App\Models\StripeWebhookEvent;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Billing\Concerns\ResolvesStripeTimestamps;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\StripeClient;

class ProcessStripeWebhookEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use ResolvesStripeTimestamps;
    use SerializesModels;

    private const ACTIVE_STATUSES = [
        'active',
        'trialing',
        'past_due',
        'incomplete',
    ];

    /**
     * @param  string  $payload  The raw, signature-verified webhook payload.
     */
    public function __construct(
        private readonly string $payload,
    ) {}

    public function handle(): void
    {
        $event = Event::constructFrom(json_decode($this->payload, true));

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event->data->object),
            'customer.subscription.created',
            'customer.subscription.updated' => $this->syncSubscription($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            default => Log::info('Unhandled Stripe webhook event.', ['type' => $event->type]),
        };

        StripeWebhookEvent::query()
            ->where('stripe_event_id', $event->id)
            ->update(['processed_at' => now()]);
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

        $subscription = $stripe->subscriptions->retrieve($session->subscription, []);

        $this->syncSubscription($subscription, $session);
    }

    private function syncSubscription(object $stripeSubscription, ?object $session = null): void
    {
        $stripePriceId = $stripeSubscription->items->data[0]->price->id ?? null;

        if (! $stripePriceId) {
            Log::warning('Stripe webhook: no price id on subscription.', [
                'stripe_id' => $stripeSubscription->id ?? null,
            ]);

            return;
        }

        $plan = Plan::query()->where('stripe_price_id', $stripePriceId)->first();

        if (! $plan) {
            Log::warning('Stripe webhook: no local plan for price.', [
                'stripe_price_id' => $stripePriceId,
            ]);

            return;
        }

        $user = $this->resolveUser($stripeSubscription, $session);

        if (! $user) {
            Log::warning('Stripe webhook: could not resolve user.', [
                'stripe_id' => $stripeSubscription->id ?? null,
                'customer' => $stripeSubscription->customer ?? null,
            ]);

            return;
        }

        if (! empty($stripeSubscription->customer)) {
            $user->forceFill(['stripe_id' => $stripeSubscription->customer])->save();
        }

        $this->cancelOtherLocalSubscriptions($user, $stripeSubscription);

        $currentPeriodEnd = $this->resolveCurrentPeriodEnd($stripeSubscription);

        Subscription::query()->updateOrCreate(
            ['stripe_id' => $stripeSubscription->id],
            [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => $stripeSubscription->status,
                'stripe_status' => $stripeSubscription->status,
                'stripe_price' => $stripePriceId,
                'starts_at' => $this->timestampToDate($stripeSubscription->start_date ?? null),
                'trial_ends_at' => $this->timestampToDate($stripeSubscription->trial_end ?? null),
                'ends_at' => $this->resolveSubscriptionEndsAt($stripeSubscription, $currentPeriodEnd),
                'current_period_end' => $currentPeriodEnd,
            ],
        );
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

    private function resolveSubscriptionEndsAt(object $stripeSubscription, ?string $currentPeriodEnd): ?string
    {
        if (! empty($stripeSubscription->ended_at)) {
            return $this->timestampToDate($stripeSubscription->ended_at);
        }

        if (! empty($stripeSubscription->cancel_at)) {
            return $this->timestampToDate($stripeSubscription->cancel_at);
        }

        if (($stripeSubscription->cancel_at_period_end ?? false) === true) {
            return $currentPeriodEnd;
        }

        if (($stripeSubscription->status ?? null) === 'canceled') {
            return now()->toDateTimeString();
        }

        return null;
    }
}
