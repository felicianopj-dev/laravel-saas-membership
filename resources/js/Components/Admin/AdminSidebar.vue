<script setup>
import { adminNavigation } from '@/Data/admin-navigation'
import AdminNavLink from '@/Components/Admin/AdminNavLink.vue'
</script>

<template>
  <aside class="hidden w-72 shrink-0 border-r border-slate-800 bg-slate-950 xl:flex xl:flex-col">
    <div class="border-b border-slate-800 px-6 py-6">
      <div class="flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-bold text-white shadow-lg shadow-indigo-950/50">
          LS
        </div>

        <div>
          <p class="text-sm font-semibold tracking-wide text-white">
            Laravel SaaS
          </p>

          <p class="text-xs text-slate-400">
            Admin Console
          </p>
        </div>
      </div>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-6">
      <nav class="space-y-2">
        <AdminNavLink
            v-for="item in adminNavigation"
            :key="item.href"
            :item="item"
        >
          <template #icon>
            <svg
                v-if="item.icon === 'home'"
                class="h-5 w-5 shrink-0"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
              <path d="M3 10.5 12 3l9 7.5" />
              <path d="M5 9.5V21h14V9.5" />
              <path d="M9 21v-6h6v6" />
            </svg>

            <svg
                v-else-if="item.icon === 'users'"
                class="h-5 w-5 shrink-0"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
              <path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2" />
              <circle cx="9.5" cy="7" r="4" />
              <path d="M20 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M14 3.13a4 4 0 0 1 0 7.75" />
            </svg>
          </template>
        </AdminNavLink>
      </nav>
    </div>

    <div class="border-t border-slate-800 px-6 py-5">
      <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4">
        <p class="text-sm font-medium text-white">
          Admin area
        </p>

        <p class="mt-1 text-xs leading-5 text-slate-400">
          Manage members, subscriptions, metrics, and future billing modules from a single place.
        </p>
      </div>
    </div>
  </aside>
</template>