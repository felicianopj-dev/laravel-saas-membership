<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

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

  router.delete(`/admin/plans/${plan.id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Manage Plans" />

  <AdminLayout title="Manage Plans">
    <div class="space-y-6">
      <section class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">
            Plans
          </h1>

          <p class="mt-1 text-sm text-slate-500">
            Manage membership plans and Stripe price references.
          </p>
        </div>

        <Link
            href="/admin/plans/create"
            class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
        >
          New plan
        </Link>
      </section>

      <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Plan
              </th>

              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Price
              </th>

              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Stripe
              </th>

              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Status
              </th>

              <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                Actions
              </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 bg-white">
            <tr
                v-for="plan in plans"
                :key="plan.id"
            >
              <td class="px-6 py-4">
                <div class="font-semibold text-slate-900">
                  {{ plan.name }}
                </div>

                <div class="mt-1 text-sm text-slate-500">
                  {{ plan.slug }}
                </div>
              </td>

              <td class="px-6 py-4">
                <div class="font-semibold text-slate-900">
                  {{ plan.formatted_price }}
                </div>

                <div class="mt-1 text-sm text-slate-500">
                  {{ plan.billing_interval }}
                </div>
              </td>

              <td class="px-6 py-4 text-sm text-slate-500">
                <div>
                  Product: {{ plan.stripe_product_id || '—' }}
                </div>

                <div class="mt-1">
                  Price: {{ plan.stripe_price_id || '—' }}
                </div>
              </td>

              <td class="px-6 py-4">
                <span
                    class="rounded-full px-3 py-1 text-xs font-semibold"
                    :class="plan.is_active
                    ? 'bg-emerald-100 text-emerald-700'
                    : 'bg-slate-100 text-slate-600'"
                >
                  {{ plan.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>

              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-3">
                  <Link
                      :href="`/admin/plans/${plan.id}/edit`"
                      class="text-sm font-medium text-slate-600 hover:text-slate-900"
                  >
                    Edit
                  </Link>

                  <button
                      type="button"
                      class="text-sm font-medium text-rose-600 hover:text-rose-700"
                      @click="deletePlan(plan)"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="!plans.length">
              <td
                  colspan="5"
                  class="px-6 py-10 text-center text-sm text-slate-500"
              >
                No plans found.
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </AdminLayout>
</template>