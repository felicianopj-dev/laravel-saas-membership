<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MockBillingProvider implements BillingProviderInterface
{
    public function subscribe(User $user, Plan $plan): BillingSubscriptionResult
    {
        $startsAt = Carbon::now();
        
        $endsAt = $plan->billing_interval === 'yearly'
            ? $startsAt->copy()->addYear()
            : $startsAt->copy()->addMonth();
        
        return new BillingSubscriptionResult(
            provider: 'mock',
            status: 'active',
            providerSubscriptionId: 'mock_sub_' . Str::uuid(),
            providerCustomerId: 'mock_cus_' . $user->id,
            startsAt: $startsAt,
            endsAt: $endsAt,
            trialEndsAt: null,
        );
    }
}