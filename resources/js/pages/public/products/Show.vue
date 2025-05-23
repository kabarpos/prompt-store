<template>
  <MainLayout>
    <Head :title="product.name" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-6">
        <Link href="/" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 flex items-center gap-1">
          <ChevronLeft class="h-4 w-4" />
          Kembali ke Beranda
        </Link>
      </div>

      <!-- Breadcrumb -->
      <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-slate-500 dark:text-slate-400">
          <li>
            <Link href="/" class="hover:text-primary-600 dark:hover:text-primary-400">Beranda</Link>
          </li>
          <li class="flex items-center space-x-2">
            <span>/</span>
            <Link v-if="product.category" :href="`/products?category=${product.category.id}`" class="hover:text-primary-600 dark:hover:text-primary-400">
              {{ product.category.name }}
            </Link>
          </li>
          <li class="flex items-center space-x-2">
            <span>/</span>
            <span class="font-medium text-slate-700 dark:text-slate-300">{{ product.name }}</span>
          </li>
        </ol>
      </nav>

      <div class="bg-white dark:bg-slate-900 rounded-xl shadow-md overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="flex flex-col md:flex-row">
          <!-- Product Image Section -->
          <div class="md:w-2/5 p-6">
            <!-- Main product image -->
            <div class="relative aspect-square rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 cursor-pointer" @click="openLightbox(activeImageIndex)">
              <img 
                :src="activeImage" 
                :alt="product.name" 
                class="w-full h-full object-contain"
              />
              <div class="absolute inset-0 bg-black/5 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center">
                <div class="bg-white/90 dark:bg-slate-800/90 p-2 rounded-full">
                  <ZoomIn class="h-5 w-5 text-slate-700 dark:text-slate-200" />
                </div>
              </div>
            </div>
            
            <!-- Image Gallery Thumbnails -->
            <div class="mt-4 grid grid-cols-5 gap-2">
              <div 
                v-for="(image, idx) in galleryImages" 
                :key="idx"
                class="aspect-square rounded-md overflow-hidden border cursor-pointer transition-all duration-200"
                :class="[
                  idx === activeImageIndex 
                    ? 'border-primary-600 ring-2 ring-primary-500/30 dark:border-primary-400 dark:ring-primary-400/30' 
                    : 'border-slate-200 dark:border-slate-700 hover:border-primary-400 dark:hover:border-primary-400'
                ]"
                @click="setActiveImage(idx)"
              >
                <img :src="image.src" :alt="`${product.name} - ${idx + 1}`" class="w-full h-full object-cover" />
              </div>
              
              <!-- Dummy thumbnails jika tidak cukup gambar -->
              <div 
                v-for="idx in Math.max(0, 4 - galleryImages.length)" 
                :key="`dummy-${idx}`"
                class="aspect-square rounded-md overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center"
              >
                <span class="text-xs text-slate-400 dark:text-slate-500">No image</span>
              </div>
            </div>
          </div>

          <!-- Product Details Section -->
          <div class="md:w-3/5 p-6 md:border-l border-gray-200 dark:border-slate-700 space-y-6">
            <!-- Product header info -->
            <div>
              <div class="flex items-center gap-2 mb-2">
                <Badge v-if="product.category" variant="outline" class="px-2 py-0.5">
                  {{ product.category.name }}
                </Badge>
                <Badge variant="outline" class="bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 px-2 py-0.5">
                  Tersedia
                </Badge>
              </div>
              <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ product.name }}</h1>
              <div class="mt-4 flex items-center justify-between">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ formatPrice(product.price) }}</div>
                <div v-if="product.discount_price" class="text-lg text-slate-500 dark:text-slate-400 line-through">
                  {{ formatPrice(product.original_price || product.price * 1.2) }}
                </div>
              </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-slate-700 my-4"></div>

            <!-- Product description -->
            <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300">
              <p>{{ product.description }}</p>
            </div>

            <!-- Product Features -->
            <div v-if="product.product_features && product.product_features.length > 0" class="mt-4">
              <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-3">Fitur Produk</h3>
              <ul class="space-y-2">
                <li v-for="(feature, index) in processedFeatures" :key="index" class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="text-slate-700 dark:text-slate-300">{{ feature }}</span>
                </li>
              </ul>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-slate-700 my-4"></div>

            <!-- Add to Cart Action -->
            <div class="flex flex-col space-y-4">
              <div class="flex items-center gap-4">
                <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-md">
                  <button 
                    @click="decrementQuantity" 
                    class="px-3 py-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200"
                    :disabled="quantity <= 1"
                  >
                    <Minus class="h-4 w-4" />
                  </button>
                  <span class="px-4 py-2 text-slate-900 dark:text-white">{{ quantity }}</span>
                  <button 
                    @click="incrementQuantity" 
                    class="px-3 py-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200"
                  >
                    <Plus class="h-4 w-4" />
                  </button>
                </div>
                <Button @click="addToCart(product.id, quantity)" class="flex-1 py-6">
                  <ShoppingCart class="h-4 w-4 mr-2" />
                  Tambahkan ke Keranjang
                </Button>
              </div>
              <Button variant="outline" class="w-full">
                <Heart class="h-4 w-4 mr-2" />
                Tambahkan ke Wishlist
              </Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="relatedProducts.length > 0" class="mt-16">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">Produk Terkait</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div 
            v-for="related in relatedProducts" 
            :key="related.id" 
            class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer group border border-slate-200 dark:border-slate-700"
            @click="goToProduct(related.slug)"
          >
            <div class="relative aspect-[4/3] overflow-hidden">
              <img 
                :src="`/storage/${related.featured_image}`" 
                :alt="related.name" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
            </div>
            <div class="p-4">
              <h3 class="font-medium text-slate-900 dark:text-white text-sm line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                {{ related.name }}
              </h3>
              <div class="flex justify-between items-center mt-2">
                <span class="font-bold text-slate-900 dark:text-white">{{ formatPrice(related.price) }}</span>
                <button
                  @click.stop="addToCart(related.id)"
                  class="text-xs px-2 py-1 bg-primary/10 text-primary-600 dark:text-primary-400 rounded hover:bg-primary/20 transition-colors">
                  Beli
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Debug info untuk development -->
      <div v-if="$page.props.appDebug" class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-md text-xs">
        <div class="font-bold mb-1">Debug Gallery:</div>
        <div>Total images: {{ galleryImages.length }}</div>
        <div>Has gallery: {{ product.gallery ? 'Yes' : 'No' }}</div>
        <div v-if="product.gallery">Gallery items: {{ product.gallery.length }}</div>
        <pre class="mt-2 overflow-auto max-h-32">{{ JSON.stringify(product.gallery, null, 2) }}</pre>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div 
      v-if="lightboxOpen" 
      class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4 md:p-8"
      @click="closeLightbox"
    >
      <div class="relative w-full max-w-5xl mx-auto" @click.stop>
        <!-- Close button -->
        <button 
          @click="closeLightbox" 
          class="absolute -top-4 -right-4 md:top-0 md:right-0 bg-white dark:bg-slate-800 p-2 rounded-full shadow-lg z-10"
        >
          <X class="h-6 w-6 text-slate-700 dark:text-slate-200" />
        </button>

        <!-- Navigation buttons -->
        <button 
          v-if="lightboxIndex > 0" 
          @click="prevImage"
          class="absolute left-2 top-1/2 -translate-y-1/2 bg-white dark:bg-slate-800 p-2 rounded-full shadow-lg"
        >
          <ChevronLeft class="h-6 w-6 text-slate-700 dark:text-slate-200" />
        </button>

        <button 
          v-if="lightboxIndex < galleryImages.length - 1" 
          @click="nextImage"
          class="absolute right-2 top-1/2 -translate-y-1/2 bg-white dark:bg-slate-800 p-2 rounded-full shadow-lg"
        >
          <ChevronRight class="h-6 w-6 text-slate-700 dark:text-slate-200" />
        </button>

        <!-- Main image -->
        <div class="bg-white/5 rounded-lg overflow-hidden p-2">
          <img 
            :src="galleryImages[lightboxIndex].src" 
            :alt="product.name" 
            class="w-full h-auto max-h-[80vh] object-contain mx-auto" 
          />
        </div>

        <!-- Thumbnails -->
        <div class="flex justify-center mt-4 overflow-auto gap-2 px-2">
          <button 
            v-for="(image, idx) in galleryImages" 
            :key="idx"
            @click="lightboxIndex = idx"
            class="w-16 h-16 rounded-md overflow-hidden transition-opacity duration-200"
            :class="lightboxIndex === idx ? 'ring-2 ring-primary-400' : 'opacity-70 hover:opacity-100'"
          >
            <img :src="image.src" :alt="`Thumbnail ${idx + 1}`" class="w-full h-full object-cover" />
          </button>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/components/layout/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ChevronLeft, ChevronRight, ShoppingCart, CheckCircle, Heart, Plus, Minus, ZoomIn, X } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
  product: Object,
  relatedProducts: Array
});

