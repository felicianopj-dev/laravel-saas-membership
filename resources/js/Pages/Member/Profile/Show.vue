<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const page = usePage()

const profile = page.props.profile

const profileForm = useForm({
  name: profile.name ?? '',
  email: profile.email ?? '',
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submitProfileForm = () => {
  profileForm.put('/member/profile')
}

const submitPasswordForm = () => {
  passwordForm.put('/member/profile/password', {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    },
  })
}
</script>

<template>
  <Head title="My Profile" />

  <MemberLayout title="My Profile">
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">My Profile</h1>
        <p class="mt-1 text-sm text-slate-600">
          Manage your account information and password.
        </p>
      </div>

      <div
          v-if="$page.props.flash?.success"
          class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
      >
        {{ $page.props.flash.success }}
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-slate-900">Account Information</h2>
          <p class="mt-1 text-sm text-slate-600">
            Update your basic account details.
          </p>
        </div>

        <form class="space-y-5" @submit.prevent="submitProfileForm">
          <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
              Name
            </label>

            <input
                id="name"
                v-model="profileForm.name"
                type="text"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-900 focus:outline-none focus:ring-0"
            >

            <p
                v-if="profileForm.errors.name"
                class="mt-2 text-sm text-red-600"
            >
              {{ profileForm.errors.name }}
            </p>
          </div>

          <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
              Email
            </label>

            <input
                id="email"
                v-model="profileForm.email"
                type="email"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-900 focus:outline-none focus:ring-0"
            >

            <p
                v-if="profileForm.errors.email"
                class="mt-2 text-sm text-red-600"
            >
              {{ profileForm.errors.email }}
            </p>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
              <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                Role
              </p>
              <p class="mt-1 text-sm font-medium text-slate-900">
                {{ profile.role }}
              </p>
            </div>

            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
              <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                Status
              </p>
              <p class="mt-1 text-sm font-medium text-slate-900">
                {{ profile.status }}
              </p>
            </div>
          </div>

          <div class="flex justify-end">
            <button
                type="submit"
                :disabled="profileForm.processing"
                class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {{ profileForm.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-slate-900">Update Password</h2>
          <p class="mt-1 text-sm text-slate-600">
            Choose a strong password to keep your account secure.
          </p>
        </div>

        <form class="space-y-5" @submit.prevent="submitPasswordForm">
          <div>
            <label for="current_password" class="mb-2 block text-sm font-medium text-slate-700">
              Current Password
            </label>

            <input
                id="current_password"
                v-model="passwordForm.current_password"
                type="password"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-900 focus:outline-none focus:ring-0"
            >

            <p
                v-if="passwordForm.errors.current_password"
                class="mt-2 text-sm text-red-600"
            >
              {{ passwordForm.errors.current_password }}
            </p>
          </div>

          <div>
            <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
              New Password
            </label>

            <input
                id="password"
                v-model="passwordForm.password"
                type="password"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-900 focus:outline-none focus:ring-0"
            >

            <p
                v-if="passwordForm.errors.password"
                class="mt-2 text-sm text-red-600"
            >
              {{ passwordForm.errors.password }}
            </p>
          </div>

          <div>
            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">
              Confirm New Password
            </label>

            <input
                id="password_confirmation"
                v-model="passwordForm.password_confirmation"
                type="password"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-900 focus:outline-none focus:ring-0"
            >
          </div>

          <div class="flex justify-end">
            <button
                type="submit"
                :disabled="passwordForm.processing"
                class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>