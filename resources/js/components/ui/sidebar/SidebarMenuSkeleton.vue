<script setup lang="ts">
import { cn } from '@/lib/utils'
import { Skeleton } from '@/components/ui/skeleton'
import { computed, type HTMLAttributes } from 'vue'

const props = defineProps<{
  showIcon?: boolean
  class?: HTMLAttributes['class']
  width?: string
  icon?: string
}>()

const width = computed(() => {
  return `${Math.floor(Math.random() * 40) + 50}%`;
})
</script>

<template>
  <div
    data-slot="sidebar-menu-skeleton"
    data-sidebar="menu-skeleton"
    :class="cn('flex h-9 w-full items-center gap-2 px-2 py-1', props.class)"
  >
    <div :class="props.icon">
      <div class="h-4 w-4 animate-pulse rounded-md bg-secondary-200 dark:bg-secondary-700" />
    </div>

    <Skeleton
      class="h-4 flex-1"
      data-sidebar="menu-skeleton-text"
      :style="{ maxWidth: width }"
    />
  </div>
</template>
