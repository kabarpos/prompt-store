<template>
  <Link
    :href="href"
    :method="method || undefined"
    :as="as"
    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition duration-150 ease-in-out"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  as: {
    type: String,
    default: 'a'
  },
  href: {
    type: String,
    default: '#'
  },
  method: {
    type: String,
    default: null
  }
});
</script> 