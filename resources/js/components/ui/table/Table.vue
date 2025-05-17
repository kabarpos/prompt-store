<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div data-slot="table-container" class="relative w-full overflow-auto">
    <table 
      data-slot="table" 
      :class="cn('w-full caption-bottom text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-50 rounded-lg border border-slate-200 dark:border-slate-700', props.class)"
    >
      <slot />
    </table>
  </div>
</template>
