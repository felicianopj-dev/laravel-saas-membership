<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
})
</script>

<template>
  <Head title="Login" />

  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm">
      <h1 class="text-2xl font-semibold text-gray-900">Login</h1>
      <p class="mt-2 text-sm text-gray-600">
        Sign in to access your account.
      </p>

      <form class="mt-6 space-y-4" @submit.prevent="form.post('/login')">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
          <input
              v-model="form.email"
              type="email"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
          >
          <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
            {{ form.errors.email }}
          </p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
          <input
              v-model="form.password"
              type="password"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
          >
          <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
            {{ form.errors.password }}
          </p>
        </div>

        <button
            type="submit"
            :disabled="form.processing"
            class="w-full rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-50"
        >
          Sign in
        </button>
      </form>

      <p class="mt-6 text-sm text-gray-600">
        Don’t have an account?
        <Link href="/register" class="font-medium text-gray-900 underline">
          Create one
        </Link>
      </p>
    </div>
  </div>
</template>