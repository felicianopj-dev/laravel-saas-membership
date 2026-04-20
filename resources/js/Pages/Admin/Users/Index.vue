<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()

const users = computed(() => page.props.users)
const filters = computed(() => page.props.filters ?? { search: '' })
const flash = computed(() => page.props.flash ?? {})
const authUser = computed(() => page.props.auth?.user ?? null)

const search = ref(filters.value.search ?? '')

watch(search, (value) => {
  router.get(
      '/admin/users',
      { search: value },
      {
        preserveState: true,
        replace: true,
        preserveScroll: true,
      },
  )
})

const formatDateTime = (value) => {
  if (!value) {
    return '—'
  }

  return new Date(value).toLocaleString()
}

const getVerifiedLabel = (user) => {
  return user.email_verified_at ? 'Verified' : 'Unverified'
}

const getAccountStatusLabel = (user) => {
  return user.is_deleted ? 'Deleted' : 'Active'
}

const destroyUser = (user) => {
  if (authUser.value?.id === user.id) {
    return
  }

  const confirmed = window.confirm(`Are you sure you want to delete ${user.name}?`)

  if (!confirmed) {
    return
  }

  router.delete(`/admin/users/${user.id}`, {
    preserveScroll: true,
  })
}

const restoreUser = (user) => {
  const confirmed = window.confirm(`Are you sure you want to restore ${user.name}?`)

  if (!confirmed) {
    return
  }

  router.patch(`/admin/users/${user.id}/restore`, {}, {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Users" />

  <AdminLayout title="Users">
    <div class="space-y-6">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">
              User Management
            </h2>

            <p class="mt-1 text-sm text-slate-500">
              Search users and manage account access.
            </p>
          </div>

          <div class="w-full md:w-80">
            <input
                v-model="search"
                type="text"
                placeholder="Search by name or email"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none"
            >
          </div>
        </div>
      </div>

      <div
          v-if="flash.success"
          class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
      >
        {{ flash.success }}
      </div>

      <div
          v-if="flash.error"
          class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
      >
        {{ flash.error }}
      </div>

      <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Name
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Email
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Role
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Status
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Verified
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Created
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Updated
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                Action
              </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 bg-white">
            <tr
                v-for="user in users.data"
                :key="user.id"
                :class="user.is_deleted ? 'bg-slate-50/70 opacity-70' : ''"
            >
              <td class="px-6 py-4">
                <div class="font-medium text-slate-900">
                  {{ user.name }}
                </div>
              </td>

              <td class="px-6 py-4 text-sm text-slate-600">
                {{ user.email }}
              </td>

              <td class="px-6 py-4">
                  <span
                      class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                      :class="user.role === 'admin'
                      ? 'bg-slate-900 text-white'
                      : 'bg-slate-100 text-slate-700'"
                  >
                    {{ user.role }}
                  </span>
              </td>

              <td class="px-6 py-4">
                  <span
                      class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                      :class="user.is_deleted
                      ? 'bg-red-100 text-red-700'
                      : 'bg-emerald-100 text-emerald-700'"
                  >
                    {{ getAccountStatusLabel(user) }}
                  </span>
              </td>

              <td class="px-6 py-4">
                  <span
                      class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                      :class="user.email_verified_at
                      ? 'bg-blue-100 text-blue-700'
                      : 'bg-slate-100 text-slate-700'"
                  >
                    {{ getVerifiedLabel(user) }}
                  </span>
              </td>

              <td class="px-6 py-4 text-sm text-slate-600">
                {{ formatDateTime(user.created_at) }}
              </td>

              <td class="px-6 py-4 text-sm text-slate-600">
                {{ formatDateTime(user.updated_at) }}
              </td>

              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <Link
                      v-if="!user.is_deleted"
                      :href="`/admin/users/${user.id}/edit`"
                      class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                  >
                    Edit
                  </Link>

                  <button
                      v-if="!user.is_deleted && authUser?.id !== user.id"
                      type="button"
                      class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 transition hover:bg-red-100"
                      @click="destroyUser(user)"
                  >
                    Delete
                  </button>

                  <button
                      v-if="user.is_deleted"
                      type="button"
                      class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100"
                      @click="restoreUser(user)"
                  >
                    Restore
                  </button>

                  <span
                      v-if="!user.is_deleted && authUser?.id === user.id"
                      class="text-xs text-slate-400"
                  >
                      Current user
                    </span>
                </div>
              </td>
            </tr>

            <tr v-if="users.data.length === 0">
              <td colspan="8" class="px-6 py-10 text-center text-sm text-slate-500">
                No users found.
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-6 py-4 shadow-sm">
        <div class="text-sm text-slate-500">
          Showing {{ users.from ?? 0 }} to {{ users.to ?? 0 }} of {{ users.total }} users
        </div>

        <div class="flex items-center gap-2">
          <button
              type="button"
              class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="!users.prev_page_url"
              @click="users.prev_page_url && router.visit(users.prev_page_url, { preserveScroll: true, preserveState: true })"
          >
            Previous
          </button>

          <button
              type="button"
              class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="!users.next_page_url"
              @click="users.next_page_url && router.visit(users.next_page_url, { preserveScroll: true, preserveState: true })"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>