import { createI18n } from 'vue-i18n'
import type { Locale } from './locale'
import { SUPPORTED_LOCALES } from './locale'

const resolveDefaultLocale = (): Locale => {
  if (typeof window === 'undefined') return 'en'

  const stored = localStorage.getItem('locale')
  return SUPPORTED_LOCALES.includes(stored as Locale)
    ? (stored as Locale)
    : 'en'
}

const defaultLocale = resolveDefaultLocale()

const i18n = createI18n({
  legacy: false,
  locale: defaultLocale,
  fallbackLocale: 'en',
  globalInjection: true,
  messages: {}, // lazy-loaded
  datetimeFormats: {
    ar: {
      short: { year: 'numeric', month: '2-digit', day: '2-digit' },
      long: {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long',
      },
    },
    en: {
      short: { year: 'numeric', month: '2-digit', day: '2-digit' },
      long: {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long',
      },
    },
  },
  numberFormats: {
    ar: {
      currency: { style: 'currency', currency: 'SAR' },
    },
    en: {
      currency: { style: 'currency', currency: 'USD' },
    },
  },
})

/**
 * Apply HTML language attributes once at bootstrap.
 * Subsequent changes must be handled by setLanguage().
 */
if (typeof document !== 'undefined') {
  document.documentElement.lang = defaultLocale
  document.documentElement.dir = defaultLocale === 'ar' ? 'rtl' : 'ltr'
}

export default i18n
