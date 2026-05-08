<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class BillingService
{
    public function __construct(
        private readonly BillingProviderInterface $billingProvider,
    ) {
    }
    
    public function subscribe(User $user, Plan $plan): Subscription
    {
        if (! $plan->is_active) {
            throw ValidationException::withMessages([
                'plan' => 'This plan is not available.',
            ]);
        }
        
        $currentSubscription = $user
            ->subscriptions()
            ->where('status', 'active')
            ->latest('id')
            ->first();
        
        if ($currentSubscription?->plan_id === $plan->id) {
            throw ValidationException::withMessages([
                'plan' => 'You are already subscribed to this plan.',
            ]);
        }
        
        $result = $this->billingProvider->subscribe($user, $plan);
        
        return Subscription::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'plan_id' => $plan->id,
                'status' => $result->status,
                'starts_at' => $result->startsAt,
                'ends_at' => $result->endsAt,
                'trial_ends_at' => $result->trialEndsAt,
            ],
        );
    }
    
    public function cancel(User $user): Subscription
    {
        $subscription = $user->currentSubscription();
        
        if (! $subscription || $subscription->status !== 'active') {
            throw ValidationException::withMessages([
                'subscription' => 'No active subscription found.',
            ]);
        }
        
        $subscription->update([
            'status' => 'canceled',
        ]);
        
        return $subscription;
    }
    
    public function resume(User $user): Subscription
    {
        $subscription = $user->currentSubscription();
        
        if (! $subscription || $subscription->status !== 'canceled') {
            throw ValidationException::withMessages([
                'subscription' => 'No canceled subscription found.',
            ]);
        }
        
        if ($subscription->ends_at?->isPast()) {
            throw ValidationException::withMessages([
                'subscription' => 'This subscription has already expired. Please choose a new plan.',
            ]);
        }
        
        $subscription->update([
            'status' => 'active',
        ]);
        
        return $subscription;
    }
}