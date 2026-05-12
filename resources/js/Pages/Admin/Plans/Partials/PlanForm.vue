<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

type PlanPayload = {
  name: string
  slug: string
  description: string
  price: number
  currency: string
  billing_interval: string
  stripe_product_id: string
  stripe_price_id: string
  is_active: boolean
  sort_order: number
}

const props = defineProps<{
  plan?: Partial<PlanPayload>
  submitUrl: string
  method: 'post' | 'put'
  submitLabel: string
}>()

const form = useForm<PlanPayload>({
  name: props.plan?.name ?? '',
  slug: props.plan?.slug ?? '',
  description: props.plan?.description ?? '',
  price: props.plan?.price ?? 0,
  currency: props.plan?.currency ?? 'usd',
  billing_interval: props.plan?.billing_interval ?? 'monthly',
  stripe_product_id: props.plan?.stripe_product_id ?? '',
  stripe_price_id: props.plan?.stripe_price_id ?? '',
  is_active: props.plan?.is_active ?? true,
  sort_order: props.plan?.sort_order ?? 0,
})

const submit = () => {
  if (props.method === 'post') {
    form.post(props.submitUrl)
    return
  }

  form.put(props.submitUrl)
}
</script>

<template>
  <form class="space-y-6" @submit.prevent="submit">
    <div class="grid gap-6 md:grid-cols-2">
      <div>
        <label class="block text-sm font-medium text-gray-700">
          Name
        </label>

        <input
            v-model="form.name"
            type="text"
            class="mt-1 w-full rounded-xl border-gray-300 shadow-sm"
        >

        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
          {{ form.errors.name }}
        </p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">
          Slug
        </label>

        <input
            v-model="form.slug"
            type="text"
            class="mt-1 w-full rounded-xl border-gray-300 shadow-sm"
        >

        <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">
          {{ form.errors.slug }}
        </p>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">
        Description
      </label>

      <textarea
          v-model="form.description"
          rows="3"
          class="mt-1 w-full rounded-xl border-gray-300 shadow-sm"
      />

      <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
        {{ form.errors.description }}
      </p>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
      <div>
        <label class="block text-sm font-medium text-gray-700">
          Price in cents
        </label>

        <input
            v-model.number="form.price"
            type="number"
            min="0"
            class="mt-1 w-full rounded-xl border-gray-300 shadow-sm"
        >

        <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">
          {{ form.errors.price }}
        </p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">
          Currency
        </label>

        <input
            v-model="form.currency"
            type="text"
            maxlength="3"
            class="mt-1 w-full rounded-xl border-gray-300 shadow-sm"
        >

        <p v-if="form.errors.currency" class="mt-1 text-sm text-red-600">
          {{ form.errors.currency }}
        </p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">
          Billing interval
        </label>

        <select
            v-model="form.billing_interval"
            class="mt-1 w-full rounded-xl border-gray-300 shadow-sm"
        >
          <option value="monthly">
            Monthly
          </option>

          <option value="yearly">
            Yearly
          </option>
        </select>

        <p v-if="form.errors.billing_interval" class="mt-1 text-sm text-red-600">
          {{ form.errors.billing_interval }}
        </p>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
      <h2 class="text-sm font-semibold text-gray-900">
        Stripe references
      </h2>

      <p class="mt-1 text-sm text-gray-500">
        Create the product and price manually in Stripe, then paste the IDs here.
      </p>

      <div class="mt-4 grid gap-6 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium text-gray-700">
            Stripe product ID
          </label>

          <input
              v-model="form.stripe_product_id"
              type="text"
              placeholder="prod_..."
              class="mt-1 w-full rounded-xl border-gray-300 shadow-sm"
          >

          <p v-if="form.errors.stripe_product_id" class="mt-1 text-sm text-red-600">
            {{ form.errors.stripe_product_id }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">
            Stripe price ID
          </label>

          <input
              v-model="form.stripe_price_id"
              type="text"
              placeholder="price_..."
              class="mt-1 w-full rounded-xl border-gray-300 shadow-sm"
          >

          <p v-if="form.errors.stripe_price_id" class="mt-1 text-sm text-red-600">
            {{ form.errors.stripe_price_id }}
          </p>
        </div>
      </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
      <div>
        <label class="block text-sm font-medium text-gray-700">
          Sort order
        </label>

        <input
            v-model.number="form.sort_order"
            type="number"
            min="0"
            class="mt-1 w-full rounded-xl border-gray-300 shadow-sm"
        >

        <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">
          {{ form.errors.sort_order }}
        </p>
      </div>

      <div class="flex items-end">
        <label class="flex items-center gap-3">
          <input
              v-model="form.is_active"
              type="checkbox"
              class="rounded border-gray-300"
          >

          <span class="text-sm font-medium text-gray-700">
                        Active
                    </span>
        </label>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button
          type="submit"
          class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
          :disabled="form.processing"
      >
        {{ form.processing ? 'Saving...' : submitLabel }}
      </button>
    </div>
  </form>
</template>