<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $freePlan = Plan::query()->where('slug', 'free')->first();
        $proPlan = Plan::query()->where('slug', 'pro')->first();
        $premiumPlan = Plan::query()->where('slug', 'premium')->first();
        
        $courses = [
            [
                'title' => 'Laravel Membership Basics',
                'description' => 'Learn the foundations of membership-based Laravel applications.',
                'plans' => ['free', 'pro', 'premium'],
                'lessons' => [
                    'Understanding membership access',
                    'Building a member dashboard',
                    'Organizing member-only content',
                ],
            ],
            [
                'title' => 'SaaS Billing Architecture',
                'description' => 'A practical overview of subscription flows, plans, and access rules.',
                'plans' => ['pro', 'premium'],
                'lessons' => [
                    'Modeling plans and subscriptions',
                    'Handling subscription lifecycle states',
                    'Preparing the app for external billing providers',
                ],
            ],
            [
                'title' => 'Stripe Webhooks for SaaS',
                'description' => 'Explore how webhook-driven billing sync works in production SaaS apps.',
                'plans' => ['pro', 'premium'],
                'lessons' => [
                    'Why webhooks matter',
                    'Syncing subscription state',
                    'Handling failed payments and cancellations',
                ],
            ],
            [
                'title' => 'Advanced Laravel SaaS Patterns',
                'description' => 'Advanced patterns for scalable Laravel SaaS applications.',
                'plans' => ['premium'],
                'lessons' => [
                    'Service layer organization',
                    'Authorization and policy design',
                    'Designing extensible domains',
                ],
            ],
            [
                'title' => 'Premium Content Strategy',
                'description' => 'Demo premium course content unlocked only by higher membership levels.',
                'plans' => ['premium'],
                'lessons' => [
                    'Designing premium content libraries',
                    'Managing access tiers',
                    'Improving member retention',
                ],
            ],
        ];
        
        foreach ($courses as $courseData) {
            $course = Course::query()->updateOrCreate(
                ['slug' => Str::slug($courseData['title'])],
                [
                    'title' => $courseData['title'],
                    'description' => $courseData['description'],
                    'thumbnail' => null,
                    'is_published' => true,
                ],
            );
            
            foreach ($courseData['lessons'] as $index => $lessonTitle) {
                Lesson::query()->updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'slug' => Str::slug($lessonTitle),
                    ],
                    [
                        'title' => $lessonTitle,
                        'content' => $this->demoLessonContent($lessonTitle),
                        'sort_order' => $index + 1,
                        'is_published' => true,
                    ],
                );
            }
            
            $planIds = collect($courseData['plans'])
                ->map(fn (string $slug) => match ($slug) {
                    'free' => $freePlan?->id,
                    'pro' => $proPlan?->id,
                    'premium' => $premiumPlan?->id,
                    default => null,
                })
                ->filter()
                ->values()
                ->all();
            
            $course->plans()->sync($planIds);
        }
    }
    
    private function demoLessonContent(string $title): string
    {
        return <<<MARKDOWN
# {$title}

This is demo lesson content used to showcase plan-based membership access.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vitae justo non
massa fermentum posuere. Donec non sem vitae ipsum luctus facilisis.

The goal of this content is to demonstrate how a Laravel SaaS application can
restrict access to courses and lessons based on the user's current subscription plan.
MARKDOWN;
    }
}