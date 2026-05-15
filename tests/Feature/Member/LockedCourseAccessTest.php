<?php

use App\Models\Course;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

it('prevents members from accessing locked courses', function () {
    $user = User::factory()->create([
        'role' => 'member',
        'status' => 'active',
    ]);
    
    $starterPlan = Plan::factory()->create([
        'slug' => 'starter',
        'is_active' => true,
    ]);
    
    $proPlan = Plan::factory()->create([
        'slug' => 'pro',
        'is_active' => true,
    ]);
    
    Subscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $starterPlan->id,
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);
    
    $lockedCourse = Course::factory()->create([
        'is_published' => true,
    ]);
    
    $lockedCourse->plans()->attach($proPlan);
    
    $response = $this
        ->actingAs($user)
        ->get(route('member.courses.show', $lockedCourse));
    
    $response
        ->assertRedirect(route('member.courses.index'))
        ->assertSessionHas('error', 'Upgrade your plan to access this course.');
});