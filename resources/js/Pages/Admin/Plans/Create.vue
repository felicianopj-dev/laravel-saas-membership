<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PlanForm from './Partials/PlanForm.vue'

defineOptions({
  layout: AdminLayout,
})

defineProps<{
  defaults: {
    currency: string
  }
}>()
</script>

<template>
  <div class="space-y-6">
    <div>
      <Link href="/admin/plans" class="text-sm font-medium text-gray-500 hover:text-gray-900">
        ← Back to plans
      </Link>

      <h1 class="mt-3 text-2xl font-semibold text-gray-900">
        Create plan
      </h1>
    </div>

    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
      <PlanForm
          :plan="{ currency: defaults.currency }"
          submit-url="/admin/plans"
          method="post"
          submit-label="Create plan"
      />
    </div>
  </div>
</template>