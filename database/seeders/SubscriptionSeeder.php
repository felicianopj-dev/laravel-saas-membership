<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $starterPlan = Plan::query()->where('slug', 'starter')->first();
        $proPlan = Plan::query()->where('slug', 'pro')->first();
        
        if (! $starterPlan || ! $proPlan) {
            return;
        }
        
        $members = User::query()
            ->where('role', 'member')
            ->orderBy('id')
            ->get();
        
        foreach ($members as $index => $member) {
            $subscriptionData = match ($index % 4) {
                0 => [
                    'plan_id' => $proPlan->id,
                    'status' => 'active',
                    'starts_at' => Carbon::now()->subDays(10),
                    'ends_at' => Carbon::now()->addDays(20),
                    'trial_ends_at' => null,
                ],
                1 => [
                    'plan_id' => $starterPlan->id,
                    'status' => 'trialing',
                    'starts_at' => Carbon::now()->subDays(3),
                    'ends_at' => Carbon::now()->addDays(27),
                    'trial_ends_at' => Carbon::now()->addDays(4),
                ],
                2 => [
                    'plan_id' => $starterPlan->id,
                    'status' => 'canceled',
                    'starts_at' => Carbon::now()->subDays(40),
                    'ends_at' => Carbon::now()->subDays(5),
                    'trial_ends_at' => null,
                ],
                default => null,
            };
            
            if (! $subscriptionData) {
                continue;
            }
            
            Subscription::query()->updateOrCreate(
                ['user_id' => $member->id],
                array_merge(
                    ['user_id' => $member->id],
                    $subscriptionData,
                ),
            );
        }
    }
}