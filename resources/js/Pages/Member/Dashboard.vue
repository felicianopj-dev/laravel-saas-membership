<script setup>
import { Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

defineProps({
  summary: {
    type: Object,
    required: true,
  },
  subscription: {
    type: Object,
    default: null,
  },
})

defineOptions({
  layout: (h, page) => h(MemberLayout, { title: 'Dashboard' }, () => page),
})

const formatPrice = (price) => {
  if (price === null || price === undefined) return '—'
  return `R$ ${(price / 100).toFixed(2)}`
}

const statusBadge = (status) => {
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
    <div
        v-if="$page.props.flash?.success"
        class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
    >
      {{ $page.props.flash.success }}
    </div>

    <div
        v-if="$page.props.flash?.error"
        class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700"
    >
      {{ $page.props.flash.error }}
    </div>
    
    <!-- Header -->
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-sm font-medium text-slate-500">
            Welcome back
          </p>

          <h2 class="mt-1 text-2xl font-bold text-slate-900">
            {{ summary.member_name }}
          </h2>
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

    <!-- Summary Cards -->
    <section class="grid gap-6 md:grid-cols-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Email</p>
        <p class="mt-2 text-lg font-semibold text-slate-900 break-all">
          {{ summary.member_email }}
        </p>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Account Status</p>
        <p class="mt-2 text-lg font-semibold text-slate-900">
          {{ summary.account_status }}
        </p>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Plan</p>
        <p class="mt-2 text-lg font-semibold text-slate-900">
          {{ subscription?.plan_name ?? 'No active plan' }}
        </p>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Subscription</p>

        <div class="mt-2">
                    <span
                        v-if="subscription"
                        class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                        :class="statusBadge(subscription.status)"
                    >
                        {{ subscription.status }}
                    </span>

          <span
              v-else
              class="text-sm font-medium text-slate-500"
          >
                        Not subscribed
                    </span>
        </div>
      </div>
    </section>

    <!-- Subscription Details -->
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h3 class="text-lg font-semibold text-slate-900">
        Subscription Overview
      </h3>

      <div v-if="subscription" class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div>
          <p class="text-sm text-slate-500">Plan</p>
          <p class="mt-1 text-sm font-semibold text-slate-900">
            {{ subscription.plan_name }}
          </p>
        </div>

        <div>
          <p class="text-sm text-slate-500">Price</p>
          <p class="mt-1 text-sm font-semibold text-slate-900">
            {{ formatPrice(subscription.price) }}
          </p>
        </div>

        <div>
          <p class="text-sm text-slate-500">Billing</p>
          <p class="mt-1 text-sm font-semibold text-slate-900">
            {{ subscription.billing_interval }}
          </p>
        </div>

        <div>
          <p class="text-sm text-slate-500">Status</p>
          <p class="mt-1 text-sm font-semibold text-slate-900">
            {{ subscription.status }}
          </p>
        </div>

        <div>
          <p class="text-sm text-slate-500">Starts at</p>
          <p class="mt-1 text-sm font-semibold text-slate-900">
            {{ subscription.starts_at ?? '—' }}
          </p>
        </div>

        <div>
          <p class="text-sm text-slate-500">Ends at</p>
          <p class="mt-1 text-sm font-semibold text-slate-900">
            {{ subscription.ends_at ?? '—' }}
          </p>
        </div>

        <div>
          <p class="text-sm text-slate-500">Trial ends</p>
          <p class="mt-1 text-sm font-semibold text-slate-900">
            {{ subscription.trial_ends_at ?? '—' }}
          </p>
        </div>
      </div>

      <div v-else class="mt-6 text-sm text-slate-600">
        You don’t have an active subscription yet.
      </div>
    </section>
  </div>
</template>