<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberCourseShowController extends Controller
{
    public function __invoke(Request $request, Course $course): Response|RedirectResponse
    {
        $currentPlanId = $request->user()->accessiblePlanId();

        $course->load([
            'plans:id,name',
            'lessons' => fn ($query) => $query
                ->where('is_published', true)
                ->orderBy('sort_order'),
        ]);

        $hasAccess = $currentPlanId
            && $course->plans->contains('id', $currentPlanId);

        if (! $hasAccess) {
            return redirect()
                ->route('member.courses.index')
                ->with('error', 'Upgrade your plan to access this course.');
        }

        return Inertia::render('Member/Courses/Show', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail,
                'lessons' => $course->lessons->map(fn ($lesson) => [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'slug' => $lesson->slug,
                    'sort_order' => $lesson->sort_order,
                ]),
            ],
        ]);
    }
}
