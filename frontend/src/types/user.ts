export interface User {
  id: number
  name: string
  email: string
  password: string
  password_confirmation: string
  avatar: string
  banner: string
  bio: string
  status: 'active' | 'inactive'
}
