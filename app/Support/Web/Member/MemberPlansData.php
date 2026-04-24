<?php

namespace App\Support\Web\Member;

use App\Models\Plan;
use App\Models\User;

class MemberPlansData
{
    public static function make(User $user): array
    {
        $currentSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->latest('id')
            ->first();
        
        return [
            'current_plan_id' => $currentSubscription?->plan_id,
            
            'plans' => Plan::query()
                ->where('is_active', true)
                ->orderBy('price')
                ->get()
                ->map(fn (Plan $plan): array => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'price' => $plan->price,
                    'billing_interval' => $plan->billing_interval,
                    'is_current' => $currentSubscription?->plan_id === $plan->id,
                ]),
        ];
    }
}