<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div
    data-slot="card"
    :class="
      cn(
        'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-50 flex flex-col gap-6 rounded-xl border border-slate-200 dark:border-slate-700 py-6 shadow-sm',
        props.class,
      )
    "
  >
    <slot />
  </div>
</template>
