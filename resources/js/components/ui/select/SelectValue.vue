<script setup lang="ts">
import { inject, computed, watch, ref } from 'vue'

const props = defineProps({
  placeholder: {
    type: String,
    default: 'Pilih opsi'
  }
})

// Mendapatkan nilai dari parent Select
const selectValue = inject('select-value', ref<any>(''))
const selectedLabel = inject('select-selected-label', ref(''))

// Nilai yang ditampilkan
const displayValue = computed(() => {
  if (selectedLabel.value) {
    return selectedLabel.value
  }
  return selectValue.value || props.placeholder
})
</script>

<template>
  <span class="block truncate">
    {{ displayValue }}
  </span>
</template>
