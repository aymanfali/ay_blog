<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuCheckboxItem,
} from '@/components/ui/dropdown-menu'

defineProps<{ table: any }>()
</script>

<template>
  <div class="flex items-center gap-2 py-4">
    <Input
      placeholder="Search…"
      class="max-w-sm"
      @update:model-value="table.setGlobalFilter($event)"
    />

    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button variant="outline" class="ml-auto">Columns</Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        <DropdownMenuCheckboxItem
          v-for="col in table.getAllColumns().filter((c) => c.getCanHide())"
          :key="col.id"
          :model-value="col.getIsVisible()"
          @update:model-value="(v) => col.toggleVisibility(!!v)"
        >
          {{ col.id }}
        </DropdownMenuCheckboxItem>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>
