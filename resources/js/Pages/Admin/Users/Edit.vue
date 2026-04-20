<script setup>
import { computed } from 'vue'
import { router, Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()

const userRecord = computed(() => page.props.userRecord)
console.log(userRecord.value)
console.log(userRecord.value.is_deleted)
const flash = computed(() => page.props.flash ?? {})
const authUser = computed(() => page.props.auth?.user ?? null)

const profileForm = useForm({
  name: userRecord.value.name,
  email: userRecord.value.email,
  role: userRecord.value.role,
  status: userRecord.value.status,
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

const destroyUser = () => {
  if (isCurrentUser.value) {
    return
  }

  const confirmed = window.confirm(`Are you sure you want to delete ${userRecord.value.name}?`)

  if (!confirmed) {
    return
  }

  router.delete(`/admin/users/${userRecord.value.id}`, {
    preserveScroll: true,
  })
}

const restoreUser = () => {
  const confirmed = window.confirm(`Are you sure you want to restore ${userRecord.value.name}?`)

  if (!confirmed) {
    return
  }

  router.patch(`/admin/users/${userRecord.value.id}/restore`, {}, {
    preserveScroll: true,
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
            Update account information, role, status, and password.
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
            Update the user's name, email, role, and status.
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

            <div>
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Status
              </label>

              <select
                  v-model="profileForm.status"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
                  :disabled="isCurrentUser"
              >
                <option value="active">
                  Active
                </option>

                <option value="inactive">
                  Inactive
                </option>
              </select>

              <p v-if="profileForm.errors.status" class="mt-2 text-sm text-red-600">
                {{ profileForm.errors.status }}
              </p>

              <p v-if="isCurrentUser" class="mt-2 text-xs text-slate-400">
                You cannot deactivate your own account here.
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

      <div class="rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm">
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-red-700">
            Danger Zone
          </h2>

          <p class="mt-1 text-sm text-red-600">
            Irreversible and sensitive actions for this user account.
          </p>
        </div>

        <div class="flex flex-col gap-4">
          <!-- DELETE -->
          <div
              v-if="!userRecord.is_deleted"
              class="flex items-center justify-between rounded-xl border border-red-200 bg-white p-4"
          >

            <div>
              <p class="text-sm font-medium text-slate-900">
                Delete user
              </p>

              <p class="text-sm text-slate-500">
                This will soft delete the user account. It can be restored later.
              </p>
            </div>

            <button
                type="button"
                class="inline-flex items-center rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-100"
                :disabled="isCurrentUser"
                @click="destroyUser"
            >
              Delete
            </button>
          </div>

          <!-- RESTORE -->
          <div
              v-if="userRecord.is_deleted"
              class="flex items-center justify-between rounded-xl border border-emerald-200 bg-white p-4"
          >
            <div>
              <p class="text-sm font-medium text-slate-900">
                Restore user
              </p>

              <p class="text-sm text-slate-500">
                This will restore the user account and allow access again.
              </p>
            </div>

            <button
                type="button"
                class="inline-flex items-center rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100"
                @click="restoreUser"
            >
              Restore
            </button>
          </div>

          <!-- SELF PROTECTION -->
          <p
              v-if="isCurrentUser"
              class="text-xs text-slate-400"
          >
            You cannot delete your own account.
          </p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>