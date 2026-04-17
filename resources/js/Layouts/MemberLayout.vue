<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    default: 'Member Area',
  },
})

const page = usePage()

const user = computed(() => page.props.auth?.user ?? null)

const navigationItems = [
  {
    label: 'Dashboard',
    href: '/member',
    exact: true,
  },
  {
    label: 'My Profile',
    href: '/member/profile',
    startsWith: '/member/profile',
  },
]

const isActiveRoute = (item) => {
  if (item.exact) {
    return page.url === item.href
  }

  if (item.startsWith) {
    return page.url === item.href || page.url.startsWith(item.startsWith)
  }

  return page.url === item.href
}
</script>

<template>
  <div class="min-h-screen bg-slate-100">
    <Head :title="title" />

    <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 border-r border-slate-200 bg-slate-900 text-white lg:flex lg:flex-col">
      <div class="border-b border-slate-800 px-6 py-5">
        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
          Laravel SaaS
        </div>

        <div class="mt-2 text-xl font-bold text-white">
          Member Area
        </div>
      </div>

      <nav class="flex-1 space-y-2 px-4 py-6">
        <Link
            v-for="item in navigationItems"
            :key="item.href"
            :href="item.href"
            class="block rounded-lg px-4 py-3 text-sm font-medium transition"
            :class="isActiveRoute(item)
            ? 'bg-white text-slate-900'
            : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
        >
          {{ item.label }}
        </Link>
      </nav>

      <div class="border-t border-slate-800 px-6 py-5">
        <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
          Logged in as
        </div>

        <div class="mt-2 text-sm font-semibold text-white">
          {{ user?.name ?? 'Member' }}
        </div>

        <div class="text-sm text-slate-400">
          {{ user?.email ?? 'member@example.com' }}
        </div>
      </div>
    </aside>

    <div class="lg:pl-64">
      <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
          <div>
            <div class="text-sm font-medium text-slate-500">
              Member Portal
            </div>

            <h1 class="text-xl font-semibold text-slate-900">
              {{ title }}
            </h1>
          </div>

          <div class="flex items-center gap-4">
            <div class="text-right">
              <div class="text-sm font-semibold text-slate-900">
                {{ user?.name ?? 'Member' }}
              </div>

              <div class="text-sm text-slate-500">
                {{ user?.email ?? 'member@example.com' }}
              </div>
            </div>

            <Link
                href="/logout"
                method="post"
                as="button"
                type="button"
                class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-600 shadow-sm transition hover:bg-red-50 hover:text-red-700"
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

              <span>Logout</span>
            </Link>
          </div>
        </div>
      </header>

      <main class="mx-auto max-w-7xl px-6 py-8">
        <slot />
      </main>
    </div>
  </div>
</template>