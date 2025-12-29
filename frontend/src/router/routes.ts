import type { RouteRecordRaw } from 'vue-router'
import { publicRoutes } from './publicRoutes'
import { dashboardRoutes } from './dashboardRoutes'
import { authRoutes } from './authRoutes'
import { errorRoutes } from './errorRoutes'

export const routes: RouteRecordRaw[] = [
  ...publicRoutes,
  ...authRoutes,
  ...dashboardRoutes, // protected routes
  ...errorRoutes, // error routes must be last
]

