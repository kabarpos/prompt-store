<template>
  <div>
    <slot :field="props.field" />
  </div>
</template>

<script setup>
const props = defineProps({
  field: Object
});
</script> 