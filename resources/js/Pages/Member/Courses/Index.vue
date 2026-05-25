<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

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
  <Head title="My Courses" />

  <MemberLayout title="My Courses">
    <div class="space-y-6">
      <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">
          Courses
        </h1>

        <p class="mt-1 text-sm text-slate-500">
          Access your available courses and unlock more with an upgraded plan.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-semibold text-slate-900">
          Available courses
        </h2>

        <div
            v-if="availableCourses.length"
            class="grid gap-3 md:grid-cols-2 xl:grid-cols-3"
        >
          <article
              v-for="course in availableCourses"
              :key="course.id"
              class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
          >
            <h3 class="font-semibold text-slate-900">
              {{ course.title }}
            </h3>

            <p class="mt-2 text-sm text-slate-600">
              {{ course.description || 'Start learning with this course.' }}
            </p>

            <a
                :href="`/member/courses/${course.id}`"
                class="mt-3 inline-flex text-sm font-medium text-slate-700 hover:text-slate-900"
            >
              Continue course
            </a>
          </article>
        </div>
      </section>

      <section class="space-y-3">
        <h2 class="text-lg font-semibold text-slate-900">
          Locked courses
        </h2>

        <div
            v-if="lockedCourses.length"
            class="grid gap-3 md:grid-cols-2 xl:grid-cols-3"
        >
          <article
              v-for="course in lockedCourses"
              :key="course.id"
              class="rounded-2xl border border-slate-200 bg-slate-50 p-4 opacity-80"
          >
            <div class="flex items-start justify-between gap-3">
              <h3 class="font-semibold text-slate-900">
                {{ course.title }}
              </h3>

              <span class="rounded-full bg-slate-200 px-2 py-1 text-xs font-medium text-slate-700">
              Locked
            </span>
            </div>

            <p class="mt-2 text-sm text-slate-600">
              {{ course.description || 'Upgrade your plan to access this course.' }}
            </p>
          </article>
        </div>
      </section>

      <section class="rounded-2xl bg-slate-900 p-4 text-white shadow-sm">
        <h2 class="text-lg font-semibold">
          Upgrade to unlock more courses
        </h2>

        <p class="mt-2 text-sm text-slate-300">
          Get access to premium courses, advanced lessons, and exclusive content.
        </p>

        <a
            href="/member/plans"
            class="mt-3 inline-flex rounded-lg bg-white px-4 py-2 text-sm font-medium text-slate-900 transition hover:bg-slate-100"
        >
          Upgrade plan
        </a>
      </section>
    </div>
  </MemberLayout>
</template>