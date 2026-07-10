<?php

namespace App\Support\Web\Member;

use App\Models\User;

class MemberDashboardData
{
    public static function make(User $user): array
    {
        $subscription = $user
            ->latestSubscription()
            ->with('plan')
            ->first();

        return [
            'summary' => [
                'member_name' => $user->name,
                'member_email' => $user->email,
                'account_status' => $user->status,
                'access_role' => $user->role,
            ],
            'subscription' => $subscription
                ? [
                    'plan_name' => $subscription->plan?->name,
                    'plan_slug' => $subscription->plan?->slug,
                    'status' => $subscription->status,
                    'billing_interval' => $subscription->plan?->billing_interval,
                    'price' => $subscription->plan?->price,
                    'starts_at' => $subscription->starts_at?->toDateString(),
                    'ends_at' => $subscription->ends_at?->toDateString(),
                    'trial_ends_at' => $subscription->trial_ends_at?->toDateString(),
                    'has_access' => $user->hasAccess(),
                    'is_active' => $subscription->active() && ! $subscription->onGracePeriod(),
                    'is_canceled' => $subscription->onGracePeriod(),
                    'is_expired' => $subscription->ended(),
                ]
                : null,
        ];
    }
}
