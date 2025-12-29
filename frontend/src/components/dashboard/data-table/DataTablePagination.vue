<script setup lang="ts">
import type { PaginationState, Table } from '@tanstack/vue-table'
import { Button } from '@/components/ui/button'
import { Select, SelectTrigger, SelectContent, SelectItem } from '@/components/ui/select'
import { computed } from 'vue'
import { ChevronLeft, ChevronRight, ChevronsRight, ChevronsLeft } from 'lucide-vue-next';

const props = defineProps<{
  table: Table<any>
  pagination: PaginationState
  pageSizeOptions: number[]
}>()

const pageSizeModel = computed({
  get: () => String(props.pagination.pageSize),
  set: (value: string) => {
    props.table.setPagination({
      pageIndex: 0,
      pageSize: Number(value),
    })
  },
})

const currentPage = computed(() => props.pagination.pageIndex)
const totalPages = computed(() => props.table.getPageCount())

</script>

<template>
  <div class="flex flex-col sm:flex-row items-center justify-between px-2 space-y-2 sm:space-y-0">
    <!-- Page info and size selector -->
    <div class="flex items-center space-x-2 text-sm text-muted-foreground">
      <Select v-model="pageSizeModel">
        <SelectTrigger class="w-20">
          {{ pageSizeModel }}
        </SelectTrigger>

        <span>Page {{ currentPage + 1 }} of {{ totalPages }}</span>
        <SelectContent>
          <SelectItem v-for="size in props.pageSizeOptions" :key="size" :value="String(size)">
            {{ size }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Navigation buttons -->
    <div class="flex items-center space-x-1">
      <div class="ml-auto flex items-center gap-2 lg:ml-0">
        <Button
          variant="outline"
          class="hidden h-8 w-8 p-0 lg:flex"
          :disabled="!props.table.getCanPreviousPage()"
          @click="props.table.setPageIndex(0)"
        >
          <span class="sr-only">Go to first page</span>
          <ChevronsLeft />
        </Button>

        <Button
          variant="outline"
          class="size-8"
          size="icon"
          :disabled="!props.table.getCanPreviousPage()"
          @click="props.table.previousPage()"
        >
          <span class="sr-only">Go to previous page</span>
          <ChevronLeft />
        </Button>

        <Button
          variant="outline"
          class="size-8"
          size="icon"
          :disabled="!props.table.getCanNextPage()"
          @click="props.table.nextPage()"
        >
          <span class="sr-only">Go to next page</span>
          <ChevronRight />
        </Button>

        <Button
          variant="outline"
          class="hidden size-8 lg:flex"
          size="icon"
          :disabled="!props.table.getCanNextPage()"
          @click="props.table.setPageIndex(props.table.getPageCount() - 1)"
        >
          <span class="sr-only">Go to last page</span>
          <ChevronsRight />
        </Button>
      </div>
    </div>
  </div>
</template>
