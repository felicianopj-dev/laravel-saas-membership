<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()

const userRecord = computed(() => page.props.userRecord)
const flash = computed(() => page.props.flash ?? {})
const authUser = computed(() => page.props.auth?.user ?? null)

const form = useForm({
  name: userRecord.value.name,
  email: userRecord.value.email,
  role: userRecord.value.role,
})

const isCurrentUser = computed(() => {
  return authUser.value?.id === userRecord.value.id
})

const submit = () => {
  form.put(`/admin/users/${userRecord.value.id}`, {
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
            Update account information and role.
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
        <form class="space-y-6" @submit.prevent="submit">
          <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Name
              </label>

              <input
                  v-model="form.name"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
              >

              <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                {{ form.errors.name }}
              </p>
            </div>

            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Email
              </label>

              <input
                  v-model="form.email"
                  type="email"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
              >

              <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                {{ form.errors.email }}
              </p>
            </div>

            <div>
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Role
              </label>

              <select
                  v-model="form.role"
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

              <p v-if="form.errors.role" class="mt-2 text-sm text-red-600">
                {{ form.errors.role }}
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
                :disabled="form.processing"
                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
            >
              Save changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>