<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

defineProps<{
  courses: {
    data: Array<{
      id: number
      title: string
      slug: string
      description: string | null
      is_published: boolean
      lessons_count: number
      plans: Array<{
        id: number
        name: string
      }>
      created_at: string | null
    }>
    links: Array<{
      url: string | null
      label: string
      active: boolean
    }>
  }
}>()

const destroyCourse = (course: { id: number; title: string }) => {
  if (!confirm(`Delete "${course.title}"?`)) {
    return
  }

  router.delete(`/admin/courses/${course.id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="space-y-6">
    <section class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">
          Courses
        </h1>

        <p class="mt-1 text-sm text-slate-500">
          Manage course content and plan access.
        </p>
      </div>

      <Link
          href="/admin/courses/create"
          class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
      >
        New course
      </Link>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              Course
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              Plans
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              Lessons
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
              v-for="course in courses.data"
              :key="course.id"
          >
            <td class="px-6 py-4">
              <div class="font-semibold text-slate-900">
                {{ course.title }}
              </div>

              <div class="mt-1 text-sm text-slate-500">
                {{ course.slug }}
              </div>
            </td>

            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-2">
                  <span
                      v-for="plan in course.plans"
                      :key="plan.id"
                      class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700"
                  >
                    {{ plan.name }}
                  </span>

                <span
                    v-if="!course.plans.length"
                    class="text-sm text-slate-400"
                >
                    No plans
                  </span>
              </div>
            </td>

            <td class="px-6 py-4 text-sm text-slate-600">
              {{ course.lessons_count }}
            </td>

            <td class="px-6 py-4">
                <span
                    class="rounded-full px-3 py-1 text-xs font-semibold"
                    :class="course.is_published
                    ? 'bg-emerald-100 text-emerald-700'
                    : 'bg-slate-100 text-slate-600'"
                >
                  {{ course.is_published ? 'Published' : 'Draft' }}
                </span>
            </td>

            <td class="px-6 py-4 text-right">
              <div class="flex justify-end gap-3">
                <Link
                    :href="`/admin/courses/${course.id}/edit`"
                    class="text-sm font-medium text-slate-600 hover:text-slate-900"
                >
                  Edit
                </Link>

                <button
                    type="button"
                    class="text-sm font-medium text-rose-600 hover:text-rose-700"
                    @click="destroyCourse(course)"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="!courses.data.length">
            <td
                colspan="5"
                class="px-6 py-10 text-center text-sm text-slate-500"
            >
              No courses found.
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>