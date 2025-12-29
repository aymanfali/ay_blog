import api from '../http'
import { API } from '../endpoints'
import type {
  LoginPayload,
  LoginResponse,
  ProfileResponse,
  UpdateProfileResponse,
} from '@/types/api'
import type { User } from '@/types/user'

export const AuthService = {
  async login(payload: LoginPayload): Promise<void> {
    const { data } = await api.post<LoginResponse>(API.AUTH.LOGIN, payload)

    if (!data.success) {
      throw new Error(data.message || 'Login failed')
    }

    // token exists only in LoginResponse
    localStorage.setItem('access_token', data.data.token)
  },

  async profile(): Promise<User> {
    const { data } = await api.get<ProfileResponse>(API.AUTH.PROFILE)

    if (!data.success) {
      throw new Error(data.message || 'Failed to fetch profile')
    }

    return data.data
  },

  async updateProfile(formData: FormData): Promise<User> {
    const { data } = await api.put<UpdateProfileResponse>(API.AUTH.UPDATE_PROFILE, formData)
    if (!data.success) throw new Error(data.message || 'Failed to update profile')
    return data.data
  },

  async logout(): Promise<void> {
    await api.post(API.AUTH.LOGOUT)
    localStorage.removeItem('access_token')
  },
}
