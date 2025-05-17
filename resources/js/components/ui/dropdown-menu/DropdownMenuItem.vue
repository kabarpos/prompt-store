<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { reactiveOmit } from '@vueuse/core'
import { DropdownMenuItem, type DropdownMenuItemProps, useForwardProps } from 'reka-ui'

const props = withDefaults(defineProps<DropdownMenuItemProps & {
  class?: HTMLAttributes['class']
  inset?: boolean
  variant?: 'default' | 'destructive' | 'primary' | 'success' | 'warning'
}>(), {
  variant: 'default',
})

const delegatedProps = reactiveOmit(props, 'inset', 'variant')

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <DropdownMenuItem
    data-slot="dropdown-menu-item"
    :data-inset="inset ? '' : undefined"
    :data-variant="variant"
    v-bind="forwardedProps"
    :class="cn(`
      focus:bg-slate-100 dark:focus:bg-slate-800 focus:text-slate-900 dark:focus:text-slate-50 
      data-[variant=destructive]:text-danger-700 dark:data-[variant=destructive]:text-danger-400 
      data-[variant=destructive]:focus:bg-danger-50 dark:data-[variant=destructive]:focus:bg-danger-950 
      data-[variant=destructive]:focus:text-danger-900 dark:data-[variant=destructive]:focus:text-danger-300
      data-[variant=primary]:text-primary-700 dark:data-[variant=primary]:text-primary-400 
      data-[variant=primary]:focus:bg-primary-50 dark:data-[variant=primary]:focus:bg-primary-950 
      data-[variant=primary]:focus:text-primary-900 dark:data-[variant=primary]:focus:text-primary-300
      data-[variant=success]:text-success-700 dark:data-[variant=success]:text-success-400 
      data-[variant=success]:focus:bg-success-50 dark:data-[variant=success]:focus:bg-success-950 
      data-[variant=success]:focus:text-success-900 dark:data-[variant=success]:focus:text-success-300
      data-[variant=warning]:text-warning-700 dark:data-[variant=warning]:text-warning-400 
      data-[variant=warning]:focus:bg-warning-50 dark:data-[variant=warning]:focus:bg-warning-950 
      data-[variant=warning]:focus:text-warning-900 dark:data-[variant=warning]:focus:text-warning-300
      [&_svg:not([class*='text-'])]:text-slate-500 dark:[&_svg:not([class*='text-'])]:text-slate-400
      relative flex cursor-default items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 data-[inset]:pl-8 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4`, props.class)"
  >
    <slot />
  </DropdownMenuItem>
</template>
