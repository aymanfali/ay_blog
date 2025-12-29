import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

import './assets/main.css'

import { i18n, setLanguage } from './locales'
import { useAuthStore } from './stores/auth'

const app = createApp(App)

/**
 * 1. Install plugins first
 */
app.use(createPinia())
app.use(router)
app.use(i18n)

/**
 * 2. Apply persisted locale BEFORE mount
 */
await setLanguage(i18n.global.locale.value)

/**
 * 3. Global helpers
 */
app.config.globalProperties.$formatDate = (value: string, format: 'short' | 'long' = 'short') => {
  if (!value) return ''

  try {
    const normalized = value.replace(/\.\d{6}Z$/, 'Z')
    return i18n.global.d(new Date(normalized), format)
  } catch {
    return value
  }
}


const authStore = useAuthStore()
await authStore.initAuth() // restore auth from localStorage

app.mount('#app')
