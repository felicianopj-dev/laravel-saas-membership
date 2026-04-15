<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlanFactory extends Factory
{
    protected $model = Plan::class;
    
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Starter Monthly',
            'Pro Monthly',
            'Pro Yearly',
        ]);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'price' => fake()->randomElement([2900, 4900, 9900]),
            'billing_interval' => str_contains(strtolower($name), 'yearly') ? 'yearly' : 'monthly',
            'is_active' => true,
        ];
    }
}