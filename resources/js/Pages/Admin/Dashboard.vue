<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  stats: {
    type: Array,
    required: true,
  },
  recentMembers: {
    type: Array,
    required: true,
  },
  growthSummary: {
    type: Array,
    required: true,
  },
  nextStep: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <Head :title="title" />

  <div class="space-y-6">
    <!-- Stats -->
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
      <!-- Recent Members -->
      <section class="rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/60">
        <div class="border-b border-slate-200 px-6 py-5">
          <h2 class="text-lg font-semibold text-slate-950">
            Recent members
          </h2>

          <p class="mt-1 text-sm text-slate-500">
            Latest registrations and subscription activity.
          </p>
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

      <!-- Right column -->
      <section class="space-y-6">
        <!-- Growth summary -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/60">
          <h2 class="text-lg font-semibold text-slate-950">
            Growth summary
          </h2>

          <p class="mt-1 text-sm text-slate-500">
            A quick snapshot of business momentum.
          </p>

          <div class="mt-6 space-y-5">
            <div
                v-for="item in growthSummary"
                :key="item.label"
            >
              <div class="mb-2 flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-700">
                                    {{ item.label }}
                                </span>

                <span class="text-slate-500">
                                    {{ item.value }}%
                                </span>
              </div>

              <div class="h-3 rounded-full bg-slate-100">
                <div
                    class="h-3 rounded-full"
                    :class="{
                                        'bg-slate-900': item.color === 'slate',
                                        'bg-indigo-600': item.color === 'indigo',
                                        'bg-emerald-500': item.color === 'emerald',
                                    }"
                    :style="{ width: `${item.value}%` }"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Next step card -->
        <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 p-6 text-white shadow-xl shadow-slate-300/40">
          <p class="text-sm font-medium text-slate-300">
            {{ nextStep.eyebrow }}
          </p>

          <h3 class="mt-2 text-2xl font-semibold tracking-tight">
            {{ nextStep.title }}
          </h3>

          <p class="mt-3 text-sm leading-6 text-slate-300">
            {{ nextStep.description }}
          </p>

          <div class="mt-6 inline-flex rounded-2xl bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur">
            {{ nextStep.badge }}
          </div>
        </div>
      </section>
    </div>
  </div>
</template>