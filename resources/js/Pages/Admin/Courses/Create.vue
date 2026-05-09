<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import CourseForm from './CourseForm.vue'

defineOptions({
  layout: AdminLayout,
})

defineProps<{
  plans: Array<{
    id: number
    name: string
    slug: string
  }>
}>()

const form = useForm({
  title: '',
  description: '',
  is_published: true,
  plan_ids: [],
})

const submit = () => {
  form.post('/admin/courses')
}
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h1 class="text-2xl font-bold text-slate-900">
        Create course
      </h1>

      <p class="mt-1 text-sm text-slate-500">
        Add a new course and define which plans can access it.
      </p>
    </section>

    <CourseForm
        :form="form"
        :plans="plans"
        submit-label="Create course"
        @submit="submit"
    />
  </div>
</template>