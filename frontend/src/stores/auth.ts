import { defineStore } from 'pinia'
import { ref } from 'vue'
import { AuthService } from '@/api/services/auth.service'
import type { User } from '@/types/user'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const isAuthenticated = ref(false)
  const router = useRouter()

  const setAuth = (u: User) => {
    user.value = u
    isAuthenticated.value = true
  }

  const logout = async () => {
    await AuthService.logout()
    user.value = null
    isAuthenticated.value = false
    router.push('/auth/login')
  }

  const initAuth = async () => {
    const token = localStorage.getItem('access_token')
    if (!token) return
    const u = await AuthService.profile()
    setAuth(u)
  }

  return { user, isAuthenticated, setAuth, logout, initAuth }
})
