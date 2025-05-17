<script setup lang="ts">
import { cn } from '@/lib/utils'
import {
  DropdownMenuContent,
  type DropdownMenuContentEmits,
  type DropdownMenuContentProps,
  DropdownMenuPortal,
  useForwardPropsEmits,
} from 'reka-ui'
import { computed, ref, onMounted, type HTMLAttributes } from 'vue'

const props = withDefaults(
  defineProps<DropdownMenuContentProps & { class?: HTMLAttributes['class'] }>(),
  {
    sideOffset: 4,
  },
)
const emits = defineEmits<DropdownMenuContentEmits>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)

// Nilai default untuk transform origin
const transformOriginClass = ref('origin-top')

// Optionally adjust based on props
onMounted(() => {
  if (props.side === 'top') {
    transformOriginClass.value = 'origin-bottom'
  } else if (props.side === 'left') {
    transformOriginClass.value = 'origin-right'
  } else if (props.side === 'right') {
    transformOriginClass.value = 'origin-left'
  }
})
</script>

<template>
  <DropdownMenuPortal>
    <DropdownMenuContent
      data-slot="dropdown-menu-content"
      v-bind="forwarded"
      :class="cn('bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-50 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 max-h-dropdown min-w-[8rem] overflow-x-hidden overflow-y-auto rounded-md border border-slate-200 dark:border-slate-700 p-1 shadow-md', transformOriginClass, props.class)"
    >
      <slot />
    </DropdownMenuContent>
  </DropdownMenuPortal>
</template>
