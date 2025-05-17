<script setup lang="ts">
import { ref, provide, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number, Object, Array],
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

// State dropdown
const isOpen = ref(false);
provide('select-is-open', isOpen)

// State for selected value
const selectValue = ref<any>(props.modelValue)
provide('select-value', selectValue)

// Selected label (shown in trigger)
const selectedLabel = ref('')
provide('select-selected-label', selectedLabel)

// Watch for model value changes
watch(() => props.modelValue, (val) => {
  selectValue.value = val
})

// Update model value on selection
watch(selectValue, (val) => {
  emit('update:modelValue', val)
})

// Function to close dropdown
const closeDropdown = () => {
  isOpen.value = false
}

// Expose key function to components
provide('select-close', closeDropdown)
</script>

<template>
  <div>
    <slot 
      :open="isOpen" 
      :value="selectValue" 
      :close="closeDropdown">
    </slot>
  </div>
</template>