// Track quantity for cart
const quantity = ref(1);

// Gallery state
const activeImageIndex = ref(0);
const lightboxOpen = ref(false);
const lightboxIndex = ref(0);

// Process gallery images
const galleryImages = computed(() => {
  console.log("Raw product data:", props.product);
  console.log("Gallery data:", props.product.gallery);
  
  const images = [];
  
  // Add main featured image
  if (props.product.featured_image) {
    images.push({
      src: props.product.featured_image.startsWith('/storage/') 
        ? props.product.featured_image
        : `/storage/${props.product.featured_image}`,
      alt: props.product.name
    });
  }
  
  // Add gallery images if they exist
  if (props.product.gallery && Array.isArray(props.product.gallery)) {
    props.product.gallery.forEach((img, idx) => {
      // Periksa berbagai kemungkinan struktur data
      const imagePath = img.image || img.image_path || img.path || img;
      
      if (typeof imagePath === 'string') {
        images.push({
          src: imagePath.startsWith('/storage/') 
            ? imagePath
            : `/storage/${imagePath}`,
          alt: `${props.product.name} - ${idx + 1}`
        });
      }
    });
  }
  
  return images.length > 0 ? images : [{ 
    src: '/images/placeholder.png', 
    alt: 'Placeholder image' 
  }];
});

