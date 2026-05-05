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
}