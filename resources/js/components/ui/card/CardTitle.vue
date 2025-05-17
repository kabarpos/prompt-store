<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div
    data-slot="card-title"
    :class="cn('leading-none text-lg font-medium text-slate-900 dark:text-slate-50', props.class)"
  >
    <slot />
  </div>
</template>
