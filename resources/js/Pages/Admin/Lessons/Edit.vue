<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import LessonForm from './LessonForm.vue'

const props = defineProps<{
  course: {
    id: number
    title: string
  }
  lesson: {
    id: number
    title: string
    content: string
    sort_order: number
    is_published: boolean
  }
}>()

const form = useForm({
  title: props.lesson.title,
  content: props.lesson.content,
  sort_order: props.lesson.sort_order,
  is_published: props.lesson.is_published,
})

const submit = () => {
  form.put(`/admin/courses/${props.course.id}/lessons/${props.lesson.id}`)
}
</script>

<template>
  <Head title="Edit Lesson" />

  <AdminLayout title="Edit Lesson">
    <div class="space-y-6">
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">
          Edit lesson
        </h1>

        <p class="mt-1 text-sm text-slate-500">
          Update lesson content for {{ course.title }}.
        </p>
      </section>

      <LessonForm
          :form="form"
          :course="course"
          submit-label="Save changes"
          @submit="submit"
      />
    </div>
  </AdminLayout>
</template>