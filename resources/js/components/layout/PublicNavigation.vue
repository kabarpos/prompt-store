<template>
  <nav 
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
      scrolled ? 'bg-background/95 backdrop-blur-sm border-b border-border/50' : 'bg-transparent'
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <!-- Logo Section -->
        <Link 
          href="/" 
          class="flex items-center gap-2 hover:opacity-80 transition-opacity cursor-pointer group"
        >
          <div class="relative w-10 h-10 flex items-center justify-center">
            <div class="absolute inset-0 bg-primary-600 rounded-xl rotate-6 transition-transform group-hover:rotate-12"></div>
            <span class="relative text-2xl font-bold text-white">D</span>
          </div>
          <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-primary-500 dark:from-primary-400 dark:to-primary-300">
            Dilogif
          </span>
        </Link>

        <!-- Navigation Links - Desktop -->
        <div class="hidden md:flex items-center gap-1">
          <Link
            v-for="item in navigationItems"
            :key="item.name"
            :href="item.to"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer hover:bg-primary-50 dark:hover:bg-primary-900/20"
            :class="[
              route().current(item.name)
                ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' 
                : 'text-foreground/80 hover:text-primary-600 dark:text-foreground/80 dark:hover:text-primary-400'
            ]"
          >
            {{ item.text }}
          </Link>
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-4">
          <!-- Theme Toggle -->
          <button
            @click="toggleTheme"
            class="p-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors cursor-pointer"
            :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <SunIcon v-if="isDark" class="w-5 h-5 text-primary-400" />
            <MoonIcon v-else class="w-5 h-5 text-primary-600" />
          </button>

          <!-- Cart Button -->
          <Link
            href="/cart"
            class="relative p-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group cursor-pointer"
          >
            <ShoppingCartIcon class="w-5 h-5 text-foreground/80 group-hover:text-primary-600 dark:text-foreground/80 dark:group-hover:text-primary-400" />
            <span
              v-if="cartItemCount > 0"
              class="absolute -top-1 -right-1 w-5 h-5 flex items-center justify-center bg-primary-600 text-white text-xs font-bold rounded-full shadow-lg group-hover:scale-110 transition-transform"
            >
              {{ cartItemCount }}
            </span>
          </Link>

          <!-- Login/Register Buttons -->
          <div class="flex items-center">
           
            <Link
              href="/login"
              class="px-4 py-2 rounded-lg text-sm font-medium bg-primary-600 text-white hover:bg-primary-700 dark:bg-primary-600 dark:text-white dark:hover:bg-primary-700 transition-all shadow-lg hover:shadow-primary-600/25 cursor-pointer flex items-center"
            >
              <LogIn class="w-4 h-4" />
              <span class="ml-2">Login</span>
            </Link>
          </div>

          <!-- Mobile Menu Button -->
          <button
            @click="isOpen = !isOpen"
            class="md:hidden p-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors cursor-pointer"
          >
            <Bars3Icon v-if="!isOpen" class="w-6 h-6 text-foreground/80" />
            <XMarkIcon v-else class="w-6 h-6 text-foreground/80" />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div
      v-if="isOpen"
      class="md:hidden bg-background/80 backdrop-blur-lg border-t border-border dark:border-border"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col gap-2">
          <Link
            v-for="item in navigationItems"
            :key="item.name"
            :href="item.to"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer hover:bg-primary-50 dark:hover:bg-primary-900/20"
            :class="[
              route().current(item.name)
                ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' 
                : 'text-foreground/80 hover:text-primary-600 dark:text-foreground/80 dark:hover:text-primary-400'
            ]"
            @click="isOpen = false"
          >
            {{ item.text }}
          </Link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { 
  SunIcon, 
  MoonIcon, 
  ShoppingCartIcon, 
  Bars3Icon, 
  XMarkIcon 
} from '@heroicons/vue/24/outline'
import { LogIn } from 'lucide-vue-next'
import { subscribe, unsubscribe } from '@/event-bus'

const isOpen = ref(false)
const scrolled = ref(false)
const isDark = ref(false)

const page = usePage()
const cartItemCount = ref(page.props.cartCount || 0)

// Subscribe to cart count changes
const updateCount = (count) => {
  cartItemCount.value = count
}

onMounted(() => {
  isDark.value = document.documentElement.classList.contains('dark')
  window.addEventListener('scroll', handleScroll)
  handleScroll()
  
  // Subscribe to cart count updates
  subscribe('cartCount', updateCount)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  // Unsubscribe when component is unmounted
  unsubscribe('cartCount', updateCount)
})

const navigationItems = [
  { name: 'beranda', to: '/', text: 'Beranda' },
  { name: 'layanan', to: '/services', text: 'Layanan' },
  { name: 'produk', to: '/products', text: 'Produk' },
  { name: 'tentang', to: '/about', text: 'Tentang' },
  { name: 'kontak', to: '/contact', text: 'Kontak' }
]

const toggleTheme = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark')
}

const handleScroll = () => {
  scrolled.value = window.scrollY > 0
}
</script> 