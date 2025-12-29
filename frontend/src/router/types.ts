export interface AppRouteMeta {
  layout?: 'public' | 'auth' | 'dashboard'
  requiresAuth?: boolean
  guestOnly?: boolean
  permissions?: string[]
  title?: string
}
