<?php

namespace App\Support\Web\Member;

use App\Models\Plan;
use App\Models\User;

class MemberPlansData
{
    public static function make(User $user): array
    {
        $currentSubscription = $user->currentSubscription();
        
        return [
            'current_plan_id' => $currentSubscription?->plan_id,
            
            'plans' => Plan::query()
                ->where('is_active', true)
                ->get()
                ->map(fn (Plan $plan): array => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'price' => $plan->price,
                    'billing_interval' => $plan->billing_interval,
                    
                    'is_current' => $currentSubscription?->plan_id === $plan->id
                        && $user->hasAccess(),
                    
                    'is_current_active' => $currentSubscription?->plan_id === $plan->id
                        && $currentSubscription->status === 'active',
                    
                    'is_current_canceled' => $currentSubscription?->plan_id === $plan->id
                        && $currentSubscription->status === 'canceled'
                        && $user->hasAccess(),
                    
                    'current_subscription_ends_at' => $currentSubscription?->plan_id === $plan->id
                        ? $currentSubscription->ends_at?->toDateString()
                        : null,
                ]),
        ];
    }
}