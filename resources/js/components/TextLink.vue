<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
  href: string;
  className?: string;
}

defineProps<Props>();
</script>

<template>
  <Link 
    :href="href" 
    :class="[
      'text-primary hover:text-primary/80 transition-colors duration-200',
      className
    ]"
  >
    <slot />
  </Link>
</template> 