// Get current active image src
const activeImage = computed(() => {
  return galleryImages.value[activeImageIndex.value].src;
});

// Set active image
const setActiveImage = (index) => {
  activeImageIndex.value = index;
};

// Lightbox functionality
const openLightbox = (index) => {
  lightboxIndex.value = index;
  lightboxOpen.value = true;
  // Disable body scroll when lightbox is open
  document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
  lightboxOpen.value = false;
  // Re-enable body scroll when lightbox is closed
  document.body.style.overflow = '';
};

const nextImage = () => {
  if (lightboxIndex.value < galleryImages.value.length - 1) {
    lightboxIndex.value++;
  }
};

const prevImage = () => {
  if (lightboxIndex.value > 0) {
    lightboxIndex.value--;
  }
};

// When pressing escape, close the lightbox
onMounted(() => {
  const handleKeyDown = (e) => {
    if (e.key === 'Escape' && lightboxOpen.value) {
      closeLightbox();
    } else if (e.key === 'ArrowRight' && lightboxOpen.value) {
      nextImage();
    } else if (e.key === 'ArrowLeft' && lightboxOpen.value) {
      prevImage();
    }
  };
  
  window.addEventListener('keydown', handleKeyDown);
  
  return () => {
    window.removeEventListener('keydown', handleKeyDown);
  };
});

const incrementQuantity = () => {
  quantity.value++;
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

// Mengolah fitur produk agar ditampilkan dengan benar
const processedFeatures = computed(() => {
  if (!props.product.product_features) return [];
  
  // Jika sudah berupa array string, gunakan langsung
  if (Array.isArray(props.product.product_features) && 
      props.product.product_features.every(item => typeof item === 'string')) {
    return props.product.product_features;
  }
  
  // Jika berupa array objek (dari form builder), ambil text/valuenya
  if (Array.isArray(props.product.product_features) && 
      props.product.product_features.some(item => typeof item === 'object')) {
    return props.product.product_features.map(item => {
      if (typeof item === 'object' && item !== null) {
        return item.text || item.value || JSON.stringify(item);
      }
      return String(item);
    });
  }
  
  // Jika berupa string JSON, coba parse
  if (typeof props.product.product_features === 'string') {
    try {
      const parsed = JSON.parse(props.product.product_features);
      if (Array.isArray(parsed)) {
        return parsed.map(item => {
          if (typeof item === 'object' && item !== null) {
            return item.text || item.value || JSON.stringify(item);
          }
          return String(item);
        });
      }
    } catch (e) {
      // Jika bukan JSON valid, split berdasarkan koma atau baris baru
      return props.product.product_features
        .split(/[,\n]+/)
        .map(item => item.trim())
        .filter(item => item.length > 0);
    }
  }
  
  // Fallback: konversi ke string dan split by karakter
  return [String(props.product.product_features)];
});

// Format harga dalam format rupiah
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

// Fungsi untuk menambahkan produk ke keranjang
const addToCart = (productId, qty = 1) => {
  // Kirim request ke endpoint cart.add
  axios.post('/cart/add', {
    product_id: productId,
    quantity: qty
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

// Fungsi untuk navigasi ke halaman produk lain
const goToProduct = (slug) => {
  router.visit(`/products/${slug}`);
};
</script> 