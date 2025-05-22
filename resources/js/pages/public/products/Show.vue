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

      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md overflow-hidden">
        <div class="md:flex">
          <!-- Product Image -->
          <div class="md:w-1/2">
            <div class="relative h-96 md:h-full">
              <img 
                :src="`/storage/${product.featured_image}`" 
                :alt="product.name" 
                class="w-full h-full object-cover"
              />
            </div>
          </div>

          <!-- Product Details -->
          <div class="md:w-1/2 p-8 md:border-l border-gray-200 dark:border-slate-700 space-y-6">
            <div>
              <div class="flex items-center gap-2 mb-2">
                <Badge v-if="product.category" variant="outline" class="px-2 py-0.5">
                  {{ product.category.name }}
                </Badge>
                <Badge variant="outline" class="bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 px-2 py-0.5">
                  Tersedia
                </Badge>
              </div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ product.name }}</h1>
              <div class="mt-4">
                <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ formatPrice(product.price) }}</div>
              </div>
            </div>

            <div class="prose dark:prose-invert max-w-none">
              <p>{{ product.description }}</p>
            </div>

            <!-- Product Features -->
            <div v-if="product.product_features && product.product_features.length > 0" class="mt-6 space-y-2">
              <h3 class="text-lg font-medium text-slate-900 dark:text-white">Fitur Produk</h3>
              <ul class="list-disc list-inside space-y-1">
                <li v-for="(feature, index) in processedFeatures" :key="index" class="text-slate-700 dark:text-slate-300">
                  {{ feature }}
                </li>
              </ul>
            </div>

            <!-- Add to Cart Button -->
            <div class="pt-4">
              <Button @click="addToCart(product.id)" class="w-full">
                <ShoppingCart class="h-4 w-4 mr-2" />
                Tambahkan ke Keranjang
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
            class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-200"
            @click="goToProduct(related.slug)"
          >
            <div class="relative h-48">
              <img 
                :src="`/storage/${related.featured_image}`" 
                :alt="related.name" 
                class="w-full h-full object-cover"
              />
            </div>
            <div class="p-4">
              <h3 class="font-medium text-slate-900 dark:text-white text-sm line-clamp-2">{{ related.name }}</h3>
              <div class="flex justify-between items-center mt-2">
                <span class="font-bold text-slate-900 dark:text-white">{{ formatPrice(related.price) }}</span>
                <button
                  @click.stop="addToCart(related.id)"
                  class="text-xs px-2 py-1 bg-primary/10 text-primary rounded hover:bg-primary/20 transition-colors">
                  Beli
                </button>
              </div>
            </div>
          </div>
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
import { ChevronLeft, ShoppingCart } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed } from 'vue';

const props = defineProps({
  product: Object,
  relatedProducts: Array
});

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

// Fungsi untuk navigasi ke halaman produk lain
const goToProduct = (slug) => {
  router.visit(`/products/${slug}`);
};
</script> 