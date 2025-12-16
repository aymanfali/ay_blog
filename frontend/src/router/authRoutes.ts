import AuthLayout from '@/views/auth/layout/AuthLayout.vue'
import LoginView from '@/views/auth/LoginView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'
import type { RouteRecordRaw } from 'vue-router'

export const authRoutes: readonly RouteRecordRaw[] = [
  {
    path: '/auth',
    redirect: { name: 'auth.login' },
    component: AuthLayout,
    meta: {
      layout: 'auth',
      guestOnly: true,
    },
    children: [
      {
        path: 'login',
        name: 'auth.login',
        component: LoginView,
        meta: {
          title: 'Login',
        },
      },
      {
        path: 'register',
        name: 'auth.register',
        component: RegisterView,
        meta: {
          title: 'Register',
        },
      },
    ],
  },
]
