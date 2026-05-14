<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PlanForm from './Partials/PlanForm.vue'

type Plan = {
  id: number
  name: string
  slug: string
  description: string | null
  price: number
  currency: string
  billing_interval: string
  stripe_product_id: string | null
  stripe_price_id: string | null
  is_active: boolean
  sort_order: number
}

defineProps<{
  plan: Plan
}>()
</script>

<template>
  <Head title="Edit Plan" />

    <AdminLayout title="Edit Plan">
    <div class="space-y-6">
      <div>
        <Link href="/admin/plans" class="text-sm font-medium text-gray-500 hover:text-gray-900">
          ← Back to plans
        </Link>

        <h1 class="mt-3 text-2xl font-semibold text-gray-900">
          Edit plan
        </h1>
      </div>

      <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
        <PlanForm
            :plan="{
                      name: plan.name,
                      slug: plan.slug,
                      description: plan.description ?? '',
                      price: plan.price,
                      currency: plan.currency,
                      billing_interval: plan.billing_interval,
                      stripe_product_id: plan.stripe_product_id ?? '',
                      stripe_price_id: plan.stripe_price_id ?? '',
                      is_active: plan.is_active,
                      sort_order: plan.sort_order,
                  }"
            :submit-url="`/admin/plans/${plan.id}`"
            method="put"
            submit-label="Update plan"
        />
      </div>
    </div>
  </AdminLayout>
</template>