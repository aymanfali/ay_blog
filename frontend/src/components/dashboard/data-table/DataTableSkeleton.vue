<!-- components/data-table/DataTableSkeleton.vue -->
<script setup lang="ts">
defineProps<{
  rows: number
  cols: number
}>()
</script>

<template>
  <tbody>
    <tr v-for="r in rows" :key="r" class="border-b">
      <td v-for="c in cols" :key="c" class="px-3 py-3">
        <div class="h-4 w-full rounded-md bg-muted/70 animate-pulse" />
      </td>
    </tr>
  </tbody>
</template>
