<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div
    data-slot="sidebar-header"
    :class="cn('flex flex-col gap-2 p-2 border-b border-slate-200 dark:border-slate-700', props.class)"
  >
    <slot />
  </div>
</template>
