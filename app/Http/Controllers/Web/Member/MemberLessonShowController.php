<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberLessonShowController extends Controller
{
    public function __invoke(Request $request, Course $course, Lesson $lesson): Response|RedirectResponse
    {
        $currentPlanId = $request->user()->accessiblePlanId();

        $course->load('plans:id,name');

        $hasAccess = $currentPlanId
            && $course->plans->contains('id', $currentPlanId);

        if (! $hasAccess) {
            return redirect()
                ->route('member.courses.index')
                ->with('error', 'Upgrade your plan to access this course.');
        }

        if ($lesson->course_id !== $course->id || ! $lesson->is_published) {
            abort(404);
        }

        $lessons = $course->lessons()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get(['id', 'course_id', 'title', 'slug', 'sort_order']);

        $currentIndex = $lessons->search(fn (Lesson $item) => $item->id === $lesson->id);

        $previousLesson = $currentIndex > 0
            ? $lessons[$currentIndex - 1]
            : null;

        $nextLesson = $currentIndex !== false && $currentIndex < $lessons->count() - 1
            ? $lessons[$currentIndex + 1]
            : null;

        return Inertia::render('Member/Lessons/Show', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
            ],
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'content' => $lesson->content,
                'sort_order' => $lesson->sort_order,
            ],
            'navigation' => [
                'previous' => $previousLesson ? [
                    'id' => $previousLesson->id,
                    'title' => $previousLesson->title,
                ] : null,
                'next' => $nextLesson ? [
                    'id' => $nextLesson->id,
                    'title' => $nextLesson->title,
                ] : null,
            ],
        ]);
    }
}
