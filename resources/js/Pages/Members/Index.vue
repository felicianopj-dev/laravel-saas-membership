<script setup>
defineProps({
  members: {
    type: Object,
    required: true,
  },
});

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
          Members
        </h1>

        <p class="mt-2 text-slate-600">
          Member listing powered by Inertia and Laravel.
        </p>
      </div>

      <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Phone</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Created</th>
            </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 bg-white">
            <tr v-for="member in members.data" :key="member.id">
              <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ member.name }}</td>
              <td class="px-6 py-4 text-sm text-slate-600">{{ member.email }}</td>
              <td class="px-6 py-4 text-sm text-slate-600">{{ member.phone ?? '—' }}</td>
              <td class="px-6 py-4 text-sm text-slate-600">{{ member.status }}</td>
              <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(member.created_at) }}</td>
            </tr>
            </tbody>
          </table>
        </div>

        <div class="flex items-center justify-between border-t border-slate-200 px-6 py-4 text-sm text-slate-600">
                    <span>
                        Page {{ members.current_page }} of {{ members.last_page }}
                    </span>

          <span>
                        Total: {{ members.total }}
                    </span>
        </div>
      </section>
    </div>
  </main>
</template>