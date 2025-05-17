<template>
  <p v-if="props.message || $slots.default" class="text-sm font-medium text-red-500">
    <slot>{{ props.message }}</slot>
  </p>
</template>

<script setup>
const props = defineProps({
  message: String
});
</script> 