<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\User;

interface BillingProviderInterface
{
    public function subscribe(User $user, Plan $plan): BillingSubscriptionResult;
}