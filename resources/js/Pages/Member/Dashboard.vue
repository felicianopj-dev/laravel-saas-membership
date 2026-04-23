<script setup>
import { Link } from '@inertiajs/vue3';
import MemberLayout from '@/Layouts/MemberLayout.vue';

defineProps({
  member: {
    type: Object,
    required: true,
  },
});

defineOptions({
  layout: (h, page) => h(MemberLayout, { title: 'Dashboard' }, () => page),
});
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-sm font-medium text-slate-500">
            Welcome back
          </p>

          <h2 class="mt-1 text-2xl font-bold text-slate-900">
            {{ member.name }}
          </h2>

          <p class="mt-2 text-sm text-slate-600">
            This is the basic member dashboard. You can expand this later with subscription details, billing history, and profile editing.
          </p>
        </div>

        <div>
          <Link
              href="/member/profile"
              class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
          >
            View profile
          </Link>
        </div>
      </div>
    </section>

    <section class="grid gap-6 md:grid-cols-3">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">
          Email
        </p>

        <p class="mt-2 text-lg font-semibold text-slate-900">
          {{ member.email }}
        </p>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">
          Membership status
        </p>

        <p class="mt-2 text-lg font-semibold text-emerald-600">
          {{ member.membership_status }}
        </p>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">
          Joined at
        </p>

        <p class="mt-2 text-lg font-semibold text-slate-900">
          {{ member.joined_at ?? '—' }}
        </p>
      </div>
    </section>

    <section class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6">
      <h3 class="text-lg font-semibold text-slate-900">
        Next steps
      </h3>

      <div class="mt-4 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
          <div class="text-sm font-semibold text-slate-900">
            Subscription details
          </div>

          <p class="mt-2 text-sm text-slate-600">
            Show current plan, renewal date, and billing information.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>