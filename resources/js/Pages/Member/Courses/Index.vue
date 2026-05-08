<script setup lang="ts">
import MemberLayout from '@/Layouts/MemberLayout.vue'

defineOptions({
  layout: MemberLayout,
})

defineProps<{
  availableCourses: Array<{
    id: number
    title: string
    description: string | null
  }>
  lockedCourses: Array<{
    id: number
    title: string
    description: string | null
  }>
}>()
</script>

<template>
  <div class="space-y-8">
    <div>
      <h1 class="text-2xl font-semibold text-gray-900">
        Courses
      </h1>

      <p class="mt-1 text-sm text-gray-600">
        Access your available courses and unlock more with an upgraded plan.
      </p>
    </div>

    <section class="space-y-4">
      <h2 class="text-lg font-semibold text-gray-900">
        Available courses
      </h2>

      <div
          v-if="availableCourses.length"
          class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
      >
        <article
            v-for="course in availableCourses"
            :key="course.id"
            class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
        >
          <h3 class="font-semibold text-gray-900">
            {{ course.title }}
          </h3>

          <p class="mt-2 text-sm text-gray-600">
            {{ course.description || 'Start learning with this course.' }}
          </p>

          <a
              :href="`/member/courses/${course.id}`"
              class="mt-4 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-700"
          >
            Continue course
          </a>
        </article>
      </div>

      <div
          v-else
          class="rounded-xl border border-dashed border-gray-300 bg-white p-6 text-sm text-gray-600"
      >
        No courses available for your current plan.
      </div>
    </section>

    <section class="space-y-4">
      <h2 class="text-lg font-semibold text-gray-900">
        Locked courses
      </h2>

      <div
          v-if="lockedCourses.length"
          class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
      >
        <article
            v-for="course in lockedCourses"
            :key="course.id"
            class="rounded-xl border border-gray-200 bg-gray-50 p-5 opacity-80"
        >
          <div class="flex items-start justify-between gap-4">
            <h3 class="font-semibold text-gray-900">
              {{ course.title }}
            </h3>

            <span class="rounded-full bg-gray-200 px-2 py-1 text-xs font-medium text-gray-700">
              Locked
            </span>
          </div>

          <p class="mt-2 text-sm text-gray-600">
            {{ course.description || 'Upgrade your plan to access this course.' }}
          </p>
        </article>
      </div>
    </section>

    <section class="rounded-xl bg-indigo-600 p-6 text-white shadow-sm">
      <h2 class="text-lg font-semibold">
        Upgrade to unlock more courses
      </h2>

      <p class="mt-2 text-sm text-indigo-100">
        Get access to premium courses, advanced lessons, and exclusive content.
      </p>

      <a
          href="/member/billing"
          class="mt-4 inline-flex rounded-lg bg-white px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50"
      >
        Upgrade plan
      </a>
    </section>
  </div>
</template>