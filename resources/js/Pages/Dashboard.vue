<script setup>
defineProps({
  stats: {
    type: Object,
    required: true,
  },
  recentMembers: {
    type: Array,
    required: true,
  },
});

const cards = [
  {
    label: 'Total Users',
    valueKey: 'users',
  },
  {
    label: 'Admins',
    valueKey: 'admins',
  },
  {
    label: 'Members',
    valueKey: 'members',
  },
  {
    label: 'Active Subscriptions',
    valueKey: 'activeSubscriptions',
  },
  {
    label: 'Plans',
    valueKey: 'plans',
  },
];

const formatDate = (value) => {
  return new Date(value).toLocaleDateString('en-US');
};
</script>

<template>
  <main class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-6 py-10">
      <div class="mb-8">
        <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">
          Membership Admin
        </p>

        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
          Dashboard
        </h1>

        <p class="mt-2 text-slate-600">
          Overview of users, plans, and subscriptions.
        </p>
      </div>

      <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <article
            v-for="card in cards"
            :key="card.valueKey"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
        >
          <p class="text-sm text-slate-500">
            {{ card.label }}
          </p>

          <p class="mt-3 text-3xl font-semibold text-slate-900">
            {{ stats[card.valueKey] }}
          </p>
        </article>
      </section>

      <section class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
          <h2 class="text-lg font-semibold text-slate-900">
            Recent Members
          </h2>
        </div>

        <div v-if="recentMembers.length" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                Email
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                Created
              </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 bg-white">
            <tr v-for="member in recentMembers" :key="member.id">
              <td class="px-6 py-4 text-sm font-medium text-slate-900">
                {{ member.name }}
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">
                {{ member.email }}
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">
                {{ member.status }}
              </td>
              <td class="px-6 py-4 text-sm text-slate-600">
                {{ formatDate(member.created_at) }}
              </td>
            </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="px-6 py-8 text-sm text-slate-500">
          No members found.
        </div>
      </section>
    </div>
  </main>
</template>