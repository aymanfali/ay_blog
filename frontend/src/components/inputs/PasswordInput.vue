<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Eye, EyeOff } from 'lucide-vue-next'
import { z } from 'zod'

interface Props {
  modelValue?: string
  placeholder?: string
  disabled?: boolean
  id?: string
  name?: string
  error?: string | null // backend validation message
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'validation', error: string | null): void
}>()

const isVisible = ref(false)
const localValue = ref(props.modelValue ?? '')

// Watch for parent changes
watch(
  () => props.modelValue,
  (newVal) => {
    if (newVal !== localValue.value) localValue.value = newVal ?? ''
  },
)

const inputType = computed(() => (isVisible.value ? 'text' : 'password'))

// Zod schema
const passwordSchema = z.string().min(8, 'Password must be at least 8 characters')

// Update value & validate locally
function updateValue(event: Event) {
  localValue.value = (event.target as HTMLInputElement).value
  emit('update:modelValue', localValue.value)

  try {
    passwordSchema.parse(localValue.value)
    emit('validation', null)
  } catch (err) {
    if (err instanceof z.ZodError) {
      emit('validation', err.errors[0].message)
    }
  }
}
</script>

<template>
  <div class="relative w-full">
    <Input
      :id="props.id"
      :name="props.name"
      :type="inputType"
      v-model="localValue"
      :placeholder="props.placeholder"
      :disabled="props.disabled"
      class="pr-10"
      @input="updateValue"
      :aria-invalid="!!props.error"
    />
    <Button
      type="button"
      size="icon"
      variant="ghost"
      class="absolute top-1/2 right-2 -translate-y-1/2 p-1"
      @click="isVisible = !isVisible"
      :aria-label="isVisible ? 'Hide password' : 'Show password'"
    >
      <component :is="isVisible ? EyeOff : Eye" class="w-5 h-5" />
    </Button>
    <p v-if="props.error" class="mt-1 text-sm text-red-600">{{ props.error }}</p>
  </div>
</template>
