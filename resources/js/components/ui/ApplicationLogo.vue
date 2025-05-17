<template>
  <svg
    viewBox="0 0 50 50"
    :class="className"
    xmlns="http://www.w3.org/2000/svg"
  >
    <!-- Logo Circle -->
    <circle cx="25" cy="25" r="20" class="fill-primary" />
    
    <!-- Letter D -->
    <path
      d="M20 15h5c5.523 0 10 4.477 10 10s-4.477 10-10 10h-5V15z"
      fill="white"
    />
    <path
      d="M25 17c4.418 0 8 3.582 8 8s-3.582 8-8 8h-3V17h3z"
      :class="className"
    />
  </svg>
</template>

<script setup lang="ts">
defineProps<{
  className?: string;
}>();
</script> 