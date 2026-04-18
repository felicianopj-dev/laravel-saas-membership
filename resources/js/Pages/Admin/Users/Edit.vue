<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()

const userRecord = computed(() => page.props.userRecord)
const flash = computed(() => page.props.flash ?? {})
const authUser = computed(() => page.props.auth?.user ?? null)

const profileForm = useForm({
  name: userRecord.value.name,
  email: userRecord.value.email,
  role: userRecord.value.role,
})

const passwordForm = useForm({
  password: '',
  password_confirmation: '',
})

const isCurrentUser = computed(() => {
  return authUser.value?.id === userRecord.value.id
})

const submitProfile = () => {
  profileForm.put(`/admin/users/${userRecord.value.id}`, {
    preserveScroll: true,
  })
}

const submitPassword = () => {
  passwordForm.put(`/admin/users/${userRecord.value.id}/password`, {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    },
  })
}
</script>

<template>
  <AdminLayout title="Edit User">
    <Head title="Edit User" />

    <div class="mx-auto max-w-3xl space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-slate-900">
            Edit User
          </h1>

          <p class="mt-1 text-sm text-slate-500">
            Update account information, role, and password.
          </p>
        </div>

        <Link
            href="/admin/users"
            class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
          Back to users
        </Link>
      </div>

      <div
          v-if="flash.success"
          class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
      >
        {{ flash.success }}
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-slate-900">
            Account Information
          </h2>

          <p class="mt-1 text-sm text-slate-500">
            Update the user's name, email, and role.
          </p>
        </div>

        <form class="space-y-6" @submit.prevent="submitProfile">
          <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Name
              </label>

              <input
                  v-model="profileForm.name"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
              >

              <p v-if="profileForm.errors.name" class="mt-2 text-sm text-red-600">
                {{ profileForm.errors.name }}
              </p>
            </div>

            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Email
              </label>

              <input
                  v-model="profileForm.email"
                  type="email"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
              >

              <p v-if="profileForm.errors.email" class="mt-2 text-sm text-red-600">
                {{ profileForm.errors.email }}
              </p>
            </div>

            <div>
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Role
              </label>

              <select
                  v-model="profileForm.role"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
                  :disabled="isCurrentUser"
              >
                <option value="member">
                  Member
                </option>

                <option value="admin">
                  Admin
                </option>
              </select>

              <p v-if="profileForm.errors.role" class="mt-2 text-sm text-red-600">
                {{ profileForm.errors.role }}
              </p>

              <p v-if="isCurrentUser" class="mt-2 text-xs text-slate-400">
                You cannot change your own role here.
              </p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
            <Link
                href="/admin/users"
                class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
              Cancel
            </Link>

            <button
                type="submit"
                :disabled="profileForm.processing"
                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
            >
              Save changes
            </button>
          </div>
        </form>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-slate-900">
            Reset Password
          </h2>

          <p class="mt-1 text-sm text-slate-500">
            Set a new password manually for this user.
          </p>
        </div>

        <form class="space-y-6" @submit.prevent="submitPassword">
          <div class="grid gap-6 md:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-medium text-slate-700">
                New password
              </label>

              <input
                  v-model="passwordForm.password"
                  type="password"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
              >

              <p v-if="passwordForm.errors.password" class="mt-2 text-sm text-red-600">
                {{ passwordForm.errors.password }}
              </p>
            </div>

            <div>
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Confirm new password
              </label>

              <input
                  v-model="passwordForm.password_confirmation"
                  type="password"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
              >
            </div>
          </div>

          <div class="flex items-center justify-end border-t border-slate-200 pt-6">
            <button
                type="submit"
                :disabled="passwordForm.processing"
                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
            >
              Update password
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>