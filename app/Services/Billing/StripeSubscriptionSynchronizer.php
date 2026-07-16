<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Billing\Concerns\ResolvesStripeTimestamps;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

/**
 * Reconciles a local subscription with its Stripe counterpart.
 *
 * Shared by the async webhook job (`ProcessStripeWebhookEvent`) and the
 * post-checkout eager sync in `PlanController`, so both paths write identical
 * state. Every write is idempotent (`updateOrCreate` on `stripe_id`), which is
 * what lets the eager sync and the webhook backstop run without duplicating.
 */
class StripeSubscriptionSynchronizer
{
    use ResolvesStripeTimestamps;

    private const ACTIVE_STATUSES = [
        'active',
        'trialing',
        'past_due',
        'incomplete',
    ];

    /**
     * Retrieve a completed checkout session by id and sync its subscription.
     *
     * Used by the eager, on-return sync. The `$expectedUserId` guard rejects a
     * session whose `client_reference_id` does not match the authenticated
     * user, so a member can't force a sync of someone else's session by
     * guessing a `session_id`.
     */
    public function syncFromCheckoutSessionId(string $sessionId, int $expectedUserId): void
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['subscription']]);

        if ((string) ($session->client_reference_id ?? '') !== (string) $expectedUserId) {
            Log::warning('Eager checkout sync: session does not belong to the current user.', [
                'session_id' => $sessionId,
                'expected_user_id' => $expectedUserId,
                'client_reference_id' => $session->client_reference_id ?? null,
            ]);

            return;
        }

        $this->syncFromCheckoutSession($session);
    }

    public function syncFromCheckoutSession(object $session): void
    {
        if (($session->mode ?? null) !== 'subscription') {
            return;
        }

        if (empty($session->subscription)) {
            return;
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        // The eager path expands `subscription` on the session, but the webhook
        // payload only carries the id; normalize by re-retrieving so both feed
        // syncSubscription a full subscription object.
        $subscriptionId = is_string($session->subscription)
            ? $session->subscription
            : $session->subscription->id;

        $subscription = $stripe->subscriptions->retrieve($subscriptionId, []);

        $this->syncSubscription($subscription, $session);
    }

    public function syncSubscription(object $stripeSubscription, ?object $session = null): void
    {
        $stripePriceId = $stripeSubscription->items->data[0]->price->id ?? null;

        if (! $stripePriceId) {
            Log::warning('Stripe sync: no price id on subscription.', [
                'stripe_id' => $stripeSubscription->id ?? null,
            ]);

            return;
        }

        $plan = Plan::query()->where('stripe_price_id', $stripePriceId)->first();

        if (! $plan) {
            Log::warning('Stripe sync: no local plan for price.', [
                'stripe_price_id' => $stripePriceId,
            ]);

            return;
        }

        $user = $this->resolveUser($stripeSubscription, $session);

        if (! $user) {
            Log::warning('Stripe sync: could not resolve user.', [
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

    public function markDeleted(object $stripeSubscription): void
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
