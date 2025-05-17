<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { MoreHorizontal } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { buttonTheme } from '@/components/ui/button';

// Mengambil tipe colorScheme dari buttonTheme
type ColorScheme = keyof typeof buttonTheme;

interface Props {
  size?: 'default' | 'sm' | 'lg' | 'icon';
  srText?: string;
  class?: string;
  colorScheme?: ColorScheme;
}

const props = withDefaults(defineProps<Props>(), {
  size: 'icon',
  srText: 'Buka menu',
  class: '',
  colorScheme: 'ghost' as ColorScheme // Menggunakan ghost theme dari theme.ts
});
</script>

<template>
  <Button 
    :colorScheme="props.colorScheme"
    :size="props.size"
    :class="cn(
      'h-8 w-8 cursor-pointer',
      props.class
    )"
  >
    <MoreHorizontal class="h-4 w-4" />
    <span class="sr-only">{{ props.srText }}</span>
  </Button>
</template> 