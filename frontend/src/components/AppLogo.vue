<script setup lang="ts">
import { GalleryVerticalEnd } from 'lucide-vue-next'

const props = defineProps<{
  showName?: boolean
}>()
</script>

<template>
  <div class="flex items-center gap-3">
    <!-- Logo always visible -->
    <div
      class="flex aspect-square w-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground"
    >
      <GalleryVerticalEnd class="w-4 h-4" />
    </div>

    <!-- Name only shown if showName is true -->
    <div v-if="props.showName" class="flex-1 text-start text-sm leading-tight">
      <span class="truncate font-semibold">Acme Inc</span>
    </div>
  </div>
</template>
