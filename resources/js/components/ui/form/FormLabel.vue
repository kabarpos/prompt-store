<template>
  <label class="text-sm font-medium leading-none" :for="props.for">
    <slot />
  </label>
</template>

<script setup>
const props = defineProps({
  for: String
});
</script> 