<template>
  <div
    :class="[
      'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus-ring',
      'whitespace-nowrap',
      'cursor-default',
      variantClasses,
      size === 'sm' ? 'text-[0.65rem] px-2 py-0.25' : '',
      size === 'lg' ? 'text-sm px-3 py-1' : '',
    ]"
  >
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => [
      'default', 
      'primary', 
      'secondary', 
      'destructive', 
      'outline', 
      'success', 
      'warning', 
      'ghost',
      'credential',
      'domain',
      'update',
      'download',
      'other',
      'orange'
    ].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'default':
      return 'bg-primary-600 dark:bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700';
    case 'primary':
      return 'bg-primary-600 dark:bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700';
    case 'secondary':
      return 'bg-secondary-600 dark:bg-secondary-600 text-white hover:bg-secondary-700 dark:hover:bg-secondary-700';
    case 'destructive':
      return 'bg-danger-600 dark:bg-danger-600 text-white hover:bg-danger-700 dark:hover:bg-danger-700';
    case 'success':
      return 'bg-success-600 dark:bg-success-600 text-white hover:bg-success-700 dark:hover:bg-success-700';
    case 'warning':
      return 'bg-warning-500 dark:bg-warning-500 text-white hover:bg-warning-600 dark:hover:bg-warning-600';
    case 'outline':
      return 'bg-transparent text-primary-700 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-950/50';
    case 'ghost':
      return 'bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-400 hover:bg-primary-200 dark:hover:bg-primary-900';
    case 'credential':
      return 'bg-blue-600 dark:bg-blue-600 text-white hover:bg-blue-700 dark:hover:bg-blue-700';
    case 'domain':
      return 'bg-violet-600 dark:bg-violet-600 text-white hover:bg-violet-700 dark:hover:bg-violet-700';
    case 'update':
      return 'bg-emerald-600 dark:bg-emerald-600 text-white hover:bg-emerald-700 dark:hover:bg-emerald-700';
    case 'orange':
      return 'bg-orange-500 dark:bg-orange-500 text-white hover:bg-orange-600 dark:hover:bg-orange-600';
    case 'download':
      return 'bg-amber-500 dark:bg-amber-500 text-white hover:bg-amber-600 dark:hover:bg-amber-600';
    case 'other':
      return 'bg-gray-500 dark:bg-gray-600 text-white hover:bg-gray-600 dark:hover:bg-gray-700';
    default:
      return 'bg-primary-600 dark:bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700';
  }
});
</script>
