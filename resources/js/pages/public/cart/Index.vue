<template>
  <Head title="Keranjang Belanja" />

  <MainLayout>
    <div class="bg-background py-24 relative overflow-hidden">
      <!-- Background Decorations -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-1/2 -right-1/2 w-96 h-96 rounded-full bg-primary/5 blur-3xl"></div>
        <div class="absolute -bottom-1/2 -left-1/2 w-96 h-96 rounded-full bg-blue-500/5 blur-3xl"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-grid-slate-200/40 dark:bg-grid-slate-800/40 bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_80%_80%_at_50%_50%,black,transparent)]"></div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <!-- Breadcrumb -->
        <div class="bg-background/60 backdrop-blur-sm rounded-lg shadow-sm mb-8 border border-border px-6 py-4" data-aos="fade-up">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
        
        <h1 class="text-4xl font-bold mb-8 text-center text-foreground bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-400" data-aos="fade-up">Keranjang Belanja</h1>
        
        <div v-if="cartItems.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Cart Items -->
          <div class="lg:col-span-2" data-aos="fade-up" data-aos-delay="100">
            <div class="bg-background/60 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden border border-border">
              <!-- Cart Items Table -->
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-slate-900">
                <thead>
                  <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Produk
                    </th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Harga
                    </th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Jumlah
                    </th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Subtotal
                    </th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200">
                  <tr v-for="item in cartItems" :key="item.id" class="hover:bg-gray-50">
                    <!-- Product -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-gray-700">
                          <img 
                            :src="`/storage/${item.product.featured_image}`" 
                            :alt="item.product.name" 
                            class="h-full w-full object-cover object-center"
                          />
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                            {{ item.product.name }}
                          </div>
                          <div v-if="item.product.category" class="text-xs text-gray-500 dark:text-primary">
                            {{ item.product.category.name }}
                          </div>
                        </div>
                      </div>
                    </td>
                    
                    <!-- Price -->
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-white">
                      {{ formatPrice(item.product.price) }}
                    </td>
                    
                    <!-- Quantity -->
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-white">
                      <div class="inline-flex items-center border border-gray-300 rounded-md">
                        <button 
                          @click="decrementQuantity(item)" 
                          class="px-3 py-1 text-gray-600 hover:bg-gray-100"
                          :disabled="item.quantity <= 1 || isUpdating(item.id)"
                        >
                          -
                        </button>
                        <span class="px-4 py-1 text-center w-12">{{ item.quantity }}</span>
                        <button 
                          @click="incrementQuantity(item)" 
                          class="px-3 py-1 text-gray-600 hover:bg-gray-100"
                          :disabled="isUpdating(item.id)"
                        >
                          +
                        </button>
                      </div>
                    </td>
                    
                    <!-- Subtotal -->
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white">
                      {{ formatPrice(item.product.price * item.quantity) }}
                    </td>
                    
                    <!-- Actions -->
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-white">
                      <button 
                        @click="removeItem(item)" 
                        class="text-red-600 hover:text-red-900"
                        :disabled="isRemoving(item.id)"
                      >
                        <TrashIcon v-if="!isRemoving(item.id)" class="h-5 w-5" />
                        <div v-else class="h-5 w-5 animate-spin rounded-full border-2 border-red-500 border-t-transparent mx-auto"></div>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
              
              <!-- Cart Actions -->
              <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <Link :href="route('products.index')" class="inline-flex items-center">
                  <Button variant="default" colorScheme="primary">Lanjut Belanja</Button>
                </Link>
                <div>
                  <Button variant="destructive" colorScheme="destructive" @click="goToCheckout">
                    Lanjut ke Checkout
                    <ArrowRightIcon class="ml-2 h-4 w-4" />
                  </Button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Order Summary -->
          <div class="lg:col-span-1" data-aos="fade-up" data-aos-delay="200">
            <div class="bg-background/60 backdrop-blur-sm rounded-xl shadow-sm p-6 border border-border">
              <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Ringkasan Pesanan</h2>
              
              <!-- Coupon Form -->
              <CouponForm 
                :initial-coupon="coupon" 
                @coupon-applied="handleCouponApplied" 
                @coupon-removed="handleCouponRemoved"
              />
              
              <!-- Summary Items -->
              <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                  <p class="text-gray-600 dark:text-white">Subtotal ({{ summary.itemCount }} item)</p>
                  <p class="font-medium">{{ formatPrice(summary.subtotal) }}</p>
                </div>
                <div class="flex justify-between">
                  <p class="text-gray-600 dark:text-white">Biaya Admin</p>
                  <p class="font-medium">{{ formatPrice(summary.adminFee) }}</p>
                </div>
                <div class="flex justify-between">
                  <p class="text-gray-600 dark:text-white">Diskon</p>
                  <p class="font-medium text-green-600 dark:text-white">-{{ formatPrice(summary.discount) }}</p>
                </div>
              </div>
              
              <!-- Total -->
              <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-lg font-medium text-gray-900 dark:text-white">Total</p>
                <p class="text-lg font-bold text-primary-600 dark:text-white">{{ formatPrice(summary.total) }}</p>
              </div>
              
              <!-- Checkout Button -->
              <Button 
                class="w-full mt-6"
                colorScheme="primary"
                @click="goToCheckout"
              >
                Lanjut ke Checkout
              </Button>
            </div>
          </div>
        </div>
        
        <!-- Empty Cart State -->
        <div v-else class="text-center py-16 bg-background/60 backdrop-blur-sm rounded-xl shadow-sm border border-border" data-aos="fade-up">
          <div class="text-6xl mb-4">🛒</div>
          <h3 class="text-2xl font-semibold mb-2">Keranjang Belanja Kosong</h3>
          <p class="text-gray-500 mb-6 max-w-md mx-auto">Sepertinya Anda belum menambahkan produk apa pun ke keranjang belanja Anda</p>
          <Link :href="route('products.index')">
            <Button colorScheme="primary">Mulai Belanja</Button>
          </Link>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import Breadcrumb from '@/components/ui/breadcrumb.vue';
