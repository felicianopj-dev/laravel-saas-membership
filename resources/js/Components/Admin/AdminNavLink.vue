<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  mobile: {
    type: Boolean,
    default: false,
  },
})

const page = usePage()

const isActive = computed(() => {
  if (props.item.exact) {
    return page.url === props.item.href
  }

  return page.url.startsWith(props.item.href)
})
</script>

<template>
  <Link
      :href="item.href"
      class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200"
      :class="[
            isActive
                ? 'bg-white/10 text-white shadow-sm'
                : 'text-slate-300 hover:bg-white/5 hover:text-white',
            mobile ? 'text-base' : '',
        ]"
  >
    <slot name="icon" />

    <span>{{ item.label }}</span>
  </Link>
</template>