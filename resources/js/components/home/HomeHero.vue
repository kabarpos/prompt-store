<template>
  <section class="relative min-h-screen flex items-center overflow-hidden bg-background dark:bg-[#0f172a]">
    <!-- Background Patterns/Shapes -->
    <div class="absolute inset-0 z-0 overflow-hidden">
      <!-- Modern Grid Pattern -->
      <div class="absolute top-0 left-0 w-full h-full opacity-5 dark:opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <linearGradient id="grid-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style="stop-color:currentColor;stop-opacity:0.5" />
              <stop offset="100%" style="stop-color:currentColor;stop-opacity:0.2" />
            </linearGradient>
            <pattern id="grid-pattern" width="4" height="4" patternUnits="userSpaceOnUse">
              <path d="M 4 0 L 0 0 0 4" fill="none" stroke="url(#grid-gradient)" stroke-width="0.2" class="text-foreground" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#grid-pattern)" />
        </svg>
      </div>
      
      <!-- Gradient Blobs -->
      <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full"
           style="background: radial-gradient(circle, rgba(var(--primary-rgb), 0.2) 0%, rgba(var(--primary-rgb), 0.1) 40%, rgba(var(--primary-rgb), 0) 70%);
                  filter: blur(40px);"></div>
      <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full"
           style="background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0.1) 40%, rgba(59, 130, 246, 0) 70%);
                  filter: blur(40px);"></div>
      
      <!-- Additional Decorative Elements -->
      <div class="absolute top-1/4 right-1/4 w-48 h-48 rounded-full"
           style="background: radial-gradient(circle, rgba(var(--primary-rgb), 0.1) 0%, rgba(var(--primary-rgb), 0) 70%);
                  filter: blur(30px);"></div>
      <div class="absolute bottom-1/4 left-1/3 w-64 h-64 rounded-full"
           style="background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0) 70%);
                  filter: blur(30px);"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full py-20 md:py-32">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Hero Content -->
        <div class="text-center mb-16">
          <h1 class="text-5xl md:text-6xl font-bold mb-6 text-slate-900 dark:text-white">
            AI Prompt Marketplace
          </h1>
          <p class="text-xl mb-8 text-slate-600 dark:text-gray-300 max-w-3xl mx-auto">
            Explore 190k expert-crafted prompt templates
          </p>
          <p class="text-lg mb-10 text-primary-600 dark:text-primary-400">
            Midjourney, ChatGPT, DALL-E, FLUX & more
          </p>
          
          <div class="flex flex-wrap justify-center gap-4 mb-8">
            <button
              class="bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/30 dark:hover:bg-orange-800/40 text-orange-800 dark:text-orange-300 px-8 py-3 rounded-full shadow-sm hover:shadow-md transition-all duration-200 font-medium"
            >
              Explore prompts
            </button>
            <button
              class="bg-slate-800/80 hover:bg-slate-700 text-white dark:bg-slate-800 dark:hover:bg-slate-700 px-8 py-3 rounded-full shadow-sm hover:shadow-md transition-all duration-200 font-medium"
            >
              Sell prompts
            </button>
          </div>
          
          <!-- Reviews & Featured In -->
          <div class="mt-12">
            <!-- Stars & Reviews -->
            <div class="flex justify-center items-center mb-6">
              <div class="flex">
                <StarIcon class="h-5 w-5 text-yellow-400" v-for="i in 5" :key="i" />
              </div>
              <span class="ml-2 text-sm text-slate-500 dark:text-slate-400">
                19,500+ five star reviews
              </span>
              <span class="mx-4 text-slate-300 dark:text-slate-700">|</span>
              <div class="flex items-center">
                <ShieldCheckIcon class="h-5 w-5 text-green-500 mr-1" />
                <span class="text-sm text-slate-500 dark:text-slate-400">
                  Trusted by 300,000+ users
                </span>
              </div>
            </div>
            
            <!-- Featured In
            <div class="mt-8">
              <p class="text-xs uppercase text-slate-500 dark:text-slate-400 mb-4">Featured in</p>
              <div class="flex flex-wrap justify-center gap-6 opacity-70">
                <img src="/images/press/theverge.svg" alt="The Verge" class="h-6 grayscale" />
                <img src="/images/press/wired.svg" alt="Wired" class="h-6 grayscale" />
                <img src="/images/press/fastcompany.svg" alt="Fast Company" class="h-6 grayscale" />
                <img src="/images/press/financial-times.svg" alt="Financial Times" class="h-6 grayscale" />
                <img src="/images/press/techcrunch.svg" alt="TechCrunch" class="h-6 grayscale" />
                <img src="/images/press/wsj.svg" alt="Wall Street Journal" class="h-6 grayscale" />
              </div>
            </div> -->
          </div>
        </div>
        
        <!-- Feature Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-16">
          <!-- Card 1: Creator Communities -->
          <div class="bg-white dark:bg-gradient-to-br dark:from-slate-900 dark:to-slate-800 rounded-2xl overflow-hidden relative group transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-slate-100 dark:border-slate-700">
            <div class="absolute top-2 right-2 px-2 py-1 bg-primary-600/80 text-white text-xs rounded">New feature</div>
            <div class="p-6 flex flex-col h-full">
              <img src="https://placehold.co/600x400/1e293b/ffffff?text=Creator+Communities" alt="Creator Communities" class="w-full h-40 object-cover rounded mb-4" />
              <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-2">Creator Communities</h3>
              <p class="text-slate-600 dark:text-slate-300 text-sm mt-2">Chat with thousands of other AI creators</p>
            </div>
          </div>
          
          <!-- Card 2: Explore the Marketplace -->
          <div class="bg-white dark:bg-gradient-to-br dark:from-slate-900 dark:to-slate-800 rounded-2xl overflow-hidden relative group transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-slate-100 dark:border-slate-700">
            <div class="p-6 flex flex-col h-full">
              <img src="https://placehold.co/600x400/1e293b/ffffff?text=Marketplace" alt="Marketplace" class="w-full h-40 object-cover rounded mb-4" />
              <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-2">Explore the Marketplace</h3>
              <p class="text-slate-600 dark:text-slate-300 text-sm mt-2">Browse 190k+ quality, tested prompts</p>
            </div>
          </div>
          
          <!-- Card 3: Sell Your Prompts -->
          <div class="bg-white dark:bg-gradient-to-br dark:from-slate-900 dark:to-slate-800 rounded-2xl overflow-hidden relative group transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-slate-100 dark:border-slate-700">
            <div class="p-6 flex flex-col h-full">
              <img src="https://placehold.co/600x400/1e293b/ffffff?text=Sell+Prompts" alt="Sell Prompts" class="w-full h-40 object-cover rounded mb-4" />
              <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-2">Sell Your Prompts</h3>
              <p class="text-slate-600 dark:text-slate-300 text-sm mt-2">Create, share and earn</p>
            </div>
          </div>
          
          <!-- Card 4: Hire an AI Creator -->
          <div class="bg-white dark:bg-gradient-to-br dark:from-slate-900 dark:to-slate-800 rounded-2xl overflow-hidden relative group transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-slate-100 dark:border-slate-700">
            <div class="p-6 flex flex-col h-full">
              <img src="https://placehold.co/600x400/1e293b/ffffff?text=Hire+Creator" alt="Hire Creator" class="w-full h-40 object-cover rounded mb-4" />
              <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-2">Hire an AI Creator</h3>
              <p class="text-slate-600 dark:text-slate-300 text-sm mt-2">Discover world class AI expertise</p>
            </div>
          </div>
        </div>
        
        <!-- Featured Prompts -->
        <div class="mt-24">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Featured Prompts</h2>
            
            <!-- Filter Dropdown -->
            <div class="relative">
              <button 
                @click="showCategoryFilter = !showCategoryFilter"
                class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm text-sm font-medium"
              >
                {{ selectedCategory ? selectedCategory.name : 'All Categories' }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
              
              <div v-if="showCategoryFilter" class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg z-10">
                <ul class="py-2 max-h-60 overflow-auto">
                  <li>
                    <button 
                      @click="filterByCategory(null)" 
                      class="w-full text-left px-4 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700"
                      :class="{'text-primary-600 dark:text-primary-400': !selectedCategory}"
                    >
                      All Categories
                    </button>
                  </li>
                  <li v-for="category in categories" :key="category.id">
                    <button 
                      @click="filterByCategory(category)" 
                      class="w-full text-left px-4 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700"
                      :class="{'text-primary-600 dark:text-primary-400': selectedCategory && selectedCategory.id === category.id}"
                    >
                      {{ category.name }}
                    </button>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <!-- Prompt Cards -->
            <div
              v-for="product in filteredProducts" 
              :key="product.id"
              class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer"
              @click="goToProductDetail(product)"
            >
              <div class="p-1">
                <div class="relative">
                  <img 
                    :src="`/storage/${product.featured_image}`" 
                    :alt="product.name" 
                    class="w-full h-48 object-cover rounded-lg"
                  />
                  <span v-if="product.category" class="absolute top-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded">
                    {{ product.category.name }}
                  </span>
                </div>
                <div class="p-3">
                  <h3 class="font-medium text-slate-900 dark:text-white text-sm line-clamp-2">{{ product.name }}</h3>
                  <div class="flex justify-between items-center mt-2">
                    <span class="font-bold text-slate-900 dark:text-white">{{ formatPrice(product.price) }}</span>
                    <button
                      @click.stop="addToCart(product.id)"
                      class="text-xs px-2 py-1 bg-primary/10 text-primary rounded hover:bg-primary/20 transition-colors">
                      Beli
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Cart Button -->
            <div 
              v-if="filteredProducts.length > 0"
              class="bg-gradient-to-tr from-slate-900/30 to-slate-700/30 backdrop-blur-sm rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center cursor-pointer"
              @click="goToCart"
            >
              <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-200/20 backdrop-blur-sm mx-auto flex items-center justify-center mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <h3 class="font-medium text-white text-sm">Lihat Keranjang</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { StarIcon, ShieldCheckIcon } from '@heroicons/vue/24/solid'
import { onMounted, ref, computed, onBeforeUnmount } from 'vue'
import axios from 'axios'
import { router } from '@inertiajs/vue3'

// Define props untuk menerima data produk
const props = defineProps({
  products: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  }
})

