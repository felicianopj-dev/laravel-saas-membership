<?php

namespace App\Services\Billing\Contracts;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

interface BillingProviderInterface
{
    public function createSubscriptionCheckout(User $user, Plan $plan): string;

    public function changeSubscriptionPlan(User $user, Subscription $subscription, Plan $plan): string;

    public function cancelSubscription(User $user, Subscription $subscription): string;

    public function resumeSubscription(User $user, Subscription $subscription): string;
}
