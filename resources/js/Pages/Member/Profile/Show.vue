<script setup>
import MemberLayout from '@/Layouts/MemberLayout.vue';

defineProps({
  member: {
    type: Object,
    required: true,
  },
});

defineOptions({
  layout: (h, page) => h(MemberLayout, { title: 'My Profile' }, () => page),
});
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="text-sm font-medium text-slate-500">
            Account overview
          </p>

          <h2 class="mt-1 text-2xl font-bold text-slate-900">
            {{ member.name }}
          </h2>

          <p class="mt-2 text-sm text-slate-600">
            This page shows the logged-in member information. Later you can turn this into an edit page.
          </p>
        </div>

        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                    {{ member.membership_status }}
                </span>
      </div>
    </section>

    <section class="grid gap-6 md:grid-cols-2">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-900">
          Personal details
        </h3>

        <dl class="mt-6 space-y-4">
          <div>
            <dt class="text-sm font-medium text-slate-500">
              Name
            </dt>
            <dd class="mt-1 text-base text-slate-900">
              {{ member.name }}
            </dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-slate-500">
              Email
            </dt>
            <dd class="mt-1 text-base text-slate-900">
              {{ member.email }}
            </dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-slate-500">
              Joined at
            </dt>
            <dd class="mt-1 text-base text-slate-900">
              {{ member.joined_at ?? '—' }}
            </dd>
          </div>
        </dl>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-900">
          Membership
        </h3>

        <dl class="mt-6 space-y-4">
          <div>
            <dt class="text-sm font-medium text-slate-500">
              Status
            </dt>
            <dd class="mt-1 text-base font-semibold text-emerald-600">
              {{ member.membership_status }}
            </dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-slate-500">
              Current area
            </dt>
            <dd class="mt-1 text-base text-slate-900">
              Member self-service portal
            </dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-slate-500">
              Next feature
            </dt>
            <dd class="mt-1 text-base text-slate-900">
              Profile edit form
            </dd>
          </div>
        </dl>
      </div>
    </section>
  </div>
</template>