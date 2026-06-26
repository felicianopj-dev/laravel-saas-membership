<?php

use App\Models\Course;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

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

    $availableCourse = Course::factory()->create([
        'title' => 'Laravel Foundations',
        'is_published' => true,
    ]);
    $availableCourse->plans()->attach($plan);

    $lockedCourse = Course::factory()->create([
        'title' => 'Locked Course',
        'is_published' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('member.courses.index'));

    $response->assertOk()->assertInertia(
        fn (Assert $page) => $page
            ->component('Member/Courses/Index')
            ->has('availableCourses', 1)
            ->where('availableCourses.0.title', 'Laravel Foundations')
            ->has('lockedCourses', 1)
            ->where('lockedCourses.0.title', 'Locked Course')
    );
});
