<script setup>
import { cn } from '@/lib/utils';
import { 
  ChevronDown, 
  ChevronUp, 
  Check, 
  Search, 
  X,
  ChevronRight,
  ChevronLeft,
  Home
} from 'lucide-vue-next';

const props = defineProps({
  name: {
    type: String,
    required: true,
    validator: (value) => ['chevron-down', 'chevron-up', 'check', 'search', 'x', 'chevron-right', 'chevron-left', 'home'].includes(value)
  },
  class: {
    type: String,
    default: ''
  },
  size: {
    type: [String, Number],
    default: 24
  },
  strokeWidth: {
    type: [String, Number],
    default: 2
  },
  color: {
    type: String,
    default: 'currentColor'
  }
});

const iconMap = {
  'chevron-down': ChevronDown,
  'chevron-up': ChevronUp,
  'check': Check,
  'search': Search,
  'x': X,
  'chevron-right': ChevronRight,
  'chevron-left': ChevronLeft,
  'home': Home
};

// Mendapatkan komponen icon berdasarkan nama
const iconComponent = iconMap[props.name];
</script>

<template>
  <component 
    :is="iconComponent" 
    :class="cn('h-4 w-4', props.class)" 
    :size="typeof props.size === 'string' ? parseInt(props.size, 10) : props.size" 
    :stroke-width="typeof props.strokeWidth === 'string' ? parseInt(props.strokeWidth, 10) : props.strokeWidth" 
    :color="props.color" 
  />
</template>
