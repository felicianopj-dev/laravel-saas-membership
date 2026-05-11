<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

defineProps<{
  course: {
    id: number
    title: string
  }
  lessons: {
    data: Array<{
      id: number
      title: string
      slug: string
      sort_order: number
      is_published: boolean
    }>
  }
}>()

const destroyLesson = (courseId: number, lessonId: number, title: string) => {
  if (!confirm(`Delete "${title}"?`)) {
    return
  }

  router.delete(`/admin/courses/${courseId}/lessons/${lessonId}`)
}
</script>

<template>
  <div class="space-y-6">
    <section class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div>
        <Link
            href="/admin/courses"
            class="text-sm font-medium text-slate-500 hover:text-slate-900"
        >
          ← Courses
        </Link>

        <h1 class="mt-4 text-2xl font-bold text-slate-900">
          {{ course.title }}
        </h1>

        <p class="mt-1 text-sm text-slate-500">
          Manage course lessons.
        </p>
      </div>

      <Link
          :href="`/admin/courses/${course.id}/lessons/create`"
          class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
      >
        New lesson
      </Link>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
        <tr>
          <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            Lesson
          </th>

          <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            Order
          </th>

          <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            Status
          </th>

          <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
            Actions
          </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-slate-200 bg-white">
        <tr
            v-for="lesson in lessons.data"
            :key="lesson.id"
        >
          <td class="px-6 py-4">
            <div class="font-semibold text-slate-900">
              {{ lesson.title }}
            </div>

            <div class="mt-1 text-sm text-slate-500">
              {{ lesson.slug }}
            </div>
          </td>

          <td class="px-6 py-4 text-sm text-slate-600">
            {{ lesson.sort_order }}
          </td>

          <td class="px-6 py-4">
              <span
                  class="rounded-full px-3 py-1 text-xs font-semibold"
                  :class="lesson.is_published
                  ? 'bg-emerald-100 text-emerald-700'
                  : 'bg-slate-100 text-slate-600'"
              >
                {{ lesson.is_published ? 'Published' : 'Draft' }}
              </span>
          </td>

          <td class="px-6 py-4">
            <div class="flex justify-end gap-3">
              <Link
                  :href="`/admin/courses/${course.id}/lessons/${lesson.id}/edit`"
                  class="text-sm font-medium text-slate-600 hover:text-slate-900"
              >
                Edit
              </Link>

              <button
                  type="button"
                  class="text-sm font-medium text-rose-600 hover:text-rose-700"
                  @click="destroyLesson(course.id, lesson.id, lesson.title)"
              >
                Delete
              </button>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>