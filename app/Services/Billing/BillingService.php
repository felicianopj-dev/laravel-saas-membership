<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Billing\Contracts\BillingProviderInterface;
use Illuminate\Validation\ValidationException;

class BillingService
{
    public function __construct(
        private readonly BillingProviderInterface $billingProvider,
    ) {}

    public function createSubscriptionCheckout(User $user, Plan $plan): string
    {
        if (! $plan->is_active) {
            throw ValidationException::withMessages([
                'plan' => 'This plan is not available.',
            ]);
        }

        $currentSubscription = $this->currentBillableSubscription($user);

        if ($currentSubscription?->plan_id === $plan->id) {
            throw ValidationException::withMessages([
                'plan' => 'You are already subscribed to this plan.',
            ]);
        }

        if ($plan->price === 0) {
            return $this->activateFreePlan($user, $plan, $currentSubscription);
        }

        if ($currentSubscription?->stripe_id) {
            return $this->billingProvider->changeSubscriptionPlan(
                user: $user,
                subscription: $currentSubscription,
                plan: $plan,
            );
        }

        return $this->billingProvider->createSubscriptionCheckout($user, $plan);
    }

    public function cancel(User $user): Subscription
    {
        $subscription = $this->currentBillableSubscription($user);

        if (! $subscription) {
            throw ValidationException::withMessages([
                'subscription' => 'No active subscription found.',
            ]);
        }

        if ($subscription->stripe_id) {
            $this->billingProvider->cancelSubscription($user, $subscription);

            return $subscription->fresh();
        }

        $subscription->update([
            'status' => 'canceled',
            'stripe_status' => $subscription->stripe_status ? 'canceled' : null,
            'ends_at' => now(),
        ]);

        return $subscription->fresh();
    }

    public function resume(User $user): Subscription
    {
        $subscription = $user->currentSubscription();

        // Only a subscription with a scheduled cancellation still inside its
        // paid-through window can be resumed. An already-ended one is not
        // returned by currentSubscription(), so this also covers expiry.
        if (! $subscription || ! $subscription->onGracePeriod()) {
            throw ValidationException::withMessages([
                'subscription' => 'No canceled subscription found.',
            ]);
        }

        $this->billingProvider->resumeSubscription($user, $subscription);

        return $subscription->fresh();
    }

    private function activateFreePlan(User $user, Plan $plan, ?Subscription $currentSubscription = null): string
    {
        if ($currentSubscription?->stripe_id) {
            $this->billingProvider->cancelSubscription($user, $currentSubscription);
        }

        $user->subscriptions()
            ->whereIn('status', ['active', 'trialing', 'past_due', 'incomplete'])
            ->update([
                'status' => 'canceled',
                'stripe_status' => 'canceled',
                'ends_at' => now(),
            ]);

        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'stripe_status' => null,
            'stripe_id' => null,
            'stripe_price' => null,
            'starts_at' => now(),
            'ends_at' => null,
            'trial_ends_at' => null,
            'current_period_end' => null,
        ]);

        return route('member.plans.index');
    }

    private function currentBillableSubscription(User $user): ?Subscription
    {
        return $user
            ->subscriptions()
            ->whereIn('status', ['active', 'trialing', 'past_due', 'incomplete'])
            ->latest('id')
            ->first();
    }
}
