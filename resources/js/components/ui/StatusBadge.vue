<template>
  <div
    :class="[
      'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium',
      statusClasses
    ]"
  >
    <slot>{{ status }}</slot>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true,
    validator: (value) => ['aktif', 'active', 'tidak aktif', 'inactive', 'sukses', 'success', 'gagal', 'failed', 'pending', 'draft', 'published'].includes(value.toLowerCase()),
  },
});

const statusClasses = computed(() => {
  const normalizedStatus = props.status.toLowerCase();
  
  switch (normalizedStatus) {
    case 'aktif':
    case 'active':
    case 'sukses':
    case 'success':
    case 'published':
      return 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-300 border border-success-200 dark:border-success-800';
    
    case 'tidak aktif':
    case 'inactive':
    case 'gagal':
    case 'failed':
      return 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-300 border border-danger-200 dark:border-danger-800';
    
    case 'pending':
      return 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-300 border border-warning-200 dark:border-warning-800';
    
    case 'draft':
      return 'bg-secondary-100 text-secondary-800 dark:bg-secondary-900 dark:text-secondary-300 border border-secondary-200 dark:border-secondary-800';
    
    default:
      return 'bg-secondary-100 text-secondary-800 dark:bg-secondary-900 dark:text-secondary-300 border border-secondary-200 dark:border-secondary-800';
  }
});
</script> 