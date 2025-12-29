<script setup lang="ts">
import { Icon } from '@iconify/vue'
import { useColorMode } from '@vueuse/core'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Monitor, MoonStar, Sun } from 'lucide-vue-next'
import { computed } from 'vue'

const mode = useColorMode()

const isActive = (theme: 'light' | 'dark' | 'auto') => computed(() => mode.value === theme)

const currentIcon = computed(() => {
  if (mode.value === 'light') return 'radix-icons:sun'
  if (mode.value === 'dark') return 'radix-icons:moon'
  return 'radix-icons:monitor'
})
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="outline">
        <Icon :icon="currentIcon" class="h-[1.2rem] w-[1.2rem] transition-all" />
        <span class="sr-only">Toggle theme</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent class="flex items-center gap-1 p-1">
      <DropdownMenuItem :class="{ 'bg-muted': isActive('light').value }" @click="mode = 'light'">
        <Sun class="m-1" />
      </DropdownMenuItem>
      <DropdownMenuItem :class="{ 'bg-muted': isActive('dark').value }" @click="mode = 'dark'">
        <MoonStar class="m-1" />
      </DropdownMenuItem>
      <DropdownMenuItem :class="{ 'bg-muted': isActive('auto').value }" @click="mode = 'auto'">
        <Monitor class="m-1" />
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
