<script setup lang="ts">
import { cn } from '@/lib/utils'
import { ref, inject, onMounted, onUnmounted, reactive, watch, nextTick } from 'vue'

const props = defineProps({
  class: {
    type: String,
    default: ''
  },
  position: {
    type: String,
    default: 'popper' // 'popper' or 'item-aligned'
  },
  side: {
    type: String,
    default: 'bottom' // 'bottom' or 'top'
  },
  align: {
    type: String,
    default: 'start' // 'start', 'center', 'end'
  },
  sideOffset: {
    type: Number,
    default: 4
  }
})

// Mendapatkan state isOpen dari SelectTrigger
const isOpen = inject('select-is-open', ref(false))

// Ref untuk elemen dropdown
const contentRef = ref<HTMLElement | null>(null)

// State untuk posisi
const position = reactive({
  width: '12rem',
  maxHeight: '16rem',
  top: '0px',
  left: '0px',
  transformOrigin: 'top'
})

// Mendapatkan referensi ke trigger element
const triggerElement = inject('select-trigger-element', ref<HTMLElement | null>(null))

// Fungsi untuk memposisikan dropdown
const updateDropdownPosition = () => {
  if (!triggerElement.value || !contentRef.value) return
  
  const trigger = triggerElement.value.getBoundingClientRect()
  const viewport = {
    width: window.innerWidth,
    height: window.innerHeight
  }
  
  // Default width dari trigger
  position.width = `${trigger.width}px`
  
  // Hitung posisi berdasarkan align dan side
  let top = 0
  let left = 0
  
  if (props.side === 'bottom') {
    top = trigger.bottom + props.sideOffset + window.scrollY
  } else {
    top = trigger.top - props.sideOffset - (contentRef.value?.offsetHeight || 0) + window.scrollY
    position.transformOrigin = 'bottom'
  }
  
  // Align horizontal
  if (props.align === 'start') {
    left = trigger.left + window.scrollX
  } else if (props.align === 'center') {
    left = trigger.left + (trigger.width / 2) - ((contentRef.value?.offsetWidth || 0) / 2) + window.scrollX
  } else if (props.align === 'end') {
    left = trigger.right - (contentRef.value?.offsetWidth || 0) + window.scrollX
  }
  
  // Pastikan dropdown tidak keluar dari viewport
  const dropdown = contentRef.value.getBoundingClientRect()
  const dropdownHeight = dropdown.height || 300 // Fallback jika height belum tersedia
  
  // Cek apakah dropdown keluar dari bawah viewport
  const overflowBottom = (trigger.bottom + dropdownHeight) > viewport.height
  const hasSpaceOnTop = trigger.top > dropdownHeight
  
  // Flip ke atas jika overflow bawah dan ada cukup ruang di atas
  if (props.side === 'bottom' && overflowBottom && hasSpaceOnTop) {
    top = trigger.top - dropdownHeight - props.sideOffset + window.scrollY
    position.transformOrigin = 'bottom'
  }
  
  // Batasi tinggi jika mendekati batas viewport
  const maxHeightValue = Math.min(
    viewport.height - (top - window.scrollY) - 10,
    viewport.height * 0.8
  )
  position.maxHeight = `${Math.max(100, maxHeightValue)}px`
  
  // Pastikan tidak keluar di kiri/kanan viewport
  if (left < 5) {
    left = 5
  } else if ((left + dropdown.width) > viewport.width) {
    left = viewport.width - dropdown.width - 5
  }
  
  // Update posisi
  position.top = `${top}px`
  position.left = `${left}px`
}

// Update position when visible
watch(isOpen, (newValue) => {
  if (newValue) {
    nextTick(() => {
      updateDropdownPosition()
    })
  }
})

// Update posisi saat window di-resize
const handleResize = () => {
  if (isOpen.value) {
    updateDropdownPosition()
  }
}

// Close dropdown ketika klik di luar
const handleClickOutside = (event: MouseEvent) => {
  if (!isOpen.value) return
  
  const targetElement = event.target as HTMLElement
  
  // Cek apakah klik di luar content dan trigger
  if (
    contentRef.value && 
    !contentRef.value.contains(targetElement) && 
    triggerElement.value && 
    !triggerElement.value.contains(targetElement)
  ) {
    isOpen.value = false
  }
}

// Setup event listener
onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
  window.addEventListener('resize', handleResize)
  window.addEventListener('scroll', handleResize)
})

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside)
  window.removeEventListener('resize', handleResize)
  window.removeEventListener('scroll', handleResize)
})
</script>

<template>
  <Teleport to="body">
    <div 
      v-if="isOpen" 
      ref="contentRef" 
      class="fixed z-[9999] min-w-[8rem] overflow-hidden rounded-md border border-input bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-md animate-in fade-in-0 zoom-in-95"
      :class="[
        props.class,
        props.side === 'top' ? 'slide-in-from-bottom-2' : 'slide-in-from-top-2'
      ]"
      :style="{
        width: position.width,
        maxHeight: position.maxHeight,
        top: position.top,
        left: position.left,
        transformOrigin: position.transformOrigin
      }"
    >
      <div class="p-1 overflow-y-auto" :style="{ maxHeight: position.maxHeight }">
        <slot />
      </div>
    </div>
  </Teleport>
</template>
