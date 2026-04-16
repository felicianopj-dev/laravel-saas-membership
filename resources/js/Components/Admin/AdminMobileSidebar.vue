<script setup>
import { adminNavigation } from '@/Data/admin-navigation'
import AdminNavLink from '@/Components/Admin/AdminNavLink.vue'

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['close'])
</script>

<template>
  <Teleport to="body">
    <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
      <div
          v-if="isOpen"
          class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm xl:hidden"
          @click="emit('close')"
      />
    </transition>

    <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="-translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-x-0"
        leave-to-class="-translate-x-full"
    >
      <aside
          v-if="isOpen"
          class="fixed inset-y-0 left-0 z-50 flex w-80 max-w-[85vw] flex-col border-r border-slate-800 bg-slate-950 xl:hidden"
      >
        <div class="flex items-center justify-between border-b border-slate-800 px-5 py-5">
          <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-bold text-white">
              LS
            </div>

            <div>
              <p class="text-sm font-semibold text-white">
                Laravel SaaS
              </p>

              <p class="text-xs text-slate-400">
                Admin Console
              </p>
            </div>
          </div>

          <button
              type="button"
              class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-900 text-slate-300 transition hover:text-white"
              @click="emit('close')"
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
              <path d="M18 6 6 18" />
              <path d="m6 6 12 12" />
            </svg>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6">
          <nav class="space-y-2">
            <AdminNavLink
                v-for="item in adminNavigation"
                :key="item.href"
                :item="item"
                mobile
                @click="emit('close')"
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
      </aside>
    </transition>
  </Teleport>
</template>