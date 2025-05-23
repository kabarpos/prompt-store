<template>
  <input
    type="radio"
    :id="id"
    :name="name"
    :value="value"
    :checked="checked"
    @change="$emit('change', $event.target.value)"
    class="h-4 w-4 text-primary border-gray-300 focus:ring-primary-200 focus:ring-2"
  />
</template>

<script setup>
defineProps({
  id: {
    type: String,
    required: true
  },
  name: {
    type: String,
    default: 'radio-group'
  },
  value: {
    type: [String, Number],
    required: true
  },
  checked: {
    type: Boolean,
    default: false
  }
});

defineEmits(['change']);
</script> 