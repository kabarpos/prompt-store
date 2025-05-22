<template>
  <MainLayout>
    <HomeHero :products="products" :categories="categories" />
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/components/layout/MainLayout.vue'
import HomeHero from '@/components/home/HomeHero.vue'

// Define props untuk menerima data produk dari controller
defineProps({
  products: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  }
})
</script> 