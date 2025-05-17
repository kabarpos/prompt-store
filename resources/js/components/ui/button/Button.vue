<!-- eslint-disable vue/multi-word-component-names -->
<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { buttonVariants } from './index';
import { cn } from '@/lib/utils';

export type ButtonVariant = 
  | 'default'
  | 'primary'
  | 'destructive'
  | 'outline'
  | 'secondary'
  | 'ghost'
  | 'link'
  | 'success'
  | 'warning'
  | 'action'
  | 'action-secondary'
  | 'action-danger'
  | 'action-success'
  | 'action-warning'
  | 'action-outline';

type ButtonSize = 'default' | 'sm' | 'lg' | 'icon';

interface Props {
  type?: 'button' | 'submit' | 'reset';
  variant?: ButtonVariant;
  size?: ButtonSize;
  disabled?: boolean;
  loading?: boolean;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'button',
  variant: 'default',
  size: 'default',
  disabled: false,
  loading: false,
});

const buttonClasses = computed(() => {
  const baseClasses = buttonVariants({
    variant: props.variant,
    size: props.size
  });
  
  return cn(
    baseClasses,
    props.class
  );
});

// Menentukan kelas untuk spinner
const spinnerClasses = computed(() => {
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
  <button :type="type" :class="buttonClasses" :disabled="disabled || loading">
    <span v-if="loading" class="mr-2 h-4 w-4 animate-spin rounded-full border-2" :class="spinnerClasses"></span>
    <slot />
  </button>
</template>
