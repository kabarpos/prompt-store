<script setup lang="ts">
import { cn } from '@/lib/utils'
import { Check } from 'lucide-vue-next'
import { inject, computed, ref } from 'vue'

const props = defineProps({
  value: {
    type: [String, Number],
    required: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  class: {
    type: String,
    default: ''
  }
})

// Mendapatkan nilai dari parent Select
const selectValue = inject('select-value', ref<any>(''))
const isOpen = inject('select-is-open', ref(false))
const closeDropdown = inject('select-close', () => {})

// Mengecek apakah item ini terpilih
const isSelected = computed(() => selectValue.value === props.value)

// Handle pemilihan item
const handleSelect = () => {
  if (props.disabled) return
  
  // Perbarui nilai select
  selectValue.value = props.value
  
  // Tutup dropdown setelah memilih
  closeDropdown()
}
</script>

<template>
  <div
    :class="cn(
      'relative flex w-full cursor-pointer select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none transition-colors hover:bg-accent/50 data-[state=checked]:bg-accent data-[state=checked]:text-accent-foreground',
      isSelected ? 'bg-accent text-accent-foreground' : '',
      props.disabled ? 'pointer-events-none opacity-50' : '',
      props.class
    )"
    @click="handleSelect"
    @mousedown.prevent
    :data-state="isSelected ? 'checked' : 'unchecked'"
    :data-disabled="props.disabled ? true : undefined"
  >
    <span class="absolute right-2 flex h-3.5 w-3.5 items-center justify-center">
      <Check v-if="isSelected" class="h-4 w-4" />
    </span>
    <span class="pr-6">
      <slot>{{ props.value }}</slot>
    </span>
  </div>
</template>
