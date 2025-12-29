export interface ApiErrorResponse<TData = unknown> {
  message?: string
  errors?: Record<string, string[]>
  locale?: string
  meta?: {
    timestamp: string
    status: number
  }
  success?: boolean
  data?: TData
}
