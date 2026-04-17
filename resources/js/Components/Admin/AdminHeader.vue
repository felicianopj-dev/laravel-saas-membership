<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const emit = defineEmits(['toggle-sidebar'])

const page = usePage()

const userName = computed(() => page.props.auth?.user?.name ?? 'Admin User')

const pageTitle = computed(() => page.props.title ?? 'Admin')

const pageDescription = computed(() => {
  return page.props.description ?? 'Manage your application from the admin dashboard.'
})
</script>

<template>
  <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
      <div class="flex min-w-0 items-center gap-3">
        <button
            type="button"
            class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 xl:hidden"
            @click="emit('toggle-sidebar')"
        >
          <svg
              class="h-5 w-5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
              stroke-linecap="round"
              stroke-linejoin="round"
          >
            <path d="M3 12h18" />
            <path d="M3 6h18" />
            <path d="M3 18h18" />
          </svg>
        </button>

        <div class="min-w-0">
          <p class="truncate text-lg font-semibold tracking-tight text-slate-900 sm:text-xl">
            {{ pageTitle }}
          </p>

          <p class="truncate text-sm text-slate-500">
            {{ pageDescription }}
          </p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <div class="hidden items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm sm:flex">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
            {{ userName.charAt(0).toUpperCase() }}
          </div>

          <div class="leading-tight">
            <p class="text-sm font-medium text-slate-900">
              {{ userName }}
            </p>

            <p class="text-xs text-slate-500">
              Administrator
            </p>
          </div>
        </div>

        <Link
            href="/logout"
            method="post"
            as="button"
            type="button"
            class="inline-flex items-center gap-2 rounded-2xl border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-600 shadow-sm transition hover:bg-red-50 hover:text-red-700 cursor-pointer"
        >
          <svg
              class="h-4 w-4"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
              stroke-linecap="round"
              stroke-linejoin="round"
          >
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <path d="m16 17 5-5-5-5" />
            <path d="M21 12H9" />
          </svg>

          <span class="hidden sm:inline">Logout</span>
        </Link>
      </div>
    </div>
  </header>
</template>