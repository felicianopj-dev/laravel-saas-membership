<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

defineProps({
  course: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <Head title="Course Overview" />

  <MemberLayout title="Course Overview">
    <div class="space-y-4">
      <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <Link
            href="/member/courses"
            class="text-sm font-medium text-slate-500 transition hover:text-slate-900"
        >
          ← Back to courses
        </Link>

        <div class="mt-4">
          <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
            Course overview
          </p>

          <h1 class="mt-2 text-2xl font-bold text-slate-900">
            {{ course.title }}
          </h1>

          <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
            {{ course.description || 'Continue learning with this course.' }}
          </p>
        </div>

        <div class="mt-4">
          <Link
              v-if="course.lessons.length"
              :href="`/member/courses/${course.id}/lessons/${course.lessons[0].id}`"
              class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
          >
            Continue learning
          </Link>
        </div>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">
              Lessons
            </h2>

            <p class="mt-1 text-sm text-slate-500">
              Work through the lessons in order.
            </p>
          </div>

          <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
            {{ course.lessons.length }} lessons
          </span>
        </div>

        <div v-if="course.lessons.length" class="mt-4 divide-y divide-slate-200">
          <Link
              v-for="lesson in course.lessons"
              :key="lesson.id"
              :href="`/member/courses/${course.id}/lessons/${lesson.id}`"
              class="flex items-center justify-between py-3 transition hover:bg-slate-50"
          >
            <div>
              <p class="font-medium text-slate-900">
                {{ lesson.sort_order }}. {{ lesson.title }}
              </p>

              <p class="mt-1 text-sm text-slate-500">
                Lesson content
              </p>
            </div>

            <span class="text-sm font-medium text-slate-500">
              Open
            </span>
          </Link>
        </div>

        <div
            v-else
            class="mt-4 rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500"
        >
          No lessons available yet.
        </div>
      </section>
    </div>
  </MemberLayout>
</template>