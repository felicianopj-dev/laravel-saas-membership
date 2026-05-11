<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

defineProps<{
  subscriptions: {
    data: Array<{
      id: number
      status: string
      starts_at: string | null
      ends_at: string | null
      trial_ends_at: string | null
      created_at: string | null
      user: null | {
        id: number
        name: string
        email: string
      }
      plan: null | {
        id: number
        name: string
        slug: string
        price: number
        billing_interval: string
      }
    }>
    links: Array<{
      url: string | null
      label: string
      active: boolean
    }>
  }
}>()

const formatDate = (value: string | null) => {
  if (!value) {
    return '—'
  }

  return new Date(value).toLocaleDateString()
}

const formatPrice = (value: number | null | undefined) => {
  if (value === null || value === undefined) {
    return '—'
  }

  return `R$ ${(value / 100).toFixed(2)}`
}

const statusClass = (status: string) => {
  switch (status) {
    case 'active':
      return 'bg-emerald-100 text-emerald-700'
    case 'trialing':
      return 'bg-amber-100 text-amber-700'
    case 'canceled':
      return 'bg-rose-100 text-rose-700'
    default:
      return 'bg-slate-100 text-slate-600'
  }
}
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h1 class="text-2xl font-bold text-slate-900">
        Subscriptions
      </h1>

      <p class="mt-1 text-sm text-slate-500">
        Review member subscriptions, billing plans, and access status.
      </p>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              Member
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              Plan
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              Status
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              Started
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              Ends
            </th>
            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
              Actions
            </th>
          </tr>
          </thead>

          <tbody class="divide-y divide-slate-200 bg-white">
          <tr
              v-for="subscription in subscriptions.data"
              :key="subscription.id"
          >
            <td class="px-6 py-4">
              <div v-if="subscription.user">
                <div class="font-semibold text-slate-900">
                  {{ subscription.user.name }}
                </div>

                <div class="mt-1 text-sm text-slate-500">
                  {{ subscription.user.email }}
                </div>
              </div>

              <span
                  v-else
                  class="text-sm text-slate-400"
              >
                  Deleted user
                </span>
            </td>

            <td class="px-6 py-4">
              <div v-if="subscription.plan">
                <div class="font-semibold text-slate-900">
                  {{ subscription.plan.name }}
                </div>

                <div class="mt-1 text-sm text-slate-500">
                  {{ formatPrice(subscription.plan.price) }} / {{ subscription.plan.billing_interval }}
                </div>
              </div>

              <span
                  v-else
                  class="text-sm text-slate-400"
              >
                  No plan
                </span>
            </td>

            <td class="px-6 py-4">
                <span
                    class="rounded-full px-3 py-1 text-xs font-semibold"
                    :class="statusClass(subscription.status)"
                >
                  {{ subscription.status }}
                </span>
            </td>

            <td class="px-6 py-4 text-sm text-slate-600">
              {{ formatDate(subscription.starts_at) }}
            </td>

            <td class="px-6 py-4 text-sm text-slate-600">
              {{ formatDate(subscription.ends_at) }}
            </td>

            <td class="px-6 py-4 text-right">
              <Link
                  v-if="subscription.user"
                  :href="`/admin/users/${subscription.user.id}/edit`"
                  class="text-sm font-medium text-slate-600 hover:text-slate-900"
              >
                View user
              </Link>
            </td>
          </tr>

          <tr v-if="!subscriptions.data.length">
            <td
                colspan="6"
                class="px-6 py-10 text-center text-sm text-slate-500"
            >
              No subscriptions found.
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>