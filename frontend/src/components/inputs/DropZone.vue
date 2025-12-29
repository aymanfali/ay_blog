<script setup lang="ts">
import { ref } from 'vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { UPLOAD_CONFIG } from '@/config/upload.config'

const props = withDefaults(
  defineProps<{
    label?: string
    accept?: string
    maxFileSize?: number // MB
  }>(),
  {
    label: 'File',
    accept: '*/*',
    maxFileSize: UPLOAD_CONFIG.MAX_FILE_SIZE_MB,
  }
)


/* =======================
   Emits
======================= */
const emit = defineEmits<{
  (e: 'update:file', file: File): void
}>()

/* =======================
   State
======================= */
const fileInput = ref<HTMLInputElement | null>(null)
const isDragging = ref(false)
const fileName = ref<string | null>(null)
const error = ref<string | null>(null)

/* =======================
   Logic
======================= */
function openFileDialog() {
  error.value = null
  fileInput.value?.click()
}

function validateFile(file: File): boolean {
  if (props.accept !== '*/*' && !props.accept.split(',').includes(file.type)) {
    error.value = 'Invalid file type'
    return false
  }

  const maxBytes = props.maxFileSize * 1024 * 1024
  if (file.size > maxBytes) {
    error.value = `File size must not exceed ${props.maxFileSize}MB`
    return false
  }

  return true
}

function handleFile(file: File) {
  if (!validateFile(file)) return

  fileName.value = file.name
  error.value = null
  emit('update:file', file)
}

/* =======================
   Events
======================= */
function onFileChange(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) handleFile(file)
}

function onDrop(event: DragEvent) {
  event.preventDefault()
  isDragging.value = false

  const file = event.dataTransfer?.files?.[0]
  if (file) handleFile(file)
}
</script>

<template>
  <div class="grid w-full max-w-sm gap-2">
    <Label>{{ label }}</Label>

    <div
      class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 text-center transition cursor-pointer"
      :class="[
        isDragging
          ? 'border-primary bg-primary/5'
          : 'border-muted-foreground/30 hover:border-primary',
        error && 'border-destructive',
      ]"
      @click="openFileDialog"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop="onDrop"
    >
      <p class="text-sm font-medium">Drop file here or click to upload</p>

      <p class="mt-1 text-xs text-muted-foreground">
        Accepted: {{ accept }} — Max {{ maxFileSize }}MB
      </p>

      <p v-if="fileName" class="mt-2 text-xs">Selected: {{ fileName }}</p>

      <p v-if="error" class="mt-2 text-xs text-destructive">
        {{ error }}
      </p>
    </div>

    <Input ref="fileInput" type="file" class="hidden" :accept="accept" @change="onFileChange" />
  </div>
</template>
