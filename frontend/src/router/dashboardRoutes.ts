import DashboardView from '@/views/dashboard/DashboardView.vue'
import AuthLayout from '@/views/dashboard/layout/DashboardLayout.vue'
import CategoriesIndex from '@/views/dashboard/blog/categories/IndexView.vue'
import PostsIndex from '@/views/dashboard/blog/posts/IndexView.vue'
import type { RouteRecordRaw } from 'vue-router'

export const dashboardRoutes: readonly RouteRecordRaw[] = [
  {
    path: '/dashboard',
    component: AuthLayout,
    meta: {
      layout: 'dashboard',
      requiresAuth: true,
    },
    children: [
      {
        path: '',
        name: 'dashboard.home',
        component: DashboardView,
        meta: {
          title: 'Dashboard',
        },
      },
      {
        path: 'categories',
        name: 'dashboard.categories',
        component: CategoriesIndex,
        meta: {
          title: 'Categories',
        },
      },
      {
        path: 'posts',
        name: 'dashboard.posts',
        component: PostsIndex,
        meta: {
          title: 'Posts',
        },
      },
    ],
  },
]
