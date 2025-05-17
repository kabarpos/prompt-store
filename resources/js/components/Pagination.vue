<template>
  <div v-if="links && links.length > 3" class="flex flex-wrap justify-center gap-1">
    <div v-for="(link, index) in links" :key="index">
      <component
        :is="link.url ? Link : 'span'"
        v-if="shouldRender(link)"
        :href="link.url"
        :class="[
          'inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background',
          link.active 
            ? 'bg-primary text-primary-foreground hover:bg-primary/90'
            : 'bg-transparent hover:bg-accent hover:text-accent-foreground',
          'h-9 px-4',
          {
            'cursor-not-allowed opacity-50': !link.url,
            'cursor-pointer': link.url,
          }
        ]"
        v-html="getLabel(link)"
      />
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  links: {
    type: Array,
    default: () => []
  }
});

// Fungsi untuk menentukan apakah link harus dirender
// Skip jika ini adalah 'prev' pertama dan 'next' terakhir yang kosong
const shouldRender = (link) => {
  // Skip jika link tidak memiliki URL dan adalah 'prev' atau 'next'
  if (!link.url && (link.label === '&laquo; Previous' || link.label === 'Next &raquo;')) {
    return false;
  }
  return true;
};

// Fungsi untuk mendapatkan label yang lebih sederhana
const getLabel = (link) => {
  if (link.label === '&laquo; Previous') return '&laquo;';
  if (link.label === 'Next &raquo;') return '&raquo;';
  return link.label;
};
</script> 