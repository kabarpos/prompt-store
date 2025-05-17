import type { ComputedRef, Ref } from 'vue'
import { createContext } from 'reka-ui'

export const SIDEBAR_COOKIE_NAME = 'sidebar:state'
export const SIDEBAR_COOKIE_MAX_AGE = 60 * 60 * 24 * 7

// Nilai digunakan untuk membuat Tailwind class: w-64 (16rem), dsb
export const SIDEBAR_WIDTH = '16rem' // 64 (dalam Tailwind w-64)
export const SIDEBAR_WIDTH_MOBILE = '18rem' // 72 (dalam Tailwind w-72)
export const SIDEBAR_WIDTH_ICON = '3.5rem' // 14 (dalam Tailwind w-14)

export const SIDEBAR_KEYBOARD_SHORTCUT = 'b'

export const [useSidebar, provideSidebarContext] = createContext<{
  state: ComputedRef<'expanded' | 'collapsed'>
  open: Ref<boolean>
  setOpen: (value: boolean) => void
  isMobile: Ref<boolean>
  openMobile: Ref<boolean>
  setOpenMobile: (value: boolean) => void
  toggleSidebar: () => void
}>('Sidebar')
