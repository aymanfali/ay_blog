import type { User } from './user'

export interface LoginPayload {
  email: string
  password: string
}

export interface LoginResponse {
  success: boolean
  message?: string
  data: {
    user: User
    token: string
  }
}

export interface ProfileResponse {
  success: boolean
  message?: string
  data: User
}

export interface UpdateProfileResponse {
  success: boolean
  message?: string
  data: User
}
