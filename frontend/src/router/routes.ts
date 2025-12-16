import type { RouteRecordRaw } from 'vue-router'
import { publicRoutes } from './publicRoutes'
import { dashboardRoutes } from './dashboardRoutes'
import { authRoutes } from './authRoutes'

export const routes: RouteRecordRaw[] = [
  ...publicRoutes,
  ...authRoutes,
  ...dashboardRoutes, // keep protected routes last
]

