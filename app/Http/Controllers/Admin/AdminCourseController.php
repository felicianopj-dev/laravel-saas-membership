<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminCourseController extends Controller
{
    public function index(): Response
    {
        $courses = Course::query()
            ->with('plans:id,name')
            ->withCount('lessons')
            ->latest()
            ->paginate(10)
            ->through(fn (Course $course) => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => $course->description,
                'is_published' => $course->is_published,
                'lessons_count' => $course->lessons_count,
                'plans' => $course->plans->map(fn (Plan $plan) => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                ]),
                'created_at' => $course->created_at?->toISOString(),
            ]);

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Courses/Create', [
            'plans' => $this->plans(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_published' => ['required', 'boolean'],
            'plan_ids' => ['array'],
            'plan_ids.*' => ['integer', 'exists:plans,id'],
        ]);

        $course = Course::query()->create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'thumbnail' => null,
            'is_published' => $validated['is_published'],
        ]);

        $course->plans()->sync($validated['plan_ids'] ?? []);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course): Response
    {
        $course->load('plans:id');

        return Inertia::render('Admin/Courses/Edit', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'is_published' => $course->is_published,
                'plan_ids' => $course->plans->pluck('id')->values(),
            ],
            'plans' => $this->plans(),
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_published' => ['required', 'boolean'],
            'plan_ids' => ['array'],
            'plan_ids.*' => ['integer', 'exists:plans,id'],
        ]);

        $course->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'is_published' => $validated['is_published'],
        ]);

        $course->plans()->sync($validated['plan_ids'] ?? []);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    private function plans(): array
    {
        return Plan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get(['id', 'name', 'slug'])
            ->toArray();
    }
}