// State untuk filter kategori
const showCategoryFilter = ref(false)
const selectedCategory = ref(null)

// Produk yang difilter berdasarkan kategori
const filteredProducts = computed(() => {
  if (!selectedCategory.value) {
    return props.products
  }
  return props.products.filter(product => 
    product.category_id === selectedCategory.value.id
  )
})

// Fungsi untuk memfilter berdasarkan kategori
const filterByCategory = (category) => {
  selectedCategory.value = category
  showCategoryFilter.value = false
}

// Handler untuk menutup dropdown saat klik di luar
const handleClickOutside = (event) => {
  const dropdown = document.querySelector('.relative')
  if (dropdown && !dropdown.contains(event.target)) {
    showCategoryFilter.value = false
  }
}

onMounted(() => {
  if (typeof window !== 'undefined' && window.AOS) {
    window.AOS.init()
  }
  
  // Tambahkan event listener untuk klik di luar dropdown
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  // Hapus event listener saat komponen dihapus
  document.removeEventListener('click', handleClickOutside)
})

// Format harga dalam format rupiah
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

// Fungsi untuk menambahkan produk ke keranjang
const addToCart = (productId) => {
  // Kirim request ke endpoint cart.add
  axios.post('/cart/add', {
    product_id: productId,
    quantity: 1
  })
  .then(response => {
    // Tampilkan notifikasi sukses jika ada
    if (window.toast) {
      window.toast.success('Produk ditambahkan ke keranjang');
    } else {
      alert('Produk ditambahkan ke keranjang');
    }
    
    // Refresh jumlah item di keranjang jika ada
    if (window.eventBus && window.eventBus.emit) {
      window.eventBus.emit('cartCount', response.data.count);
    }
  })
  .catch(error => {
    console.error('Error adding to cart:', error);
    if (window.toast) {
      window.toast.error('Gagal menambahkan produk ke keranjang');
    } else {
      alert('Gagal menambahkan produk ke keranjang');
    }
  });
};

// Fungsi untuk navigasi ke halaman detail produk
const goToProductDetail = (product) => {
  if (!product || !product.slug) {
    console.error('Slug produk tidak ditemukan:', product);
    return;
  }
  router.visit(`/products/${product.slug}`);
};

// Fungsi untuk navigasi ke halaman keranjang
const goToCart = () => {
  router.visit('/cart');
};
</script> 