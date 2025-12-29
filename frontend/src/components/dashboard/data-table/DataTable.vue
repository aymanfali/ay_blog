<script setup lang="ts" generic="TData">
import {
  useVueTable,
  getCoreRowModel,
  getSortedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  type SortingState,
  type ColumnFiltersState,
  type PaginationState,
  type ColumnDef,
} from '@tanstack/vue-table'
import { ref, h, computed, useSlots } from 'vue'

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import DataTablePagination from './DataTablePagination.vue'
import type { DataTableProps } from './types'
import { FlexRender } from '@tanstack/vue-table'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue,
} from '@/components/ui/select'
import { ChevronDown, ChevronsUpDown, ChevronUp } from 'lucide-vue-next'
import DataTableSkeleton from './DataTableSkeleton.vue'

const props = defineProps<DataTableProps<TData>>()

// Sorting / Filtering state
const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])

// ✅ Reactive Pagination state
const pagination = ref<PaginationState>({
  pageIndex: 0,
  pageSize: props.defaultPageSize ?? 5,
})

const rowSelection = ref<Record<string, boolean>>({})

const selectedCount = computed(() => table.getSelectedRowModel().rows.length)
const slots = useSlots()

const hasActions = computed(() => !!slots['row-actions'])

const getSortIcon = (column: any) => {
  if (!column.getCanSort()) return null
  if (!column.getIsSorted()) return ChevronsUpDown
  return column.getIsSorted() === 'asc' ? ChevronUp : ChevronDown
}

const tableColumns = computed<ColumnDef<TData>[]>(() => {
  const cols: ColumnDef<TData>[] = [
    {
      id: 'select',
      enableSorting: false,
      enableHiding: false,
      header: ({ table }) =>
        h(Checkbox, {
          modelValue:
            table.getIsAllRowsSelected() || (table.getIsSomeRowsSelected() && 'indeterminate'),
          'onUpdate:modelValue': (value: boolean) => table.toggleAllRowsSelected(!!value),
          'aria-label': 'Select all',
          onClick: (e: Event) => e.stopPropagation(),
        }),
      cell: ({ row }) =>
        h(Checkbox, {
          modelValue: row.getIsSelected(),
          'onUpdate:modelValue': (value: boolean) => row.toggleSelected(!!value),
          'aria-label': 'Select row',
          onClick: (e: Event) => e.stopPropagation(),
        }),
    },
    ...props.columns,
    ...(hasActions
      ? [
          {
            id: 'actions',
            enableSorting: false,
            header: 'Actions',
            cell: () => null,
          },
        ]
      : []),
  ]

  return cols
})

const table = useVueTable({
  enableRowSelection: true,

  getRowId: (row: any) => String(row.id),

  get data() {
    return props.data
  },

  get columns() {
    return tableColumns.value
  },

  state: {
    get sorting() {
      return sorting.value
    },
    get columnFilters() {
      return columnFilters.value
    },
    get pagination() {
      return pagination.value
    },
    get rowSelection() {
      return rowSelection.value
    },
  },

  onPaginationChange: (updater) => {
    pagination.value = typeof updater === 'function' ? updater(pagination.value) : updater
  },

  onSortingChange: (updater) => {
    sorting.value = typeof updater === 'function' ? updater(sorting.value) : updater
  },

  onColumnFiltersChange: (updater) => {
    columnFilters.value = typeof updater === 'function' ? updater(columnFilters.value) : updater
  },

  onRowSelectionChange: (updater) => {
    rowSelection.value = typeof updater === 'function' ? updater(rowSelection.value) : updater
  },

  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
})
</script>

<template>
  <div class="space-y-4">
    <div class="overflow-hidden rounded-lg border">
      <div class="flex flex-wrap items-center gap-3 m-3">
        <template v-for="column in table.getAllLeafColumns()" :key="column.id">
          <template v-if="column.columnDef.meta?.filter">
            <!-- Text Filter -->
            <Input
              v-if="column.columnDef.meta.filter.type === 'text'"
              :placeholder="column.columnDef.meta.filter.placeholder"
              class="w-48"
              :model-value="column.getFilterValue() ?? ''"
              @update:model-value="column.setFilterValue($event)"
            />

            <!-- Select Filter -->
            <Select
              v-else
              :model-value="column.getFilterValue() ?? ''"
              @update:model-value="column.setFilterValue"
            >
              <SelectTrigger class="w-40">
                <SelectValue placeholder="Filter" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="opt in column.columnDef.meta.filter.options"
                  :key="opt.value"
                  :value="opt.value"
                >
                  {{ opt.label }}
                </SelectItem>
              </SelectContent>
            </Select>
          </template>
        </template>

        <Button
          v-if="table.getState().columnFilters.length"
          variant="ghost"
          size="sm"
          @click="table.resetColumnFilters()"
        >
          Reset filters
        </Button>
      </div>

      <Table>
        <TableHeader class="bg-muted sticky top-0 z-10">
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead
              v-for="header in headerGroup.headers"
              :key="header.id"
              class="cursor-pointer select-none bg-muted"
              @click="
                header.column.getCanSort() && header.column.getToggleSortingHandler()?.($event)
              "
            >
              <div class="flex">
                <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                <span v-if="header.column.getCanSort()" class="ml-1 text-gray-400">
                  <component :is="getSortIcon(header.column)" class="w-4 h-4" />
                </span>
              </div>
            </TableHead>
          </TableRow>
        </TableHeader>

        <DataTableSkeleton
          v-if="props.loading"
          :rows="pagination.pageSize"
          :cols="table.getAllLeafColumns().length"
        />

        <TableBody v-else>
          <TableRow
            v-for="row in table.getRowModel().rows"
            :key="row.id"
            :data-state="row.getIsSelected() ? 'selected' : undefined"
            class="transition-colors hover:bg-muted/40"
          >
            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
              <FlexRender
                v-if="cell.column.id !== 'actions'"
                :render="cell.column.columnDef.cell"
                :props="cell.getContext()"
              />

              <template v-else>
                <slot name="row-actions" :row="row.original ?? null" />
              </template>
            </TableCell>
          </TableRow>

          <TableRow v-if="!table.getRowModel().rows.length">
            <TableCell :colspan="table.getAllColumns().length" class="text-center py-6">
              No results found.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <div class="text-gray-400">
      <span v-if="selectedCount">
        {{ selectedCount }} row<span v-if="selectedCount !== 1">s</span> selected
      </span>
    </div>

    <!-- Pagination -->
    <DataTablePagination
      v-if="pagination"
      :table="table"
      :pagination="pagination"
      :pageSizeOptions="props.pageSizeOptions ?? [5, 10, 20, 50]"
    />
  </div>
</template>
