<?php

namespace App\Services\Billing\Providers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Billing\Contracts\BillingProviderInterface;

class MockBillingProvider implements BillingProviderInterface
{
    public function createSubscriptionCheckout(User $user, Plan $plan): string
    {
        $this->cancelCurrentSubscriptions($user);
        
        $periodEnd = $plan->billing_interval === 'yearly'
            ? now()->addYear()
            : now()->addMonth();
        
        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'stripe_status' => null,
            'stripe_id' => null,
            'stripe_price' => null,
            'starts_at' => now(),
            'ends_at' => null,
            'trial_ends_at' => null,
            'current_period_end' => $periodEnd,
        ]);
        
        return route('member.plans.index');
    }
    
    public function changeSubscriptionPlan(User $user, Subscription $subscription, Plan $plan): string
    {
        $periodEnd = $plan->billing_interval === 'yearly'
            ? now()->addYear()
            : now()->addMonth();
        
        $subscription->update([
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $subscription->starts_at ?? now(),
            'ends_at' => null,
            'trial_ends_at' => null,
            'current_period_end' => $periodEnd,
        ]);
        
        return route('member.plans.index');
    }
    
    public function cancelSubscription(User $user, Subscription $subscription): string
    {
        $periodEnd = $subscription->current_period_end
            ?? $subscription->ends_at
            ?? now();
        
        $subscription->update([
            'status' => 'canceled',
            'ends_at' => $periodEnd,
            'current_period_end' => $periodEnd,
        ]);
        
        return route('member.plans.index');
    }
    
    public function resumeSubscription(User $user, Subscription $subscription): string
    {
        $periodEnd = $subscription->current_period_end
            ?? now()->addMonth();
        
        $subscription->update([
            'status' => 'active',
            'ends_at' => null,
            'current_period_end' => $periodEnd,
        ]);
        
        return route('member.plans.index');
    }
    
    private function cancelCurrentSubscriptions(User $user): void
    {
        $user->subscriptions()
            ->whereIn('status', ['active', 'trialing', 'past_due', 'incomplete'])
            ->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);
    }
}