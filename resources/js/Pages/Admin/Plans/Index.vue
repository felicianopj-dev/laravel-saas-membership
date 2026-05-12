<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

type Plan = {
  id: number
  name: string
  slug: string
  description: string | null
  formatted_price: string
  currency: string
  billing_interval: string
  stripe_product_id: string | null
  stripe_price_id: string | null
  is_active: boolean
  sort_order: number
}

defineProps<{
  plans: Plan[]
}>()

const deletePlan = (plan: Plan) => {
  if (!confirm(`Delete plan "${plan.name}"?`)) {
    return
  }

  router.delete(`/admin/plans/${plan.id}`)
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">
          Plans
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Manage membership plans and Stripe price references.
        </p>
      </div>

      <Link
          href="/admin/plans/create"
          class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
      >
        New plan
      </Link>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
            Plan
          </th>

          <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
            Price
          </th>

          <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
            Stripe
          </th>

          <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
            Status
          </th>

          <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
            Actions
          </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
        <tr v-for="plan in plans" :key="plan.id">
          <td class="px-6 py-4">
            <div class="font-medium text-gray-900">
              {{ plan.name }}
            </div>

            <div class="text-sm text-gray-500">
              {{ plan.slug }}
            </div>
          </td>

          <td class="px-6 py-4 text-sm text-gray-700">
            {{ plan.formatted_price }} / {{ plan.billing_interval }}
          </td>

          <td class="px-6 py-4 text-sm text-gray-500">
            <div>
              Product: {{ plan.stripe_product_id || '—' }}
            </div>

            <div>
              Price: {{ plan.stripe_price_id || '—' }}
            </div>
          </td>

          <td class="px-6 py-4">
                            <span
                                class="rounded-full px-3 py-1 text-xs font-medium"
                                :class="plan.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                            >
                                {{ plan.is_active ? 'Active' : 'Inactive' }}
                            </span>
          </td>

          <td class="px-6 py-4 text-right text-sm">
            <Link
                :href="`/admin/plans/${plan.id}/edit`"
                class="font-medium text-gray-900 hover:text-gray-700"
            >
              Edit
            </Link>

            <button
                type="button"
                class="ml-4 font-medium text-red-600 hover:text-red-700"
                @click="deletePlan(plan)"
            >
              Delete
            </button>
          </td>
        </tr>

        <tr v-if="plans.length === 0">
          <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
            No plans found.
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>