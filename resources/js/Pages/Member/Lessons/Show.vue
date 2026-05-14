<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

defineProps<{
  course: {
    id: number
    title: string
  }
  lesson: {
    id: number
    title: string
    content: string
    sort_order: number
  }
  navigation: {
    previous: null | {
      id: number
      title: string
    }
    next: null | {
      id: number
      title: string
    }
  }
}>()
</script>

<template>
  <Head title="Lesson" />

  <MemberLayout title="Lesson">
    <div class="space-y-6">
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
          <div>
            <Link
                :href="`/member/courses/${course.id}`"
                class="text-sm font-medium text-slate-500 transition hover:text-slate-900"
            >
              ← Back to course
            </Link>

            <p class="mt-6 text-sm font-medium uppercase tracking-wide text-slate-500">
              Lesson {{ lesson.sort_order }}
            </p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
              {{ lesson.title }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
              {{ course.title }}
            </p>
          </div>
        </div>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="prose prose-slate max-w-none">
          <div class="whitespace-pre-line text-sm leading-7 text-slate-700">
            {{ lesson.content }}
          </div>
        </div>
      </section>

      <section class="grid gap-4 md:grid-cols-2">
        <Link
            v-if="navigation.previous"
            :href="`/member/courses/${course.id}/lessons/${navigation.previous.id}`"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:bg-slate-50"
        >
          <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
            Previous lesson
          </p>

          <p class="mt-2 font-semibold text-slate-900">
            {{ navigation.previous.title }}
          </p>
        </Link>

        <div v-else />

        <Link
            v-if="navigation.next"
            :href="`/member/courses/${course.id}/lessons/${navigation.next.id}`"
            class="rounded-2xl border border-slate-200 bg-white p-5 text-right shadow-sm transition hover:bg-slate-50"
        >
          <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
            Next lesson
          </p>

          <p class="mt-2 font-semibold text-slate-900">
            {{ navigation.next.title }}
          </p>
        </Link>
      </section>
    </div>
  </MemberLayout>
</template>