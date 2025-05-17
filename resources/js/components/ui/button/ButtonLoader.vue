<script setup lang="ts">
import { computed } from 'vue';
import type { ButtonVariant } from './Button.vue';

interface Props {
  variant?: ButtonVariant;
  size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  size: 'md'
});

// Menentukan ukuran spinner berdasarkan prop size
const spinnerSize = computed(() => {
  switch (props.size) {
    case 'sm': return 'h-3 w-3';
    case 'lg': return 'h-5 w-5';
    default: return 'h-4 w-4'; // medium
  }
});

// Mendapatkan kelas spinner berdasarkan variant
const spinnerClass = computed(() => {
  // Untuk variant yang memerlukan spinner berwarna putih
  const whiteSpinnerVariants = ['default', 'destructive', 'secondary', 'success', 'warning', 'action', 'action-secondary', 'action-danger', 'action-success', 'action-warning'];
  
  if (whiteSpinnerVariants.includes(props.variant)) {
    return 'border-white border-t-transparent';
  }
  
  // Untuk variant dengan latar belakang transparan
  return 'border-current border-t-transparent';
});
</script>

<template>
  <div :class="[
    spinnerSize, 
    'animate-spin rounded-full border-2', 
    spinnerClass
  ]"></div>
</template> 