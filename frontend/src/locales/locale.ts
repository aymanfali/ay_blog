export const SUPPORTED_LOCALES = ['en', 'ar'] as const
export type Locale = (typeof SUPPORTED_LOCALES)[number]

export const LOCALE_META: Record<Locale, { dir: 'ltr' | 'rtl'; name: string }> = {
  en: { dir: 'ltr', name: 'English' },
  ar: { dir: 'rtl', name: 'العربية' },
}
