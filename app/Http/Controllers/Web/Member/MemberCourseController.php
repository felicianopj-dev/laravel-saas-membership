<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberCourseController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $currentPlanId = $request->user()->accessiblePlanId();

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
