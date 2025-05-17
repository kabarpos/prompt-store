<template>
  <div v-if="as === 'button'" class="w-full">
    <button
      :type="type"
      class="w-full text-left"
      :class="classes"
    >
      <slot />
    </button>
  </div>
  <Link
    v-else
    :href="href"
    :class="classes"
  >
    <slot />
  </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = withDefaults(defineProps<{
  active?: boolean;
  href?: string;
  as?: string;
  type?: 'submit' | 'button' | 'reset';
}>(), {
  active: false,
  href: '',
  as: 'a',
  type: 'submit'
});

const classes = computed(() => {
  return [
    'block w-full pl-3 pr-4 py-2 border-l-4 text-left text-base font-medium transition duration-150 ease-in-out focus:outline-none',
    props.active
      ? 'border-primary bg-primary/5 text-primary focus:text-primary focus:bg-primary/10 focus:border-primary'
      : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300'
  ];
});
</script> 