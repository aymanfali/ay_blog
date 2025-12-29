import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { setLanguage } from './index'
import type { Locale } from './locale'

export function useI18nLocaleSwitcher() {
  const { locale } = useI18n()

  const currentLocale = computed(() => locale.value as Locale)

  const switchLocale = async (lang: Locale) => {
    await setLanguage(lang)
  }

  return {
    currentLocale,
    switchLocale,
  }
}
