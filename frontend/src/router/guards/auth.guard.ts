import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'
import { useAuthStore } from '@/stores/auth' // Pinia or equivalent

export async function authGuard(
  to: RouteLocationNormalized,
  _from: RouteLocationNormalized,
  next: NavigationGuardNext,
) {
  const auth = useAuthStore()

  if (!auth.isAuthenticated) {
    await auth.initAuth()
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: 'auth.login' })
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return next({ name: 'dashboard.home' })
  }

  next()
}
