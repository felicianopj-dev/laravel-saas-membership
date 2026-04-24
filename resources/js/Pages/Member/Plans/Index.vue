<script setup>
import MemberLayout from '@/Layouts/MemberLayout.vue'

defineProps({
  plans: {
    type: Array,
    required: true,
  },
})

defineOptions({
  layout: (h, page) => h(MemberLayout, { title: 'Plans' }, () => page),
})

const formatPrice = (price) => {
  if (price === 0) return 'Free'

  return `$ ${(price / 100).toFixed(2)}`
}
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <p class="text-sm font-medium text-slate-500">
        Membership
      </p>

      <h2 class="mt-1 text-2xl font-bold text-slate-900">
        Choose your plan
      </h2>

      <p class="mt-2 max-w-2xl text-sm text-slate-600">
        Select the membership plan that best fits your needs. Payment integration will be added later.
      </p>
    </section>

    <section class="grid gap-6 md:grid-cols-3">
      <div
          v-for="plan in plans"
          :key="plan.id"
          class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
      >
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-slate-900">
            {{ plan.name }}
          </h3>

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

          <ul class="mt-6 space-y-3 text-sm text-slate-600">
            <li>- Features soon</li>
          </ul>
        </div>

        <button
            type="button"
            disabled
            class="mt-6 inline-flex items-center justify-center rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-500"
        >
          Subscribe soon
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
</template>