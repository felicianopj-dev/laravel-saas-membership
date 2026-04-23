<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const page = usePage()

const profile = page.props.profile

const form = useForm({
  name: profile.name ?? '',
  email: profile.email ?? '',
})
</script>

<template>
  <Head title="My Profile" />

  <MemberLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">My Profile</h1>
        <p class="mt-1 text-sm text-gray-600">
          Update your account information.
        </p>
      </div>

      <div
          v-if="$page.props.flash?.success"
          class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
      >
        {{ $page.props.flash.success }}
      </div>

      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Account Information</h2>
          <p class="mt-1 text-sm text-gray-600">
            Keep your account details up to date.
          </p>
        </div>

        <form
            class="space-y-5"
            @submit.prevent="form.put('/member/profile')"
        >
          <div>
            <label
                for="name"
                class="mb-2 block text-sm font-medium text-gray-700"
            >
              Name
            </label>

            <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none focus:ring-0"
            >

            <p
                v-if="form.errors.name"
                class="mt-2 text-sm text-red-600"
            >
              {{ form.errors.name }}
            </p>
          </div>

          <div>
            <label
                for="email"
                class="mb-2 block text-sm font-medium text-gray-700"
            >
              Email
            </label>

            <input
                id="email"
                v-model="form.email"
                type="email"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none focus:ring-0"
            >

            <p
                v-if="form.errors.email"
                class="mt-2 text-sm text-red-600"
            >
              {{ form.errors.email }}
            </p>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
              <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                Role
              </p>
              <p class="mt-1 text-sm font-medium text-gray-900">
                {{ profile.role }}
              </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
              <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                Status
              </p>
              <p class="mt-1 text-sm font-medium text-gray-900">
                {{ profile.status }}
              </p>
            </div>
          </div>

          <div class="flex justify-end">
            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>