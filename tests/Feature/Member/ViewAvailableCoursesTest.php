<?php

use App\Models\Course;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

it('allows members to view available courses for their plan', function () {
    $user = User::factory()->create([
        'role' => 'member',
        'status' => 'active',
    ]);
    
    $plan = Plan::factory()->create([
        'slug' => 'starter',
        'is_active' => true,
    ]);
    
    Subscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);
    
    $course = Course::factory()->create([
        'title' => 'Laravel Foundations',
        'is_published' => true,
    ]);
    
    $course->plans()->attach($plan);
    
    $response = $this
        ->actingAs($user)
        ->get(route('member.courses.index'));
    
    $response
        ->assertOk()
        ->assertSee('Laravel Foundations');
});