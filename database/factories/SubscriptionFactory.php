<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;
    
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-3 months', 'now');
        
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'status' => fake()->randomElement(['active', 'trial', 'canceled']),
            'starts_at' => $startsAt,
            'ends_at' => fake()->boolean(20) ? fake()->dateTimeBetween('now', '+3 months') : null,
            'trial_ends_at' => fake()->boolean(30) ? fake()->dateTimeBetween('now', '+14 days') : null,
        ];
    }
}