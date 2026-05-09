<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const flash = computed(() => page.props.flash ?? {})

const message = computed(() => {
  return flash.value.success
      || flash.value.error
      || flash.value.warning
      || flash.value.info
      || null
})

const type = computed(() => {
  if (flash.value.success) return 'success'
  if (flash.value.error) return 'error'
  if (flash.value.warning) return 'warning'
  if (flash.value.info) return 'info'

  return null
})

const classes = computed(() => {
  switch (type.value) {
    case 'success':
      return 'border-emerald-200 bg-emerald-50 text-emerald-700'
    case 'error':
      return 'border-rose-200 bg-rose-50 text-rose-700'
    case 'warning':
      return 'border-amber-200 bg-amber-50 text-amber-700'
    case 'info':
      return 'border-sky-200 bg-sky-50 text-sky-700'
    default:
      return ''
  }
})
</script>

<template>
  <div
      v-if="message"
      class="mb-6 rounded-xl border px-4 py-3 text-sm font-medium"
      :class="classes"
  >
    {{ message }}
  </div>
</template>