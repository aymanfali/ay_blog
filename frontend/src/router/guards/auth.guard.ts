// src/router/guards/auth.guard.ts
import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'
import { useAuthStore } from '@/stores/auth' // Pinia or equivalent

export function authGuard(
  to: RouteLocationNormalized,
  _from: RouteLocationNormalized,
  next: NavigationGuardNext,
) {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next({ name: 'auth.login' })
    return
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    next({ name: 'dashboard.home' })
    return
  }

  next()
}
