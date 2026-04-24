<?php

namespace App\Support\Web\Member;

use App\Models\Plan;

class MemberPlansData
{
    public static function make(): array
    {
        return [
            'plans' => Plan::query()
                ->where('is_active', true)
                ->orderBy('price')
                ->get()
                ->map(fn (Plan $plan): array => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'price' => $plan->price,
                    'billing_interval' => $plan->billing_interval,
                ]),
        ];
    }
}