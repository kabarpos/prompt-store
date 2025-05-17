<template>
  <DropdownMenu>
    <DropdownMenuTrigger>
      <slot name="trigger" />
    </DropdownMenuTrigger>
    
    <DropdownMenuContent :class="contentClasses">
      <slot />
    </DropdownMenuContent>
  </DropdownMenu>
</template>

<script setup lang="ts">
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
} from './dropdown-menu';

defineProps<{
  align?: 'start' | 'end' | 'center';
  width?: string | number;
  contentClasses?: string;
}>();
</script> 