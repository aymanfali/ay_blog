<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '@/components/ui/dialog'

defineProps<{
  open: boolean
  title?: string
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'submit'): void
}>()
</script>

<template>
  <Dialog :open="open" @update:open="emit('close')">
    <DialogContent class="max-w-lg">
      <DialogHeader>
        <DialogTitle>
          {{ title ?? 'Edit item' }}
        </DialogTitle>
      </DialogHeader>

      <!-- 🔥 Inject ANY form -->
      <div class="py-2">
        <slot />
      </div>

      <DialogFooter>
        <Button variant="outline" @click="emit('close')"> Cancel </Button>
        <Button :disabled="loading" @click="emit('submit')"> Save </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
