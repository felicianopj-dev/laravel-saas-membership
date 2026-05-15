<?php

use App\Models\Plan;
use App\Models\User;

it('allows admins to manage plans', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
    ]);
    
    $data = [
        'name' => 'Professional',
        'slug' => 'professional',
        'price' => 2900,
        'billing_interval' => 'monthly',
        'stripe_price_id' => 'price_test_123',
        'is_active' => true,
        'currency' => 'usd',
        'sort_order' => 1,
    ];
    
    $response = $this
        ->actingAs($admin)
        ->post(route('admin.plans.store'), $data);
    
    $response->assertRedirect();
    
    $this->assertDatabaseHas('plans', $data);
});