import MainLayout from '@/components/layout/MainLayout.vue';
import { computed, ref, onMounted } from 'vue';
import { ArrowRightIcon, TrashIcon } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import axios from 'axios';
import CouponForm from '@/components/coupons/CouponForm.vue';
import AOS from 'aos';

const props = defineProps({
  cartItems: Array,
  summary: Object,
  coupon: Object
});

// State
const updatingItems = ref(new Set());
const removingItems = ref(new Set());

// Breadcrumb items
const breadcrumbItems = computed(() => [
  { label: 'Beranda', href: route('home') },
  { label: 'Keranjang Belanja' }
]);

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

const isUpdating = (itemId) => {
  return updatingItems.value.has(itemId);
};

const isRemoving = (itemId) => {
  return removingItems.value.has(itemId);
};

const updateQuantity = (item, newQuantity) => {
  if (isUpdating(item.id)) return;
  
  updatingItems.value.add(item.id);
  
  axios.patch(route('cart.update', item.id), {
    quantity: newQuantity
  })
  .then(response => {
    if (response.data.success) {
      // Refresh the page to update the cart
      router.reload();
    } else {
      toast.error(response.data.message);
    }
  })
  .catch(error => {
    toast.error('Gagal memperbarui jumlah produk');
  })
  .finally(() => {
    updatingItems.value.delete(item.id);
  });
};

const incrementQuantity = (item) => {
  updateQuantity(item, item.quantity + 1);
};

const decrementQuantity = (item) => {
  if (item.quantity > 1) {
    updateQuantity(item, item.quantity - 1);
  }
};

const removeItem = (item) => {
  if (isRemoving(item.id)) return;
  
  removingItems.value.add(item.id);
  
  axios.delete(route('cart.remove', item.id))
  .then(response => {
    if (response.data.success) {
      toast.success(response.data.message);
      router.reload();
    } else {
      toast.error(response.data.message);
    }
  })
  .catch(error => {
    toast.error('Gagal menghapus produk dari keranjang');
  })
  .finally(() => {
    removingItems.value.delete(item.id);
  });
};

const goToCheckout = () => {
  router.get(route('checkout'));
};

// Handle coupon
const handleCouponApplied = (coupon) => {
  router.reload({
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Kupon berhasil diterapkan');
    }
  });
};

const handleCouponRemoved = () => {
  router.reload({
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Kupon berhasil dihapus');
    }
  });
};

// Initialize AOS
onMounted(() => {
  AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
  });
});
</script> 