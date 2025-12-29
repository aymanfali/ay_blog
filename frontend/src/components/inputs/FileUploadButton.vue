<template>
  <div>
    <Button variant="outline" :class="buttonClass" @click="triggerInput" class="hover:cursor-pointer">
      <slot>
        <upload class="h-8 w-8" />
      </slot>
    </Button>

    <input
      ref="inputRef"
      type="file"
      :accept="accept"
      :multiple="multiple"
      class="hidden"
      @change="onFileChange"
    />
  </div>
</template>

<script setup lang="ts">
import { Upload } from 'lucide-vue-next'
import { ref } from 'vue'
import Button from '@/components/ui/button/Button.vue'

const props = defineProps<{
  accept?: string
  multiple?: boolean
  buttonClass?: string
}>()

const emit = defineEmits<{
  (e: 'update:file', file: File | File[] | undefined): void
}>()

const inputRef = ref<HTMLInputElement | null>(null)

const triggerInput = () => inputRef.value?.click()

const onFileChange = (e: Event) => {
  const files = (e.target as HTMLInputElement).files
  if (!files || files.length === 0) return

  if (props.multiple) {
    emit('update:file', Array.from(files))
  } else {
    emit('update:file', files[0])
  }

  // Reset input to allow same file selection again
  ;(e.target as HTMLInputElement).value = ''
}
</script>
