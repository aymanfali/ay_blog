<script setup lang="ts">
import { computed } from 'vue'
import { Alert, AlertTitle, AlertDescription } from './ui/alert'
import { CheckCircle, XCircle } from 'lucide-vue-next'

type AlertVariant = 'default' | 'destructive'

const props = defineProps<{
  title?: string
  message: string
  variant?: AlertVariant
  show: boolean
}>()

// Determine which icon to show based on variant
const icon = computed(() => {
  switch (props.variant) {
    case 'destructive':
      return XCircle
    default:
      return CheckCircle
  }
})
</script>

<template>
  <Alert
    v-if="props.show"
    :variant="props.variant || 'default'"
    class="flex items-start gap-2 w-full max-w-md"
  >
    <component :is="icon" class="w-5 h-5 mt-1" />
    <div class="flex-1">
      <AlertTitle v-if="props.title">{{ props.title }}</AlertTitle>
      <AlertDescription>{{ props.message }}</AlertDescription>
    </div>
  </Alert>
</template>
