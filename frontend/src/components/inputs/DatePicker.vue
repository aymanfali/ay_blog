<script setup lang="ts">
import { ref, watch, type Ref } from 'vue'
import type { DateValue } from '@internationalized/date'
import { getLocalTimeZone, DateFormatter } from '@internationalized/date'

import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover'
import { CalendarIcon } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

// Props
const props = defineProps<{
  modelValue?: DateValue | null
  placeholder?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: DateValue | null): void
}>()

const date = ref(props.modelValue ?? null) as Ref<DateValue | null>
const df = new DateFormatter('en-US', { dateStyle: 'long' })

watch(
  () => props.modelValue,
  (val) => {
    date.value = val ?? null
  },
)

const handleSelect = (selected: DateValue) => {
  date.value = selected
  emit('update:modelValue', selected)
}
</script>

<template>
  <Popover v-slot="{ close }">
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        :class="cn('w-60 justify-start text-left font-normal', !date && 'text-muted-foreground')"
      >
        <CalendarIcon class="mr-2 h-4 w-4" />
        {{ date ? df.format(date.toDate(getLocalTimeZone())) : (placeholder ?? 'Pick a date') }}
      </Button>
    </PopoverTrigger>

    <PopoverContent class="w-auto p-0" align="start">
      <Calendar
        v-model="date"
        layout="month-and-year"
        initial-focus
        @update:model-value="
          (val) => {
            handleSelect(val)
            close()
          }
        "
      />
    </PopoverContent>
  </Popover>
</template>
