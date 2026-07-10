<?php

use App\Models\Plan;
use App\Models\User;

it('registers a member and subscribes them to the free plan', function () {
    $plan = Plan::factory()->create([
        'slug' => 'free',
        'price' => 0,
        'is_active' => true,
    ]);

    $response = $this->post('/register', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'password-123',
        'password_confirmation' => 'password-123',
    ]);

    $response->assertRedirect(route('member.dashboard'));

    $user = User::where('email', 'ada@example.com')->firstOrFail();

    $this->assertAuthenticatedAs($user);

    $this->assertDatabaseHas('subscriptions', [
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'active',
        'stripe_id' => null,
    ]);
});

it('rolls back the user when the free plan is missing', function () {
    $this->post('/register', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'password-123',
        'password_confirmation' => 'password-123',
    ]);

    // The whole registration is transactional: a missing free plan must not
    // leave an orphaned user (or subscription) row behind.
    $this->assertDatabaseMissing('users', ['email' => 'ada@example.com']);
    $this->assertDatabaseCount('subscriptions', 0);
    $this->assertGuest();
});
