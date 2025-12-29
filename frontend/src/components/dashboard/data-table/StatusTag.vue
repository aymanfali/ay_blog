<script setup lang="ts">
import { computed } from 'vue'
import { STATUS_CONFIG } from '@/config/status.config'

const props = defineProps<{ value: string }>()

const config = computed(
  () =>
    STATUS_CONFIG[props.value as keyof typeof STATUS_CONFIG] ?? {
      label: props.value,
      class: 'bg-muted text-muted-foreground border',
    },
)
</script>

<template>
  <span
    class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium"
    :class="config.class"
  >
    {{ config.label }}
  </span>
</template>
