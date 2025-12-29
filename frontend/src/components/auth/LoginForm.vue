<script setup lang="ts">
import { ref, type HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import {
  Field,
  FieldDescription,
  FieldGroup,
  FieldLabel,
  FieldSeparator,
} from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { AuthService } from '@/api/services/auth.service'
import AppAlert from '../AppAlert.vue'
import { AxiosError } from 'axios'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)

// Alert state
const showAlert = ref(false)
const alertMessage = ref('')

const submit = async () => {
  loading.value = true
  showAlert.value = false
  try {
    const user = await AuthService.login({
      email: email.value,
      password: password.value,
    })

    // login successful → hide alert
    showAlert.value = false
    alertMessage.value = ''

    authStore.setAuth(user)
    router.replace({ name: 'dashboard.home' })
  } catch (err: unknown) {
    let message = 'Login failed'

    if (err instanceof AxiosError) {
      message = err.response?.data?.message ?? message
    }

    alertMessage.value = message
    showAlert.value = true
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <form @submit.prevent="submit" :class="cn('flex flex-col gap-6', props.class)">
    <FieldGroup>
      <div class="flex flex-col items-center gap-1 text-center">
        <h1 class="text-2xl font-bold">{{ $t('login_title') }}</h1>
        <p class="text-muted-foreground text-sm text-balance">
          {{ $t('login_subtitle') }}
        </p>
      </div>
      <Field>
        <FieldLabel for="email"> {{ $t('email') }} </FieldLabel>
        <Input id="email" type="email" v-model="email" placeholder="you@example.com" required />
      </Field>
      <Field>
        <div class="flex items-center">
          <FieldLabel for="password"> {{ $t('password') }} </FieldLabel>
          <router-link to="#" class="ms-auto text-sm underline-offset-4 hover:underline">
            {{ $t('forgot_password') }}
          </router-link>
        </div>
        <Input id="password" type="password" v-model="password" required />
      </Field>
      <AppAlert :show="showAlert" variant="destructive" :message="alertMessage" title="Error" />

      <Field>
        <Button type="submit" :disabled="loading">
          <span v-if="!loading">{{ $t('login_title') }}</span>
          <span v-else>{{ $t('logging_in') }}</span>
        </Button>
      </Field>
      <FieldSeparator>{{ $t('continue_with') }}</FieldSeparator>
      <Field>
        <div class="flex justify-center gap-2">
          <Button variant="outline" type="button" class="w-1/2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path
                d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"
                fill="currentColor"
              />
            </svg>
          </Button>
          <Button variant="outline" type="button" class="w-1/2">
            <svg xmlns="http://www.w3.org/2000/svg" width="256" height="262" viewBox="0 0 256 262">
              <path
                fill="#4285f4"
                d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622l38.755 30.023l2.685.268c24.659-22.774 38.875-56.282 38.875-96.027"
              />
              <path
                fill="#34a853"
                d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055c-34.523 0-63.824-22.773-74.269-54.25l-1.531.13l-40.298 31.187l-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1"
              />
              <path
                fill="#fbbc05"
                d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82c0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602z"
              />
              <path
                fill="#eb4335"
                d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0C79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251"
              />
            </svg>
          </Button>
        </div>
        <FieldDescription class="text-center">
          {{ $t('have_no_account') }}
          <router-link to="/auth/register">{{ $t('signup_title') }}</router-link>
        </FieldDescription>
      </Field>
    </FieldGroup>
  </form>
</template>
