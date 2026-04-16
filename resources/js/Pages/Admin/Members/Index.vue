<script setup>
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

const search = ref('')

const members = ref([
  {
    id: 1,
    name: 'Olivia Carter',
    email: 'olivia@example.com',
    plan: 'Pro Monthly',
    status: 'Active',
    joinedAt: '2026-04-15',
  },
  {
    id: 2,
    name: 'Noah Bennett',
    email: 'noah@example.com',
    plan: 'Starter',
    status: 'Trial',
    joinedAt: '2026-04-14',
  },
  {
    id: 3,
    name: 'Emma Brooks',
    email: 'emma@example.com',
    plan: 'Pro Yearly',
    status: 'Active',
    joinedAt: '2026-04-13',
  },
  {
    id: 4,
    name: 'Liam Parker',
    email: 'liam@example.com',
    plan: 'Starter',
    status: 'Canceled',
    joinedAt: '2026-04-10',
  },
  {
    id: 5,
    name: 'Sophia Reed',
    email: 'sophia@example.com',
    plan: 'Growth',
    status: 'Active',
    joinedAt: '2026-04-08',
  },
])

const filteredMembers = computed(() => {
  const query = search.value.trim().toLowerCase()

  if (!query) {
    return members.value
  }

  return members.value.filter((member) => {
    return [
      member.name,
      member.email,
      member.plan,
      member.status,
    ].some((value) => value.toLowerCase().includes(query))
  })
})

const pageTitle = computed(() => 'Members')
const pageDescription = computed(() => 'View, search, and manage the people subscribed to your SaaS platform.')
</script>

<template>
  <Head :title="pageTitle" />

  <div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/60">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-lg font-semibold text-slate-950">
            Member management
          </h2>

          <p class="mt-1 text-sm text-slate-500">
            Search your member base and prepare this page for real API pagination later.
          </p>
        </div>

        <div class="w-full lg:max-w-sm">
          <label class="sr-only" for="member-search">
            Search members
          </label>

          <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </span>

            <input
                id="member-search"
                v-model="search"
                type="text"
                placeholder="Search by name, email, plan..."
                class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:ring-4 focus:ring-slate-200/60"
            >
          </div>
        </div>
      </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/60">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50/80">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
              Name
            </th>
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
              Email
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
            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
              Actions
            </th>
          </tr>
          </thead>

          <tbody class="divide-y divide-slate-200 bg-white">
          <tr
              v-for="member in filteredMembers"
              :key="member.id"
              class="transition hover:bg-slate-50/70"
          >
            <td class="px-6 py-4 text-sm font-medium text-slate-900">
              {{ member.name }}
            </td>

            <td class="px-6 py-4 text-sm text-slate-600">
              {{ member.email }}
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

            <td class="px-6 py-4 text-right">
              <button
                  type="button"
                  class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900"
              >
                View
              </button>
            </td>
          </tr>

          <tr v-if="filteredMembers.length === 0">
            <td
                colspan="6"
                class="px-6 py-14 text-center"
            >
              <div class="mx-auto max-w-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                  <svg
                      class="h-6 w-6"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                  >
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                  </svg>
                </div>

                <h3 class="mt-4 text-base font-semibold text-slate-900">
                  No members found
                </h3>

                <p class="mt-2 text-sm text-slate-500">
                  Try a different search term or clear the current filter.
                </p>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>