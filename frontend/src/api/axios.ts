import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  timeout: Number(import.meta.env.VITE_API_TIMEOUT),
  withCredentials: true,

  headers: {
    Accept: 'application/json',
  },
})

export default api
