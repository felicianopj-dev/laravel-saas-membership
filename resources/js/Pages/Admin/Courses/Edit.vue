<script setup lang="ts">
import {Head, Link, useForm} from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import CourseForm from './CourseForm.vue'

const props = defineProps<{
  course: {
    id: number
    title: string
    description: string | null
    is_published: boolean
    plan_ids: number[]
  }
  plans: Array<{
    id: number
    name: string
    slug: string
  }>
}>()

const form = useForm({
  title: props.course.title,
  description: props.course.description ?? '',
  is_published: props.course.is_published,
  plan_ids: props.course.plan_ids,
})

const submit = () => {
  form.put(`/admin/courses/${props.course.id}`)
}
</script>

<template>
  <Head title="Edit Course" />

  <AdminLayout title="Edit Course">
    <div class="space-y-6">
    <section class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">
          Edit course
        </h1>

        <p class="mt-1 text-sm text-slate-500">
          Update course details, publishing status, and plan access.
        </p>
      </div>

      <Link
          :href="`/admin/courses/${course.id}/lessons`"
          class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
      >
        Manage lessons
      </Link>
    </section>

    <CourseForm
        :form="form"
        :plans="plans"
        submit-label="Save changes"
        @submit="submit"
    />
    </div>
  </AdminLayout>
</template>