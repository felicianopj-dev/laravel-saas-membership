<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => '123',
            'role' => 'admin',
            'status' => 'active',
        ]);
        
        $members = User::factory(10)->create([
            'role' => 'member',
            'status' => 'active',
            'password' => '123',
        ]);
        
        $plans = collect([
            [
                'name' => 'Starter Monthly',
                'slug' => 'starter-monthly',
                'price' => 2900,
                'billing_interval' => 'monthly',
                'is_active' => true,
            ],
            [
                'name' => 'Pro Monthly',
                'slug' => 'pro-monthly',
                'price' => 4900,
                'billing_interval' => 'monthly',
                'is_active' => true,
            ],
            [
                'name' => 'Pro Yearly',
                'slug' => 'pro-yearly',
                'price' => 49000,
                'billing_interval' => 'yearly',
                'is_active' => true,
            ],
        ])->map(fn (array $plan) => Plan::query()->create($plan));
        
        foreach ($members as $member) {
            Subscription::factory()->create([
                'user_id' => $member->id,
                'plan_id' => $plans->random()->id,
            ]);
        }
        
        Subscription::factory()->create([
            'user_id' => $admin->id,
            'plan_id' => $plans->first()->id,
            'status' => 'active',
        ]);
    }
}