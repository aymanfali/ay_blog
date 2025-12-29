// components/data-table/types.ts
import type { ColumnDef } from '@tanstack/vue-table'

export interface DataTableProps<TData> {
  columns: ColumnDef<TData, any>[] // meta can still be ColumnMeta, cast per-column
  data: TData[]
  loading?: boolean
  pageSizeOptions?: number[]
  defaultPageSize?: number
}

export interface ColumnMeta {
  filter?: {
    type: 'text' | 'select'
    options?: { label: string; value: string }[]
    placeholder?: string
  }
}

export interface DataTableSlots<TData> {
  'row-actions'?: (props: { row: TData }) => any
}
