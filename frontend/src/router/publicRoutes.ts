import ProfileView from '@/views/auth/ProfileView.vue'
import GuestLayout from '@/views/GuestLayout.vue'
import HomeView from '@/views/HomeView.vue'
import type { RouteRecordRaw } from 'vue-router'

export const publicRoutes: readonly RouteRecordRaw[] = [
  {
    path: '/',
    component: GuestLayout,
    meta: {
      layout: 'public',
      requiresAuth: false,
    },
    children: [
      {
        path: '/profile',
        name: 'auth.profile',
        component: ProfileView,
        meta: {
          title: 'Profile',
        },
      },
      {
        path: '',
        name: 'home',
        component: HomeView,
        meta: { title: 'Home' },
      },
    ],
  },
]
