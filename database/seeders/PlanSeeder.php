<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'price' => 0,
                'billing_interval' => 'monthly',
                'is_active' => true,
                'stripe_price_id' => null,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price' => 1900,
                'billing_interval' => 'monthly',
                'is_active' => true,
                'stripe_price_id' => config('services.stripe.prices.monthly.starter'),
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 4900,
                'billing_interval' => 'monthly',
                'is_active' => true,
                'stripe_price_id' => config('services.stripe.prices.monthly.pro'),
            ],
        ];
        
        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(
                ['slug' => $plan['slug']],
                $plan,
            );
        }
    }
}