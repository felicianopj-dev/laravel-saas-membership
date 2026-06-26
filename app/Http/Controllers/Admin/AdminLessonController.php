<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminLessonController extends Controller
{
    public function index(Course $course): Response
    {
        $lessons = $course->lessons()
            ->orderBy('sort_order')
            ->paginate(20)
            ->through(fn (Lesson $lesson) => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'slug' => $lesson->slug,
                'sort_order' => $lesson->sort_order,
                'is_published' => $lesson->is_published,
                'created_at' => $lesson->created_at?->toISOString(),
            ]);

        return Inertia::render('Admin/Lessons/Index', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
            ],
            'lessons' => $lessons,
        ]);
    }

    public function create(Course $course): Response
    {
        return Inertia::render('Admin/Lessons/Create', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
            ],
        ]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_published' => ['required', 'boolean'],
        ]);

        $course->lessons()->create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'sort_order' => $validated['sort_order'],
            'is_published' => $validated['is_published'],
        ]);

        return redirect()
            ->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson created successfully.');
    }

    public function edit(Course $course, Lesson $lesson): Response
    {
        abort_if($lesson->course_id !== $course->id, 404);

        return Inertia::render('Admin/Lessons/Edit', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
            ],
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'content' => $lesson->content,
                'sort_order' => $lesson->sort_order,
                'is_published' => $lesson->is_published,
            ],
        ]);
    }

    public function update(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        abort_if($lesson->course_id !== $course->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_published' => ['required', 'boolean'],
        ]);

        $lesson->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'sort_order' => $validated['sort_order'],
            'is_published' => $validated['is_published'],
        ]);

        return redirect()
            ->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Course $course, Lesson $lesson): RedirectResponse
    {
        abort_if($lesson->course_id !== $course->id, 404);

        $lesson->delete();

        return redirect()
            ->route('admin.courses.lessons.index', $course)
            ->with('success', 'Lesson deleted successfully.');
    }
}
