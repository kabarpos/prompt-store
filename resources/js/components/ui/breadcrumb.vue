<!-- Breadcrumb.vue -->
<template>
  <nav class="breadcrumb">
    <ol class="flex items-center gap-3 text-sm">
      <li v-for="(item, index) in items" :key="index" class="flex items-center">
        <template v-if="index === items.length - 1">
          <span class="font-medium text-primary-600">{{ item.label }}</span>
        </template>
        <template v-else>
          <Link 
            v-if="item.href" 
            :href="item.href" 
            class="text-gray-600 hover:text-primary-600 transition-colors"
          >
            {{ item.label }}
          </Link>
          <span v-else class="text-gray-600">{{ item.label }}</span>
          <span class="text-gray-500 mx-3">/</span>
        </template>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  items: {
    type: Array,
    required: true,
    // Setiap item harus memiliki label dan href (opsional)
    // [{ label: 'Beranda', href: '/' }, { label: 'Produk' }]
  }
});
</script>

<style scoped>
.breadcrumb {
  padding: 0.75rem 1rem;
}

.breadcrumb ol {
  margin: 0;
}
</style> 