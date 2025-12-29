import {
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  getFilteredRowModel,
  getExpandedRowModel,
  getFacetedRowModel,
  getFacetedUniqueValues,
  getFacetedMinMaxValues,
  useVueTable,
} from '@tanstack/vue-table'
import { ref, watch } from 'vue'
import { valueUpdater } from '@/lib/utils'

export function useDataTable<TData>(data: TData[], columns: any[], key = 'data-table') {
  const sorting = ref([])
  const columnFilters = ref([])
  const columnVisibility = ref({})
  const rowSelection = ref({})
  const expanded = ref({})
  const globalFilter = ref('')
  const columnPinning = ref({ left: [], right: [] })

  // restore state
  const saved = localStorage.getItem(key)
  if (saved) {
    Object.assign(
      { sorting, columnFilters, columnVisibility, rowSelection, expanded, columnPinning },
      JSON.parse(saved),
    )
  }

  const table = useVueTable({
    data,
    columns,
    state: {
      get sorting() {
        return sorting.value
      },
      get columnFilters() {
        return columnFilters.value
      },
      get columnVisibility() {
        return columnVisibility.value
      },
      get rowSelection() {
        return rowSelection.value
      },
      get expanded() {
        return expanded.value
      },
      get globalFilter() {
        return globalFilter.value
      },
      get columnPinning() {
        return columnPinning.value
      },
    },

    onSortingChange: (u) => valueUpdater(u, sorting),
    onColumnFiltersChange: (u) => valueUpdater(u, columnFilters),
    onColumnVisibilityChange: (u) => valueUpdater(u, columnVisibility),
    onRowSelectionChange: (u) => valueUpdater(u, rowSelection),
    onExpandedChange: (u) => valueUpdater(u, expanded),
    onGlobalFilterChange: (u) => valueUpdater(u, globalFilter),
    onColumnPinningChange: (u) => valueUpdater(u, columnPinning),

    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getExpandedRowModel: getExpandedRowModel(),

    // faceted filtering
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getFacetedMinMaxValues: getFacetedMinMaxValues(),
  })

  watch(
    () => table.getState(),
    (state) => localStorage.setItem(key, JSON.stringify(state)),
    { deep: true },
  )

  return { table }
}
