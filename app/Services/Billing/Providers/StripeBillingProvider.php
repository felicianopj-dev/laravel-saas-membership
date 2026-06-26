<?php

namespace App\Services\Billing\Providers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Billing\Contracts\BillingProviderInterface;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripeBillingProvider implements BillingProviderInterface
{
    public function createSubscriptionCheckout(User $user, Plan $plan): string
    {
        if (empty($plan->stripe_price_id)) {
            throw new RuntimeException('This plan does not have a Stripe price ID.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'subscription',
            'customer_email' => $user->email,
            'client_reference_id' => (string) $user->id,
            'line_items' => [
                [
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ],
            ],
            'success_url' => route('member.plans.index').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('member.plans.index'),
            'metadata' => [
                'user_id' => (string) $user->id,
                'plan_id' => (string) $plan->id,
            ],
            'subscription_data' => [
                'metadata' => [
                    'user_id' => (string) $user->id,
                    'plan_id' => (string) $plan->id,
                ],
            ],
        ]);

        if (empty($session->url)) {
            throw new RuntimeException('Stripe checkout session URL was not generated.');
        }

        return $session->url;
    }

    public function changeSubscriptionPlan(User $user, Subscription $subscription, Plan $plan): string
    {
        if (empty($subscription->stripe_id)) {
            throw new RuntimeException('This subscription does not have a Stripe subscription ID.');
        }

        if (empty($plan->stripe_price_id)) {
            throw new RuntimeException('This plan does not have a Stripe price ID.');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $stripeSubscription = $stripe->subscriptions->retrieve(
            $subscription->stripe_id,
            []
        );

        $subscriptionItemId = $stripeSubscription->items->data[0]->id ?? null;

        if (! $subscriptionItemId) {
            throw new RuntimeException('Stripe subscription item was not found.');
        }

        $stripe->subscriptionItems->update($subscriptionItemId, [
            'price' => $plan->stripe_price_id,
            'quantity' => 1,
            'proration_behavior' => 'create_prorations',
            'metadata' => [
                'user_id' => (string) $user->id,
                'plan_id' => (string) $plan->id,
            ],
        ]);

        $stripe->subscriptions->update($subscription->stripe_id, [
            'metadata' => [
                'user_id' => (string) $user->id,
                'plan_id' => (string) $plan->id,
            ],
        ]);

        $subscription->update([
            'plan_id' => $plan->id,
            'stripe_price' => $plan->stripe_price_id,
        ]);

        return route('member.plans.index');
    }

    public function cancelSubscription(User $user, Subscription $subscription): string
    {
        if (empty($subscription->stripe_id)) {
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

        $stripe = new StripeClient(config('services.stripe.secret'));

        $stripeSubscription = $stripe->subscriptions->update($subscription->stripe_id, [
            'cancel_at_period_end' => true,
        ]);

        $periodEnd = $this->timestampToDate(
            $stripeSubscription->current_period_end
            ?? $stripeSubscription->cancel_at
            ?? null
        );

        $subscription->update([
            'status' => 'canceled',
            'stripe_status' => $stripeSubscription->status,
            'ends_at' => $periodEnd,
            'current_period_end' => $periodEnd,
        ]);

        return route('member.plans.index');
    }

    public function resumeSubscription(User $user, Subscription $subscription): string
    {
        if (empty($subscription->stripe_id)) {
            $subscription->update([
                'status' => 'active',
                'ends_at' => null,
            ]);

            return route('member.plans.index');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $stripeSubscription = $stripe->subscriptions->update($subscription->stripe_id, [
            'cancel_at_period_end' => false,
        ]);

        $subscription->update([
            'status' => $stripeSubscription->status,
            'stripe_status' => $stripeSubscription->status,
            'ends_at' => null,
            'current_period_end' => $this->timestampToDate($stripeSubscription->current_period_end ?? null),
        ]);

        return route('member.plans.index');
    }

    private function timestampToDate(null|int|string $timestamp): ?string
    {
        if (! $timestamp) {
            return null;
        }

        return now()
            ->setTimestamp((int) $timestamp)
            ->toDateTimeString();
    }
}
