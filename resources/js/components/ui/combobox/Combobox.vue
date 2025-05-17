<template>
  <div class="relative w-full">
    <div
      class="relative w-full cursor-default overflow-hidden rounded-md border border-slate-200 dark:border-slate-700 bg-transparent text-sm ring-offset-background focus-within:ring-2 focus-within:ring-primary-400 focus-within:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
    >
      <div class="flex items-center">
        <input
          ref="inputRef"
          type="text"
          :placeholder="placeholder"
          class="w-full h-9 bg-transparent py-2 pl-3 pr-10 text-sm outline-none disabled:cursor-not-allowed disabled:opacity-50"
          :value="searchQuery"
          @input="(e) => filterOptions(e)"
          @focus="open = true"
          @keydown.down.prevent="focusNextOption"
          @keydown.up.prevent="focusPrevOption"
          @keydown.enter.prevent="selectFocusedOption"
          @keydown.esc.prevent="close"
        />
        <button
          type="button"
          class="absolute inset-y-0 right-0 flex h-full items-center pr-3 cursor-pointer"
          @click.stop="toggleOpen"
          @mousedown.prevent
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': open }">
            <path d="m6 9 6 6 6-6"></path>
          </svg>
        </button>
      </div>
    </div>

    <div
      v-if="open"
      class="absolute z-50 mt-1 w-full overflow-hidden rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-secondary-900 dark:text-white shadow-md"
    >
      <div
        v-if="filteredOptions.length > 0"
        class="max-h-60 overflow-y-auto py-1"
      >
        <div
          v-for="(option, index) in filteredOptions"
          :key="option.value"
          ref="optionRefs"
          tabindex="-1"
          class="relative flex cursor-pointer select-none items-center rounded-sm px-3 py-2 text-sm outline-none"
          :class="{
            'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white': focusedIndex === index || option.value === modelValue,
            'hover:bg-slate-100 dark:hover:bg-slate-700': focusedIndex !== index
          }"
          @click="selectOption(option)"
          @mouseenter="focusedIndex = index"
          @mouseleave="focusedIndex = -1"
        >
          {{ option.label }}
          <svg v-if="option.value === modelValue" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-auto h-4 w-4">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
      </div>
      <div
        v-else
        class="py-6 text-center text-sm text-slate-500 dark:text-slate-400"
      >
        Tidak ada hasil ditemukan
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

interface Option {
  value: string | number;
  label: string;
}

const props = defineProps({
  modelValue: {
    type: [String, Number, null],
    default: null
  },
  options: {
    type: Array as () => Option[],
    default: () => []
  },
  placeholder: {
    type: String,
    default: "Pilih opsi..."
  }
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const searchQuery = ref('');
const filteredOptions = ref<Option[]>([...props.options]);
const inputRef = ref<HTMLInputElement | null>(null);
const optionRefs = ref<HTMLElement[]>([]);
const focusedIndex = ref(-1);

// Watch for modelValue changes to update display
watch(() => props.modelValue, (val) => {
  // When modelValue changes, update the display
  if (val === null || val === undefined) {
    searchQuery.value = '';
    return;
  }
  
  const selectedOption = props.options.find(opt => opt.value === val);
  if (selectedOption) {
    searchQuery.value = selectedOption.label;
  }
}, { immediate: true });

function filterOptions(e: Event) {
  const target = e.target as HTMLInputElement;
  searchQuery.value = target.value;
  
  if (!searchQuery.value) {
    filteredOptions.value = [...props.options];
    return;
  }
  
  filteredOptions.value = props.options.filter(option => 
    option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
  
  // Reset focused index when filtering
  focusedIndex.value = -1;
}

function selectOption(option: Option) {
  emit('update:modelValue', option.value);
  searchQuery.value = option.label;
  close();
}

function toggleOpen() {
  if (open.value) {
    close();
  } else {
    openDropdown();
  }
}

function openDropdown() {
  open.value = true;
  focusedIndex.value = -1;
  filteredOptions.value = [...props.options];
  
  nextTick(() => {
    if (inputRef.value) {
      inputRef.value.focus();
    }
  });
}

function close() {
  open.value = false;
  focusedIndex.value = -1;
  
  // Restore the display to the selected value if exists
  if (props.modelValue !== null && props.modelValue !== undefined) {
    const selectedOption = props.options.find(opt => opt.value === props.modelValue);
    if (selectedOption) {
      searchQuery.value = selectedOption.label;
    }
  } else if (!searchQuery.value) {
    // Reset filtered options if search is empty
    filteredOptions.value = [...props.options];
  }
}

function focusNextOption() {
  if (filteredOptions.value.length === 0) return;
  
  if (focusedIndex.value < filteredOptions.value.length - 1) {
    focusedIndex.value++;
  } else {
    focusedIndex.value = 0; // Loop back to the first option
  }
  
  scrollToFocusedOption();
}

function focusPrevOption() {
  if (filteredOptions.value.length === 0) return;
  
  if (focusedIndex.value > 0) {
    focusedIndex.value--;
  } else {
    focusedIndex.value = filteredOptions.value.length - 1; // Loop to the last option
  }
  
  scrollToFocusedOption();
}

function scrollToFocusedOption() {
  nextTick(() => {
    if (focusedIndex.value >= 0 && optionRefs.value && optionRefs.value[focusedIndex.value]) {
      optionRefs.value[focusedIndex.value].scrollIntoView({
        block: 'nearest',
        inline: 'nearest'
      });
    }
  });
}

function selectFocusedOption() {
  if (focusedIndex.value >= 0 && focusedIndex.value < filteredOptions.value.length) {
    selectOption(filteredOptions.value[focusedIndex.value]);
  }
}

// Close dropdown when clicking outside
function handleClickOutside(event: MouseEvent) {
  const target = event.target as Node;
  const comboboxEl = document.getElementById('combobox-container');
  
  if (comboboxEl && !comboboxEl.contains(target)) {
    close();
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  
  // Initialize UI based on modelValue
  if (props.modelValue !== null && props.modelValue !== undefined) {
    const selectedOption = props.options.find(opt => opt.value === props.modelValue);
    if (selectedOption) {
      searchQuery.value = selectedOption.label;
    }
  }
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Update filtered options when props.options changes
watch(() => props.options, (newOptions) => {
  filteredOptions.value = [...newOptions];
}, { deep: true });
</script>

<style scoped>
/* Fixed height for dropdown items for better keyboard navigation */
.max-h-60 {
  max-height: 15rem;
}

/* Smoother scrolling for dropdown */
.overflow-y-auto {
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
}

/* Animation for dropdown */
@keyframes dropdown-in {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-animation {
  animation: dropdown-in 0.15s ease-out forwards;
}
</style> 