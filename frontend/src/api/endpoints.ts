import { resource } from './resources'

export const API = {
  AUTH: {
    LOGIN: `/api/${import.meta.env.VITE_API_VERSION}/auth/login`,
    LOGOUT: `/api/${import.meta.env.VITE_API_VERSION}/auth/logout`,
    PROFILE: `/api/${import.meta.env.VITE_API_VERSION}/auth/profile`,
    UPDATE_PROFILE: `/api/${import.meta.env.VITE_API_VERSION}/auth/profile`,
  },

  USERS: resource('users'),
  ROLES: resource('roles'),
  PRODUCTS: resource('products'),
  EMPLOYEES: resource('employees'),
}
