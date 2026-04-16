<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const stats = [
  {
    label: 'Total members',
    value: '1,284',
    change: '+12.4%',
    description: 'Compared to last month',
  },
  {
    label: 'Active subscriptions',
    value: '932',
    change: '+8.1%',
    description: 'Recurring paid members',
  },
  {
    label: 'Monthly revenue',
    value: '$18,420',
    change: '+14.9%',
    description: 'Estimated MRR',
  },
  {
    label: 'Churn rate',
    value: '2.3%',
    change: '-0.6%',
    description: 'Lower is better',
  },
]

const recentMembers = [
  {
    name: 'Olivia Carter',
    email: 'olivia@example.com',
    plan: 'Pro Monthly',
    status: 'Active',
    joinedAt: '2026-04-15',
  },
  {
    name: 'Noah Bennett',
    email: 'noah@example.com',
    plan: 'Starter',
    status: 'Trial',
    joinedAt: '2026-04-14',
  },
  {
    name: 'Emma Brooks',
    email: 'emma@example.com',
    plan: 'Pro Yearly',
    status: 'Active',
    joinedAt: '2026-04-13',
  },
  {
    name: 'Liam Parker',
    email: 'liam@example.com',
    plan: 'Starter',
    status: 'Canceled',
    joinedAt: '2026-04-10',
  },
]

const pageTitle = computed(() => 'Dashboard')
const pageDescription = computed(() => 'Overview of your SaaS performance, membership activity, and business health.')
</script>

<template>
  <Head :title="pageTitle" />

  <div class="space-y-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
      <div
          v-for="stat in stats"
          :key="stat.label"
          class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/60"
      >
        <div class="flex items-start justify-between gap-4">
          <div>
            <p class="text-sm font-medium text-slate-500">
              {{ stat.label }}
            </p>

            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">
              {{ stat.value }}
            </p>
          </div>

          <div class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
            {{ stat.change }}
          </div>
        </div>

        <p class="mt-4 text-sm text-slate-500">
          {{ stat.description }}
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 2xl:grid-cols-[1.4fr_0.8fr]">
      <section class="rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/60">
        <div class="border-b border-slate-200 px-6 py-5">
          <div class="flex items-center justify-between gap-4">
            <div>
              <h2 class="text-lg font-semibold text-slate-950">
                Recent members
              </h2>

              <p class="mt-1 text-sm text-slate-500">
                Latest registrations and subscription activity.
              </p>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50/80">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                Member
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                Plan
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                Status
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                Joined
              </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 bg-white">
            <tr
                v-for="member in recentMembers"
                :key="member.email"
                class="transition hover:bg-slate-50/70"
            >
              <td class="px-6 py-4">
                <div>
                  <p class="text-sm font-medium text-slate-900">
                    {{ member.name }}
                  </p>

                  <p class="mt-1 text-sm text-slate-500">
                    {{ member.email }}
                  </p>
                </div>
              </td>

              <td class="px-6 py-4 text-sm text-slate-700">
                {{ member.plan }}
              </td>

              <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700': member.status === 'Active',
                                            'bg-amber-50 text-amber-700': member.status === 'Trial',
                                            'bg-rose-50 text-rose-700': member.status === 'Canceled',
                                        }"
                                    >
                                        {{ member.status }}
                                    </span>
              </td>

              <td class="px-6 py-4 text-sm text-slate-500">
                {{ member.joinedAt }}
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </section>

      <section class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/60">
          <h2 class="text-lg font-semibold text-slate-950">
            Growth summary
          </h2>

          <p class="mt-1 text-sm text-slate-500">
            A quick snapshot of business momentum.
          </p>

          <div class="mt-6 space-y-5">
            <div>
              <div class="mb-2 flex items-center justify-between text-sm">
                <span class="font-medium text-slate-700">New members</span>
                <span class="text-slate-500">74%</span>
              </div>

              <div class="h-3 rounded-full bg-slate-100">
                <div class="h-3 w-[74%] rounded-full bg-slate-900" />
              </div>
            </div>

            <div>
              <div class="mb-2 flex items-center justify-between text-sm">
                <span class="font-medium text-slate-700">Retention</span>
                <span class="text-slate-500">88%</span>
              </div>

              <div class="h-3 rounded-full bg-slate-100">
                <div class="h-3 w-[88%] rounded-full bg-indigo-600" />
              </div>
            </div>

            <div>
              <div class="mb-2 flex items-center justify-between text-sm">
                <span class="font-medium text-slate-700">Upgrade rate</span>
                <span class="text-slate-500">41%</span>
              </div>

              <div class="h-3 rounded-full bg-slate-100">
                <div class="h-3 w-[41%] rounded-full bg-emerald-500" />
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 p-6 text-white shadow-xl shadow-slate-300/40">
          <p class="text-sm font-medium text-slate-300">
            Next step
          </p>

          <h3 class="mt-2 text-2xl font-semibold tracking-tight">
            Billing and subscription management
          </h3>

          <p class="mt-3 text-sm leading-6 text-slate-300">
            This layout is ready to receive subscription pages, plan management, invoices, and member detail screens.
          </p>

          <div class="mt-6 inline-flex rounded-2xl bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur">
            Portfolio-ready admin foundation
          </div>
        </div>
      </section>
    </div>
  </div>
</template>