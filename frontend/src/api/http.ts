import api from './axios'
import type { AxiosError, AxiosResponse, InternalAxiosRequestConfig } from 'axios'
import type { ApiErrorResponse } from '@/types/api-error'
import { useAuthStore } from '@/stores/auth'

/**
 * Request Interceptor
 */
api.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = localStorage.getItem('access_token')

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    return config
  },
  (error) => Promise.reject(error),
)

/**
 * Response Interceptor
 */
api.interceptors.response.use(
  (response: AxiosResponse) => response,
  async (error: AxiosError<ApiErrorResponse>) => {
    // Network / CORS / server down
    if (!error.response) {
      return Promise.reject(error)
    }

    // Optional global 401 handling
    if (error.response.status === 401) {
      localStorage.removeItem('access_token')
    }

    if (
      error.response?.status === 403 &&
      error.response.data.message === 'Your account is inactive. Please contact support.'
    ) {
      // Clear token, redirect to login
      const authStore = useAuthStore() // ✅ get store instance
      await authStore.logout()
    }

    return Promise.reject(error)
  },
)

export default api
