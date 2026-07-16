<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function memberOnPlan(Plan $plan, array $subscriptionOverrides = []): User
{
    $user = User::factory()->create([
        'role' => 'member',
        'status' => 'active',
    ]);

    Subscription::factory()->create(array_merge([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'active',
        'starts_at' => now()->subMonth(),
        'ends_at' => null,
    ], $subscriptionOverrides));

    return $user;
}

it('does not offer to cancel a free plan', function () {
    $free = Plan::factory()->create(['slug' => 'free', 'price' => 0, 'is_active' => true]);
    $user = memberOnPlan($free);

    $this->actingAs($user)
        ->get(route('member.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Member/Dashboard')
            ->where('subscription.is_active', true)
            ->where('subscription.can_cancel', false));
});

it('offers to cancel an active paid plan', function () {
    $paid = Plan::factory()->create(['slug' => 'pro', 'price' => 9900, 'is_active' => true]);
    $user = memberOnPlan($paid);

    $this->actingAs($user)
        ->get(route('member.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Member/Dashboard')
            ->where('subscription.can_cancel', true));
});

it('does not offer to cancel a paid plan already on its grace period', function () {
    $paid = Plan::factory()->create(['slug' => 'pro', 'price' => 9900, 'is_active' => true]);
    $user = memberOnPlan($paid, ['ends_at' => now()->addDays(7)]);

    $this->actingAs($user)
        ->get(route('member.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Member/Dashboard')
            ->where('subscription.can_cancel', false)
            ->where('subscription.is_canceled', true));
});
