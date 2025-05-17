<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <tr
    data-slot="table-row"
    :class="cn('border-b border-slate-200/60 dark:border-slate-700/60 hover:bg-primary-50/50 dark:hover:bg-primary-900/10 transition-colors', props.class)"
  >
    <slot />
  </tr>
</template>
