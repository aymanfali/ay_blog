import NotFound from '@/views/errors/NotFound.vue'
import type { RouteRecordRaw } from 'vue-router'

export const errorRoutes: RouteRecordRaw[] = [
  {
    path: '/:pathMatch(.*)*',
    component: NotFound,
  },
]
