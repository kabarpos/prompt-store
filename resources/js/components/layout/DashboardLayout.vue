<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <DashboardNavigation />
    
    <!-- Page Content -->
    <main class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import DashboardNavigation from '@/components/layout/DashboardNavigation.vue';
</script> 