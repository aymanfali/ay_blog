<script lang="ts" setup>
import { computed, type HTMLAttributes } from "vue"
import { ChevronRight } from "lucide-vue-next"
import { cn } from "@/lib/utils"
import { useI18nLocaleSwitcher } from "@/locales/useI18nLocaleSwitcher"

const { currentLocale } = useI18nLocaleSwitcher()

const iconLocaleRotate = computed(() => (currentLocale.value === 'ar' ? 'rotate-180' : ''))

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()
</script>

<template>
  <li
    data-slot="breadcrumb-separator"
    role="presentation"
    aria-hidden="true"
    :class="cn('[&>svg]:size-3.5', props.class)"
  >
    <slot>
      <ChevronRight :class="iconLocaleRotate" />
    </slot>
  </li>
</template>
