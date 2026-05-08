<?php

namespace App\Http\Controllers\Web\Member;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberCourseController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user()?->loadMissing('activeSubscription.plan');
        
        $currentPlanId = $user->activeSubscription?->plan?->id;
        
        $courses = Course::query()
            ->with('plans:id,name')
            ->where('is_published', true)
            ->orderBy('title')
            ->get();
        
        $availableCourses = $courses
            ->filter(fn (Course $course) => $course->plans->contains('id', $currentPlanId))
            ->values();
        
        $lockedCourses = $courses
            ->reject(fn (Course $course) => $course->plans->contains('id', $currentPlanId))
            ->values();
        
        return Inertia::render('Member/Courses/Index', [
            'availableCourses' => $availableCourses,
            'lockedCourses' => $lockedCourses,
        ]);
    }
}