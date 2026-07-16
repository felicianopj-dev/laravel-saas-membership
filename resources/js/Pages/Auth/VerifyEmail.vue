<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  status: {
    type: String,
    default: null,
  },
})

const form = useForm({})

const submit = () => {
  form.post('/email/verification-notification')
}

const linkSent = computed(() => props.status === 'verification-link-sent')
</script>

<template>
  <Head title="Verify Email" />

  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm">
      <h1 class="text-2xl font-semibold text-gray-900">Verify your email</h1>
      <p class="mt-2 text-sm text-gray-600">
        We sent a verification link to your email address. Click it to activate
        your membership. If you didn't get it, we can send another.
      </p>

      <p v-if="linkSent" class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm font-medium text-green-700">
        A fresh verification link has been sent to your email address.
      </p>

      <form class="mt-6" @submit.prevent="submit">
        <button
            type="submit"
            :disabled="form.processing"
            class="w-full rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-50"
        >
          Resend verification email
        </button>
      </form>

      <form class="mt-4" @submit.prevent="$inertia.post('/logout')">
        <button type="submit" class="text-sm font-medium text-gray-600 underline">
          Log out
        </button>
      </form>
    </div>
  </div>
</template>
