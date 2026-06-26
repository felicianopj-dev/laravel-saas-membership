<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function createCourseForPlan(Plan $plan, array $attributes = []): Course
{
    $course = Course::factory()->create($attributes);

    $course->plans()->attach($plan);

    return $course;
}

function memberWithCanceledButActiveSubscription(Plan $plan): User
{
    $user = User::factory()->create([
        'role' => 'member',
        'status' => 'active',
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'canceled',
        'starts_at' => now()->subMonth(),
        'ends_at' => now()->addDays(7),
    ]);

    return $user;
}

function proPlan(): Plan
{
    return Plan::factory()->create([
        'name' => 'Pro Monthly',
        'slug' => 'pro',
        'price' => 9900,
        'billing_interval' => 'monthly',
        'is_active' => true,
    ]);
}

it('keeps course access for canceled subscriptions until the end date', function () {
    $plan = proPlan();
    $user = memberWithCanceledButActiveSubscription($plan);

    $course = createCourseForPlan($plan, [
        'title' => 'Advanced Laravel',
        'slug' => 'advance-laravel',
        'is_published' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('member.courses.show', $course));

    $response->assertOk()->assertInertia(
        fn (Assert $page) => $page
            ->component('Member/Courses/Show')
            ->where('course.title', 'Advanced Laravel')
    );
});

it('keeps lesson access for canceled subscriptions until the end date', function () {
    $plan = proPlan();
    $user = memberWithCanceledButActiveSubscription($plan);

    $course = createCourseForPlan($plan, [
        'title' => 'Advanced Laravel',
        'slug' => 'advance-laravel',
        'is_published' => true,
    ]);

    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Service Containers',
        'is_published' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('member.courses.lessons.show', [$course, $lesson]));

    $response->assertOk()->assertInertia(
        fn (Assert $page) => $page
            ->component('Member/Lessons/Show')
            ->where('lesson.title', 'Service Containers')
    );
});
