<template>
  <component
    :is="as"
    :class="[
      'scroll-m-20',
      size === 'lg' && 'text-3xl font-bold tracking-tight md:text-4xl',
      size === 'md' && 'text-2xl font-semibold tracking-tight',
      size === 'sm' && 'text-xl font-semibold tracking-tight',
    ]"
  >
    <slot />
  </component>
</template>

<script setup lang="ts">
interface Props {
  as?: 'h1' | 'h2' | 'h3' | 'h4'
  size?: 'sm' | 'md' | 'lg'
}

withDefaults(defineProps<Props>(), {
  as: 'h1',
  size: 'lg',
})
</script>
