<?php

use App\Models\Course;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

function createCourseForPlan(Plan $plan, array $attributes = []): Course
{
    $course = Course::factory()->create($attributes);
    
    $course->plans()->attach($plan);
    
    return $course;
}

it('keeps course access for canceled subscriptions until the end date', function () {
    $user = User::factory()->create([
        'role' => 'member',
        'status' => 'active',
    ]);
    
    $plan = Plan::factory()->create([
        'name' => 'Pro Monthly',
        'slug' => 'pro',
        'price' => 9900,
        'billing_interval' => 'monthly',
        'is_active' => true,
    ]);
    
    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'canceled',
        'starts_at' => now()->subMonth(),
        'ends_at' => now()->addDays(7),
    ]);
    
    $course = createCourseForPlan($plan, [
        'title' => 'Advanced Laravel',
        'slug' => 'advance-laravel',
        'is_published' => true,
    ]);
    
    $response = $this
        ->actingAs($user)
        ->get(route('member.courses.show', $course));
    
    $response
        ->assertOk()
        ->assertSee('Advanced Laravel');
});
