<?php

namespace App\Services\Billing\Concerns;

trait ResolvesStripeTimestamps
{
    /**
     * Resolve a subscription's current period end as a datetime string.
     *
     * Stripe moved `current_period_end` off the subscription and onto the
     * subscription item in the 2025+ API versions, so we read the top-level
     * field first and fall back to the item. Centralized here so the webhook
     * job and the billing provider can't drift apart.
     */
    protected function resolveCurrentPeriodEnd(object $stripeSubscription): ?string
    {
        $timestamp = $stripeSubscription->current_period_end
            ?? $stripeSubscription->items->data[0]->current_period_end
            ?? null;

        return $this->timestampToDate($timestamp);
    }

    protected function timestampToDate(null|int|string $timestamp): ?string
    {
        if (! $timestamp) {
            return null;
        }

        return now()
            ->setTimestamp((int) $timestamp)
            ->toDateTimeString();
    }
}
