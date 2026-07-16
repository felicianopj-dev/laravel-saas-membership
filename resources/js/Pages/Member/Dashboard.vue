<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import AdminLayout from "@/Layouts/AdminLayout.vue";

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

const formatPrice = (price) => {
  if (price === null || price === undefined) return '—'

  return `$ ${(price / 100).toFixed(2)}`
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

const accessBadge = (hasAccess) => {
  return hasAccess
      ? 'bg-emerald-100 text-emerald-700'
      : 'bg-rose-100 text-rose-700'
}

const cancelSubscription = () => {
  if (! window.confirm('Are you sure you want to cancel your subscription?')) {
    return
  }

  router.post('/member/subscription/cancel')
}

const resumeSubscription = () => {
  router.post('/member/subscription/resume')
}
</script>

<template>
  <Head title="Dashboard" />

  <MemberLayout title="Dashboard">
    <div class="space-y-6">
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

          <div class="flex flex-wrap gap-3">
            <Link
                href="/member/plans"
                class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
            >
              Manage plan
            </Link>

            <Link
                href="/member/profile"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
              View profile
            </Link>
          </div>
        </div>
      </section>

      <section class="grid gap-6 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <p class="text-sm font-medium text-slate-500">Email</p>
          <p class="mt-2 break-all text-lg font-semibold text-slate-900">
            {{ summary.member_email }}
          </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <p class="text-sm font-medium text-slate-500">Account Status</p>
          <p class="mt-2 text-lg font-semibold capitalize text-slate-900">
            {{ summary.account_status }}
          </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <p class="text-sm font-medium text-slate-500">Plan</p>
          <p class="mt-2 text-lg font-semibold text-slate-900">
            {{ subscription?.plan_name ?? 'No plan' }}
          </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <p class="text-sm font-medium text-slate-500">Access</p>

          <div class="mt-2">
            <span
                v-if="subscription"
                class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                :class="accessBadge(subscription.has_access)"
            >
              {{ subscription.has_access ? 'Enabled' : 'Blocked' }}
            </span>

            <span
                v-else
                class="text-sm font-medium text-slate-500"
            >
              Not subscribed
            </span>
          </div>

          <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-200 pt-6">
            <button
                v-if="subscription.can_cancel"
                type="button"
                class="inline-flex items-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700"
                @click="cancelSubscription"
            >
              Cancel subscription
            </button>

            <button
                v-if="subscription.is_canceled && subscription.has_access"
                type="button"
                class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
                @click="resumeSubscription"
            >
              Resume subscription
            </button>
          </div>
        </div>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
          <div>
            <h3 class="text-lg font-semibold text-slate-900">
              Subscription Overview
            </h3>

            <p class="mt-1 text-sm text-slate-500">
              Review your current plan, billing period, and access status.
            </p>
          </div>

          <Link
              href="/member/plans"
              class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
          >
            Change plan
          </Link>
        </div>

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
            <p class="mt-1 text-sm font-semibold capitalize text-slate-900">
              {{ subscription.billing_interval }}
            </p>
          </div>

          <div>
            <p class="text-sm text-slate-500">Status</p>

            <span
                class="mt-1 inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                :class="statusBadge(subscription.status)"
            >
              {{ subscription.status }}
            </span>
          </div>

          <div>
            <p class="text-sm text-slate-500">Access</p>

            <span
                class="mt-1 inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                :class="accessBadge(subscription.has_access)"
            >
              {{ subscription.has_access ? 'Enabled' : 'Blocked' }}
            </span>
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

        <div
            v-else
            class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6"
        >
          <p class="text-sm font-medium text-slate-700">
            You don’t have a subscription yet.
          </p>

          <p class="mt-1 text-sm text-slate-500">
            Choose a plan to unlock member features.
          </p>

          <Link
              href="/member/plans"
              class="mt-4 inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
          >
            View plans
          </Link>
        </div>
      </section>
    </div>
  </MemberLayout>
</template>