<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})
</script>

<template>
  <Head title="Register" />

  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm">
      <h1 class="text-2xl font-semibold text-gray-900">Create account</h1>
      <p class="mt-2 text-sm text-gray-600">
        New accounts are created as members by default.
      </p>

      <form class="mt-6 space-y-4" @submit.prevent="form.post('/register')">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
          <input
              v-model="form.name"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
          >
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
            {{ form.errors.name }}
          </p>
        </div>

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

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Confirm password</label>
          <input
              v-model="form.password_confirmation"
              type="password"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
          >
        </div>

        <button
            type="submit"
            :disabled="form.processing"
            class="w-full rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-50"
        >
          Register
        </button>
      </form>

      <p class="mt-6 text-sm text-gray-600">
        Already have an account?
        <Link href="/login" class="font-medium text-gray-900 underline">
          Sign in
        </Link>
      </p>
    </div>
  </div>
</template>