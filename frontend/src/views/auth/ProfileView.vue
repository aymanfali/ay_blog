<template>
  <div class="max-w-4xl mx-auto px-4">
    <!-- Profile Header -->
    <div class="relative rounded-xl bg-muted">
      <!-- Banner -->
      <div class="relative h-52 rounded-md shadow-2xl">
        <img
          v-if="bannerPreview"
          :src="bannerPreview"
          alt="Banner"
          class="w-full h-full object-cover"
        />

        <FileUploadButton
          accept="image/png,image/jpeg,image/webp"
          buttonClass="absolute bottom-4 right-4 bg-white/90 hover:bg-white rounded-full p-2 shadow"
          @update:file="onBannerSelected"
        />
      </div>

      <!-- Avatar -->
      <div class="absolute -bottom-20 left-6">
        <div class="relative flex flex-col items-center gap-2">
          <div class="relative">
            <img
              :src="avatarPreview || '/avatar-placeholder.png'"
              alt="Avatar"
              class="h-32 w-32 rounded-full shadow-2xl object-cover bg-muted border border-gray-200"
            />

            <FileUploadButton
              accept="image/png,image/jpeg,image/webp"
              buttonClass="absolute -bottom-2 right-4 bg-white/90 hover:bg-white rounded-full p-2 shadow"
              @update:file="onAvatarSelected"
            />
          </div>

          <!-- Avatar Update Button -->
          <Button v-if="avatarDirty" size="sm" :disabled="avatarUploading" @click="updateAvatar">
            {{ avatarUploading ? $t('saving') : $t('update_avatar') }}
          </Button>
        </div>
      </div>
    </div>

    <!-- Profile Form -->
    <form @submit.prevent="updateProfile" class="mt-28 rounded-xl p-6 space-y-6">
      <h1 class="text-xl font-semibold">
        {{ $t('profile') }}
      </h1>

      <!-- Name & Email -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <Label>{{ $t('name') }}</Label>
          <Input v-model="form.name" />
        </div>

        <div>
          <Label>{{ $t('email') }}</Label>
          <Input v-model="form.email" readonly />
        </div>
      </div>

      <!-- Bio -->
      <div>
        <Label>{{ $t('bio') }}</Label>
        <Textarea v-model="form.bio" rows="4" />
      </div>

      <!-- Password -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <Label>{{ $t('password') }}</Label>
          <PasswordInput v-model="form.password" />
        </div>

        <div>
          <Label>{{ $t('confirm_password') }}</Label>
          <PasswordInput v-model="form.password_confirmation" />
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end">
        <Button type="submit" :disabled="loading">
          {{ loading ? $t('saving') : $t('save') }}
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { AuthService } from '@/api/services/auth.service'

import Input from '@/components/ui/input/Input.vue'
import Textarea from '@/components/ui/textarea/Textarea.vue'
import Button from '@/components/ui/button/Button.vue'
import Label from '@/components/ui/label/Label.vue'
import FileUploadButton from '@/components/inputs/FileUploadButton.vue'
import PasswordInput from '@/components/inputs/PasswordInput.vue'

const auth = useAuthStore()

/* Files */
const avatarFile = ref<File | null>(null)
const bannerFile = ref<File | null>(null)

/* State */
const avatarDirty = ref(false)
const avatarUploading = ref(false)

/* Previews */
const avatarPreview = ref<string | null>(auth.user?.avatar ?? null)
const bannerPreview = ref<string | null>(auth.user?.banner ?? null)

/* Fixed dimensions */
const AVATAR_WIDTH = 128
const AVATAR_HEIGHT = 128
const BANNER_WIDTH = 1584
const BANNER_HEIGHT = 396

/* Validate image dimensions */
function validateImageSize(file: File, width: number, height: number): Promise<boolean> {
  return new Promise((resolve) => {
    const img = new Image()
    img.src = URL.createObjectURL(file)
    img.onload = () => resolve(img.width === width && img.height === height)
  })
}

/* Handlers */
const onAvatarSelected = async (file?: File | File[]) => {
  if (!file) return
  if (Array.isArray(file)) file = file[0]

  if (!(file instanceof File)) return

  const valid = await validateImageSize(file, AVATAR_WIDTH, AVATAR_HEIGHT)
  if (!valid) {
    alert(`Avatar must be exactly ${AVATAR_WIDTH}x${AVATAR_HEIGHT}px`)
    return
  }

  avatarFile.value = file
  avatarPreview.value = URL.createObjectURL(file)
  avatarDirty.value = true
}

const onBannerSelected = async (file?: File | File[]) => {
  if (!file) return
  if (Array.isArray(file)) file = file[0]

  if (!(file instanceof File)) return

  const valid = await validateImageSize(file, BANNER_WIDTH, BANNER_HEIGHT)
  if (!valid) {
    alert(`Banner must be exactly ${BANNER_WIDTH}x${BANNER_HEIGHT}px`)
    return
  }

  bannerFile.value = file
  bannerPreview.value = URL.createObjectURL(file)
}

/* Avatar Update */
const updateAvatar = async () => {
  if (!avatarFile.value) return

  avatarUploading.value = true
  try {
    const formData = new FormData()
    formData.append('avatar', avatarFile.value)

    const updatedUser = await AuthService.updateProfile(formData)
    auth.setAuth(updatedUser)

    avatarPreview.value = `${updatedUser.avatar}?t=${Date.now()}` // force refresh
    avatarDirty.value = false
    avatarFile.value = null
  } finally {
    avatarUploading.value = false
  }
}

/* Form */
const form = reactive({
  name: auth.user?.name || '',
  email: auth.user?.email || '',
  bio: auth.user?.bio || '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)

/* Profile Update */
const updateProfile = async () => {
  loading.value = true
  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('bio', form.bio)

    if (form.password) {
      formData.append('password', form.password)
      formData.append('password_confirmation', form.password_confirmation)
    }

    if (bannerFile.value) {
      formData.append('banner', bannerFile.value)
    }

    const updatedUser = await AuthService.updateProfile(formData)
    auth.setAuth(updatedUser)

    bannerPreview.value = `${updatedUser.banner}?t=${Date.now()}` // force refresh
    form.password = ''
    form.password_confirmation = ''
  } finally {
    loading.value = false
  }
}
</script>
