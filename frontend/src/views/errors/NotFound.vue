<script setup lang="ts">
import { ArrowLeftToLine, Home, SearchIcon } from 'lucide-vue-next'
import {
  Empty,
  EmptyContent,
  EmptyDescription,
  EmptyHeader,
  EmptyTitle,
} from '@/components/ui/empty'
import { Button } from '@/components/ui/button'
import type { Separator } from '@/components/ui/separator'
import { useRouter } from 'vue-router'

const router = useRouter()

function goBack() {
  router.back()
}
</script>

<template>
  <Empty class="flex items-center min-h-screen">
    <EmptyHeader>
      <EmptyTitle>404 - Not Found</EmptyTitle>
      <EmptyDescription>
        The page you're looking for doesn't exist. Try searching for what you need below.
      </EmptyDescription>
    </EmptyHeader>
    <EmptyContent>
      <div class="flex gap-2">
        <Button variant="outline" @click="goBack" class="flex items-center gap-1">
          <ArrowLeftToLine class="me-2" />Go Back
        </Button>
        <Button variant="outline"
          ><RouterLink to="/" class="flex items-center gap-1"
            ><Home class="me-2" />Go Home</RouterLink
          ></Button
        >
      </div>
      <EmptyDescription> Need help? <a href="#">Contact support</a> </EmptyDescription>
    </EmptyContent>
  </Empty>
</template>
