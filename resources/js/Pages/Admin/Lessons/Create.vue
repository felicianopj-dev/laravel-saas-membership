<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import LessonForm from './LessonForm.vue'

const props = defineProps<{
  course: {
    id: number
    title: string
  }
}>()

const form = useForm({
  title: '',
  content: '',
  sort_order: 1,
  is_published: true,
})

const submit = () => {
  form.post(`/admin/courses/${props.course.id}/lessons`)
}
</script>

<template>
  <Head title="Create Lesson" />

  <AdminLayout title="Create Lesson">
    <div class="space-y-6">
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">
          Create lesson
        </h1>

        <p class="mt-1 text-sm text-slate-500">
          Add a new lesson to {{ course.title }}.
        </p>
      </section>

      <LessonForm
          :form="form"
          :course="course"
          submit-label="Create lesson"
          @submit="submit"
      />
    </div>
  </AdminLayout>
</template>