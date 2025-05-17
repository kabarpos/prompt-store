<template>
  <form v-if="as === 'button'" :action="href || ''" :method="method" class="w-full">
    <button
      type="submit"
      class="w-full text-left"
      :class="classes"
    >
      <slot />
    </button>
  </form>
  <Link
    v-else
    :href="href || ''"
    :class="classes"
  >
    <slot />
  </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const props = withDefaults(defineProps<{
  href?: string;
  as?: string;
  method?: string;
}>(), {
  href: '',
  as: 'a',
  method: 'post'
});

const classes = 'block w-full px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out';
</script> 