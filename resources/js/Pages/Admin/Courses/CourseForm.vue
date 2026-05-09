<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
  form: any
  plans: Array<{
    id: number
    name: string
    slug: string
  }>
  submitLabel: string
}>()

defineEmits<{
  submit: []
}>()
</script>

<template>
  <form
      class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
      @submit.prevent="$emit('submit')"
  >
    <div>
      <label class="mb-2 block text-sm font-medium text-slate-700">
        Title
      </label>

      <input
          v-model="form.title"
          type="text"
          class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
      >

      <p
          v-if="form.errors.title"
          class="mt-2 text-sm text-rose-600"
      >
        {{ form.errors.title }}
      </p>
    </div>

    <div>
      <label class="mb-2 block text-sm font-medium text-slate-700">
        Description
      </label>

      <textarea
          v-model="form.description"
          rows="5"
          class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
      />

      <p
          v-if="form.errors.description"
          class="mt-2 text-sm text-rose-600"
      >
        {{ form.errors.description }}
      </p>
    </div>

    <div>
      <label class="mb-3 block text-sm font-medium text-slate-700">
        Plans
      </label>

      <div class="grid gap-3 md:grid-cols-3">
        <label
            v-for="plan in plans"
            :key="plan.id"
            class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 text-sm font-medium text-slate-700"
        >
          <input
              v-model="form.plan_ids"
              type="checkbox"
              :value="plan.id"
              class="rounded border-slate-300"
          >

          <span>{{ plan.name }}</span>
        </label>
      </div>

      <p
          v-if="form.errors.plan_ids"
          class="mt-2 text-sm text-rose-600"
      >
        {{ form.errors.plan_ids }}
      </p>
    </div>

    <div>
      <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
        <input
            v-model="form.is_published"
            type="checkbox"
            class="rounded border-slate-300"
        >

        Published
      </label>

      <p
          v-if="form.errors.is_published"
          class="mt-2 text-sm text-rose-600"
      >
        {{ form.errors.is_published }}
      </p>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
      <Link
          href="/admin/courses"
          class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
      >
        Cancel
      </Link>

      <button
          type="submit"
          :disabled="form.processing"
          class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
      >
        {{ submitLabel }}
      </button>
    </div>
  </form>
</template>