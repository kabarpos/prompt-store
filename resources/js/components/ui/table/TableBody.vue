<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <tbody
    data-slot="table-body"
    :class="cn('[&_tr:last-child]:border-0 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-50', props.class)"
  >
    <slot />
  </tbody>
</template>
