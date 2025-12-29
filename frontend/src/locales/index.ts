import i18n from './i18n'
import type { Locale } from './locale'
import { SUPPORTED_LOCALES, LOCALE_META } from './locale'

const loadedLanguages = new Set<Locale>()
const localeModules = import.meta.glob('./*.json')

export const setLanguage = async (locale: Locale): Promise<void> => {
  if (!SUPPORTED_LOCALES.includes(locale)) {
    console.warn(`Unsupported locale: ${locale}`)
    return
  }

  if (!loadedLanguages.has(locale)) {
    const loader = localeModules[`./${locale}.json`]
    if (!loader) return

    const messages = await loader()
    i18n.global.setLocaleMessage(locale, messages.default)
    loadedLanguages.add(locale)
  }

  i18n.global.locale.value = locale

  if (typeof window !== 'undefined') {
    localStorage.setItem('locale', locale)

    document.documentElement.lang = locale
    document.documentElement.dir = LOCALE_META[locale].dir

    window.dispatchEvent(
      new CustomEvent<Locale>('language-changed', { detail: locale })
    )
  }
}

export { default as i18n } from './i18n'
