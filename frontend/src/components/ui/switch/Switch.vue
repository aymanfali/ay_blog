<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  modelValue: boolean
  activeText?: string
  inactiveText?: string
  label?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
}>()

const statusText = computed(() =>
  props.modelValue ? (props.activeText ?? 'Active') : (props.inactiveText ?? 'Inactive'),
)

const updateValue = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.checked)
}
</script>

<template>
  <div class="flex items-center justify-between w-full rounded-lg bg-muted px-3 py-2">
    <h1 class="text-sm font-semibold">
      {{ props.label }}
    </h1>

    <div class="flex items-center gap-3">
      <span class="ms-3 select-none">{{ statusText }}</span>

      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer" :checked="modelValue" @change="updateValue" />
        <!-- Track -->
        <div
          class="w-11 h-6 bg-gray-500 rounded-full peer-checked:bg-cyan-500 transition-colors duration-300"
        ></div>
        <!-- Thumb -->
        <div
          class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow ring-1 ring-gray-200 transform transition-transform duration-300 peer-checked:translate-x-5"
        ></div>
      </label>
    </div>
  </div>
</template>
