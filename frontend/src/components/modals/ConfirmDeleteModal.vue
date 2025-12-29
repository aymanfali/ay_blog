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
  description?: string
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'confirm'): void
}>()
</script>

<template>
  <Dialog :open="open" @update:open="emit('close')">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>
          {{ title ?? 'Confirm deletion' }}
        </DialogTitle>
      </DialogHeader>

      <p class="text-sm text-muted-foreground">
        {{ description ?? 'This action cannot be undone.' }}
      </p>

      <DialogFooter>
        <Button variant="outline" @click="emit('close')"> Cancel </Button>
        <Button variant="destructive" :disabled="loading" @click="emit('confirm')"> Delete </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
