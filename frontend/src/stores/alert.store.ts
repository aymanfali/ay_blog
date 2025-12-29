import { defineStore } from 'pinia'

export type AlertVariant = 'default' | 'destructive'

export const useAlertStore = defineStore('alert', {
  state: () => ({
    show: false,
    title: '',
    message: '',
    variant: 'default' as AlertVariant,
  }),

  actions: {
    success(title: string, message: string) {
      this.show = true
      this.title = title
      this.message = message
      this.variant = 'default'
    },

    error(title: string, message: string) {
      this.show = true
      this.title = title
      this.message = message
      this.variant = 'destructive'
    },

    clear() {
      this.show = false
      this.title = ''
      this.message = ''
    },
  },
})
