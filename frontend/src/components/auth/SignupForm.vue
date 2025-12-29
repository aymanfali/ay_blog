<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
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

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <form :class="cn('flex flex-col gap-6', props.class)">
    <FieldGroup>
      <div class="flex flex-col items-center gap-1 text-center">
        <h1 class="text-2xl font-bold">{{ $t('signup_title') }}</h1>
        <p class="text-muted-foreground text-sm text-balance">
          {{ $t("signup_subtitle") }}
        </p>
      </div>
      <Field>
        <FieldLabel for="name">{{ $t('name') }}</FieldLabel>
        <Input id="name" type="text" required />
      </Field>
      <Field>
        <FieldLabel for="email"> {{ $t('email') }} </FieldLabel>
        <Input id="email" type="email" placeholder="you@example.com" required />
        <FieldDescription>
          {{ $t('email_note') }}
        </FieldDescription>
      </Field>
      <Field>
        <FieldLabel for="password"> {{ $t('password') }} </FieldLabel>
        <Input id="password" type="password" required />
        <FieldDescription> {{ $t('password_rule') }} </FieldDescription>
      </Field>
      <Field>
        <FieldLabel for="confirm-password"> {{ $t('confirm_password') }} </FieldLabel>
        <Input id="confirm-password" type="password" required />
      </Field>
      <Field>
        <Button type="submit"> {{ $t('signup_title') }} </Button>
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
        <FieldDescription class="px-6 text-center">
          {{ $t('have_account') }} <router-link to="/auth/login">{{ $t('login_title') }}</router-link>
        </FieldDescription>
      </Field>
      <Field>
        <FieldDescription class="px-6 text-center">
          {{ $t('agree_to') }} <a href="#">{{ $t('terms_of_services') }}</a> &
          <a href="#">{{ $t("privacy_policy") }}</a>.
        </FieldDescription>
      </Field>
    </FieldGroup>
  </form>
</template>
