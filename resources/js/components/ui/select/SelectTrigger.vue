<script setup lang="ts">
import { ref, inject, provide, computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  placeholder: {
    type: String,
    default: 'Pilih opsi'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  class: {
    type: String,
    default: ''
  }
});

// Inject from parent Select component
const selectValue = inject('select-value', ref<any>(null));
const isOpen = inject('select-is-open', ref(false));
const selectedLabel = inject('select-selected-label', ref(''));

// References for positioning
const triggerRef = ref<HTMLElement | null>(null);

// Provide the trigger element reference for positioning
provide('select-trigger-element', triggerRef);

// Toggle the dropdown
const toggleSelect = () => {
  if (props.disabled) return;
  isOpen.value = !isOpen.value;
};

// Display values
const displayValue = computed(() => {
  return selectedLabel.value || props.placeholder;
});

const isPlaceholderShown = computed(() => {
  return !selectedLabel.value;
});
</script>

<template>
  <div
    ref="triggerRef"
    :class="[
      'flex h-10 w-full items-center justify-between rounded-md border border-slate-200 bg-transparent px-3 py-2 text-sm ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-secondary-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-400 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      'dark:border-slate-700 dark:ring-offset-secondary-900 dark:placeholder:text-secondary-500 dark:focus-visible:ring-primary-400',
      'min-h-10',
      props.class,
      props.disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
    ]"
    @click="toggleSelect"
    :tabindex="props.disabled ? -1 : 0"
    :data-state="isOpen ? 'open' : 'closed'"
    role="combobox"
    aria-haspopup="listbox"
    :aria-expanded="isOpen"
    :aria-disabled="props.disabled || undefined"
  >
    <span class="flex-1 overflow-hidden text-ellipsis whitespace-nowrap text-left" :class="{ 'text-secondary-400 dark:text-secondary-500': isPlaceholderShown }">
      {{ displayValue }}
    </span>
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      class="h-4 w-4 opacity-50 transition-transform"
      :class="{ 'rotate-180': isOpen }"
    >
      <path d="m6 9 6 6 6-6" />
    </svg>
  </div>
</template>

<style scoped>
/* Focus visible styling sudah didefinisikan dalam CSS global */
.focus-visible:focus {
  outline: none;
  ring-width: 2px;
  ring-offset-width: 2px;
  ring-color: #0ea5e9;
}
.dark .focus-visible:focus {
  ring-color: #38bdf8;
  ring-offset-color: #0f172a;
}
</style>
