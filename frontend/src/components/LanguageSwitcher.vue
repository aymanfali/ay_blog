<script setup lang="ts">
import { ref } from 'vue'
import { useI18nLocaleSwitcher } from '@/locales/useI18nLocaleSwitcher'
import { LOCALE_META, SUPPORTED_LOCALES, type Locale } from '@/locales/locale'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from './ui/dropdown-menu'
import { ChevronsUpDown, Languages } from 'lucide-vue-next'
import { Button } from './ui/button'

const { currentLocale, switchLocale } = useI18nLocaleSwitcher()
const open = ref(false)

const handleSwitch = async (locale: Locale) => {
  await switchLocale(locale)
  open.value = false
}
</script>

<template>
  <DropdownMenu v-model:open="open">
    <DropdownMenuTrigger as-child>
      <Button
        variant="outline"
        class="px-4 py-2 border rounded-lg flex items-center justify-between"
      >
        <Languages class="md:me-2" />
        <span class="hidden md:block">{{ LOCALE_META[currentLocale].name }}</span>
        <ChevronsUpDown class="hidden md:block" />
      </Button>
    </DropdownMenuTrigger>

    <DropdownMenuContent side="bottom" align="end">
      <DropdownMenuItem
        v-for="locale in SUPPORTED_LOCALES"
        :key="locale"
        @click="handleSwitch(locale)"
        :class="locale === currentLocale ? 'bg-accent text-accent-foreground font-semibold' : ''"
      >
        {{ LOCALE_META[locale].name }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
