<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

defineProps({
  plans: {
    type: Array,
    required: true,
  },
  current_plan_id: {
    type: Number,
    default: null,
  },
})

const processingPlanId = ref(null)

const formatPrice = (price) => {
  if (price === 0) return 'Free'

  return `$ ${(price / 100).toFixed(2)}`
}

const subscribe = (plan) => {
  router.post(`/member/plans/${plan.id}/subscribe`, {}, {
    onFinish: () => {
      processingPlanId.value = null
    },
  })
}

const resumeSubscription = (plan) => {
  router.post('/member/subscription/resume', {}, {
    onFinish: () => {
      processingPlanId.value = null
    },
  })
}

const planBadgeLabel = (plan) => {
  if (plan.is_current_active) {
    return 'Current'
  }

  if (plan.is_current_canceled) {
    return 'Canceled'
  }

  return null
}

const planBadgeClass = (plan) => {
  if (plan.is_current_active) {
    return 'bg-emerald-100 text-emerald-700'
  }

  if (plan.is_current_canceled) {
    return 'bg-amber-100 text-amber-700'
  }

  return 'bg-slate-100 text-slate-600'
}

const isProcessingPlan = (plan) => {
  return processingPlanId.value === plan.id
}

const hasProcessingPlan = () => {
  return processingPlanId.value !== null
}

const planButtonLabel = (plan) => {
  if (isProcessingPlan(plan)) {
    return 'Loading...'
  }

  if (plan.is_current_active) {
    return 'Current Plan'
  }

  if (plan.is_current_canceled) {
    return 'Resume Plan'
  }

  return 'Subscribe'
}

const planButtonClass = (plan) => {
  if (plan.is_current_active) {
    return 'cursor-not-allowed bg-emerald-100 text-emerald-700'
  }

  if (isProcessingPlan(plan)) {
    return 'cursor-wait bg-slate-300 text-slate-600'
  }

  if (hasProcessingPlan()) {
    return plan.is_current_canceled
        ? 'bg-emerald-600 text-white'
        : 'bg-slate-900 text-white'
  }

  if (plan.is_current_canceled) {
    return 'bg-emerald-600 text-white hover:bg-emerald-700'
  }

  return 'bg-slate-900 text-white hover:bg-slate-800'
}

const handlePlanAction = (plan) => {
  if (plan.is_current_active || hasProcessingPlan()) {
    return
  }

  processingPlanId.value = plan.id

  if (plan.is_current_canceled) {
    resumeSubscription(plan)

    return
  }

  subscribe(plan)
}
</script>

<template>
  <Head title="My Plans" />

  <MemberLayout title="My Plans">
    <div class="space-y-6">
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
          <div>
            <p class="text-sm font-medium text-slate-500">
              Membership
            </p>

            <h2 class="mt-1 text-2xl font-bold text-slate-900">
              Choose your plan
            </h2>

            <p class="mt-2 max-w-2xl text-sm text-slate-600">
              Select the membership plan that best fits your needs.
            </p>
          </div>

          <a
              href="/member/billing/portal"
              class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
          >
            Manage Billing
          </a>
        </div>
      </section>

      <section class="grid gap-6 md:grid-cols-3">
        <div
            v-for="plan in plans"
            :key="plan.id"
            class="flex flex-col rounded-2xl border bg-white p-6 shadow-sm"
            :class="plan.is_current ? 'border-emerald-300' : 'border-slate-200'"
        >
          <div class="flex-1">
            <div class="flex items-start justify-between gap-4">
              <h3 class="text-lg font-semibold text-slate-900">
                {{ plan.name }}
              </h3>

              <span
                  v-if="planBadgeLabel(plan)"
                  class="rounded-full px-3 py-1 text-xs font-semibold"
                  :class="planBadgeClass(plan)"
              >
                {{ planBadgeLabel(plan) }}
              </span>
            </div>

            <div class="mt-4">
              <span class="text-3xl font-bold text-slate-900">
                {{ formatPrice(plan.price) }}
              </span>

              <span
                  v-if="plan.price > 0"
                  class="text-sm text-slate-500"
              >
                / {{ plan.billing_interval }}
              </span>
            </div>

            <div
                v-if="plan.is_current_canceled"
                class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800"
            >
              This plan is canceled, but access remains enabled until
              {{ plan.current_subscription_ends_at ?? 'the end of the current billing period' }}.
            </div>

            <ul class="mt-6 space-y-3 text-sm text-slate-600">
              <li>Access to the member dashboard</li>
            </ul>
          </div>

          <button
              type="button"
              :disabled="plan.is_current_active || hasProcessingPlan()"
              class="mt-6 inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold transition disabled:pointer-events-none"
              :class="planButtonClass(plan)"
              @click="handlePlanAction(plan)"
          >
            <svg
                v-if="isProcessingPlan(plan)"
                class="mr-2 h-4 w-4 animate-spin"
                viewBox="0 0 24 24"
                fill="none"
            >
              <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
              />
              <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
              />
            </svg>

            {{ planButtonLabel(plan) }}
          </button>
        </div>
      </section>

      <section
          v-if="plans.length === 0"
          class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-600"
      >
        No active plans are available right now.
      </section>
    </div>
  </MemberLayout>
</template>