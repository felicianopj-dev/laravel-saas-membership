<script setup>
import { ref } from 'vue'
import AdminSidebar from '@/Components/Admin/AdminSidebar.vue'
import AdminHeader from '@/Components/Admin/AdminHeader.vue'
import AdminMobileSidebar from '@/Components/Admin/AdminMobileSidebar.vue'

const isMobileSidebarOpen = ref(false)

const openSidebar = () => {
  isMobileSidebarOpen.value = true
}

const closeSidebar = () => {
  isMobileSidebarOpen.value = false
}
</script>

<template>
  <div class="min-h-screen bg-slate-100">
    <AdminMobileSidebar
        :is-open="isMobileSidebarOpen"
        @close="closeSidebar"
    />

    <div class="flex min-h-screen">
      <AdminSidebar />

      <div class="flex min-h-screen min-w-0 flex-1 flex-col">
        <AdminHeader @toggle-sidebar="openSidebar" />

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>