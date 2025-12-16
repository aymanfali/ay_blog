<script setup lang="ts">
import PublicFooter from '@/components/public/PublicFooter.vue'
import PublicHeader from '@/components/public/PublicHeader.vue'
import { RouterView } from 'vue-router'
</script>

<template>
  <PublicHeader />
  <div class="p-5 mt-16 max-w-360 mx-auto">
    <RouterView />
  </div>
  <PublicFooter />
</template>
