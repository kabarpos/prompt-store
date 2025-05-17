<template>
  <Head title="Lacak Pesanan" />

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

        <h1 class="text-4xl font-bold mb-8 text-center text-foreground bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-400" data-aos="fade-up">Lacak Pesanan</h1>

        <!-- Search Form -->
        <div class="max-w-xl mx-auto mb-12" data-aos="fade-up" data-aos-delay="100">
          <div class="bg-background/60 backdrop-blur-sm rounded-xl shadow-sm p-6 border border-border">
            <form @submit.prevent="trackOrder">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <label for="order_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Pesanan <span class="text-red-500">*</span></label>
                  <input 
                    id="order_number"
                    v-model="form.order_number" 
                    type="text" 
                    placeholder="Masukkan nomor pesanan Anda" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                    required
                  />
                </div>
                <div>
                  <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                  <input 
                    id="customer_phone"
                    v-model="form.customer_phone" 
                    type="text" 
                    placeholder="Masukkan nomor WhatsApp Anda" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                    required
                  />
                </div>
              </div>
              <div class="flex justify-center">
                <Button 
                  type="submit" 
                  class="w-full md:w-auto md:min-w-[200px]"
                  colorScheme="primary"
                  :disabled="isSubmitting"
                >
                  <SearchIcon v-if="!isSubmitting" class="h-4 w-4 mr-2" />
                  <span v-else class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                  {{ isSubmitting ? 'Mencari...' : 'Lacak Pesanan' }}
                </Button>
              </div>
            </form>
          </div>
        </div>

        <!-- Order Details -->
        <div v-if="order" class="max-w-4xl mx-auto" data-aos="fade-up" data-aos-delay="200">
          <div class="bg-background/60 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden border border-border">
            <!-- Order Status Card -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-8 mb-8">
              <div class="text-center mb-8">
                <div class="mb-4">
                  <div 
                    class="mx-auto h-20 w-20 flex items-center justify-center rounded-full"
                    :class="{
                      'bg-yellow-100': order.status === 'pending',
                      'bg-blue-100': order.status === 'processing',
                      'bg-purple-100': order.status === 'review',
                      'bg-green-100': order.status === 'completed',
                      'bg-red-100': order.status === 'cancelled'
                    }"
                  >
                    <ClockIcon v-if="order.status === 'pending'" class="h-10 w-10 text-yellow-600" />
                    <ActivityIcon v-else-if="order.status === 'processing'" class="h-10 w-10 text-blue-600" />
                    <SearchIcon v-else-if="order.status === 'review'" class="h-10 w-10 text-purple-600" />
                    <CheckCircleIcon v-else-if="order.status === 'completed'" class="h-10 w-10 text-green-600" />
                    <XCircleIcon v-else-if="order.status === 'cancelled'" class="h-10 w-10 text-red-600" />
                  </div>
                </div>
                <h2 class="text-xl font-semibold mb-2">Pesanan #{{ order.order_number }}</h2>
                <div class="inline-block">
                  <Badge 
                    variant="outline" 
                    class="text-sm px-3 py-1"
                    :class="{
                      'border-yellow-400 text-yellow-600 bg-yellow-50': order.status === 'pending',
                      'border-blue-400 text-blue-600 bg-blue-50': order.status === 'processing',
                      'border-purple-400 text-purple-600 bg-purple-50': order.status === 'review',
                      'border-green-400 text-green-600 bg-green-50': order.status === 'completed',
                      'border-red-400 text-red-600 bg-red-50': order.status === 'cancelled'
                    }"
                  >
                    {{ getStatusLabel(order.status) }}
                  </Badge>
                </div>
                <p class="text-gray-500 mt-2">{{ formatDate(order.created_at) }}</p>
              </div>
              
              <!-- Order Progress -->
              <div class="max-w-2xl mx-auto">
                <div class="relative">
                  <!-- Progress Line -->
                  <div class="absolute top-5 left-[15px] right-[15px] h-0.5 bg-gray-200"></div>
                  
                  <!-- Progress Steps -->
                  <div class="relative flex justify-between">
                    <!-- Pending Step -->
                    <div class="flex flex-col items-center">
                      <div 
                        class="w-10 h-10 rounded-full flex items-center justify-center z-10"
                        :class="order.status === 'pending' || order.status === 'processing' || order.status === 'review' || order.status === 'completed' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-400'"
                      >
                        <ShoppingCartIcon class="h-5 w-5" />
                      </div>
                      <div class="text-xs text-center mt-2">Pesanan Dibuat</div>
                    </div>
                    
                    <!-- Processing Step -->
                    <div class="flex flex-col items-center">
                      <div 
                        class="w-10 h-10 rounded-full flex items-center justify-center z-10"
                        :class="order.status === 'processing' || order.status === 'review' || order.status === 'completed' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-400'"
                      >
                        <ClipboardIcon class="h-5 w-5" />
                      </div>
                      <div class="text-xs text-center mt-2">Diproses</div>
                    </div>
                    
                    <!-- Review Step -->
                    <div class="flex flex-col items-center">
                      <div 
                        class="w-10 h-10 rounded-full flex items-center justify-center z-10"
                        :class="order.status === 'review' || order.status === 'completed' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-400'"
                      >
                        <SearchIcon class="h-5 w-5" />
                      </div>
                      <div class="text-xs text-center mt-2">Review</div>
                    </div>
                    
                    <!-- Completed Step -->
                    <div class="flex flex-col items-center">
                      <div 
                        class="w-10 h-10 rounded-full flex items-center justify-center z-10"
                        :class="order.status === 'completed' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-400'"
                      >
                        <CheckIcon class="h-5 w-5" />
                      </div>
                      <div class="text-xs text-center mt-2">Selesai</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Order Details -->
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Customer Info -->
                <div>
                  <h4 class="text-sm font-medium text-gray-500 mb-2">Informasi Pelanggan</h4>
                  <div class="bg-gray-50 rounded-lg p-4">
                    <div class="mb-2">
                      <p class="text-xs text-gray-500">Nama</p>
                      <p class="font-medium">{{ order.customer_name }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500">Nomor WhatsApp</p>
                      <p class="font-medium">{{ order.customer_phone }}</p>
                    </div>
                  </div>
                </div>
                
                <!-- Payment Summary -->
                <div>
                  <h4 class="text-sm font-medium text-gray-500 mb-2">Ringkasan Pembayaran</h4>
                  <div class="bg-gray-50 rounded-lg p-4">
                    <div class="space-y-1 mb-2">
                      <div class="flex justify-between">
                        <p class="text-xs text-gray-500">Subtotal</p>
                        <p class="text-sm font-medium">{{ formatPrice(order.subtotal) }}</p>
                      </div>
                      <div class="flex justify-between">
                        <p class="text-xs text-gray-500">Biaya Admin</p>
                        <p class="text-sm font-medium">{{ formatPrice(order.admin_fee) }}</p>
                      </div>
                      <div class="flex justify-between">
                        <p class="text-xs text-gray-500">Diskon</p>
                        <p class="text-sm font-medium text-green-600">-{{ formatPrice(order.discount) }}</p>
                      </div>
                    </div>
                    <div class="pt-2 border-t border-gray-200 flex justify-between">
                      <p class="text-sm font-medium">Total</p>
                      <p class="text-sm font-bold text-primary-600">{{ formatPrice(order.total_amount) }}</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Payment Information - NEW SECTION -->
              <div v-if="order.payment" class="mb-6">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Informasi Pembayaran</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                  <!-- Payment Method -->
                  <div class="flex flex-col space-y-2">
                    <!-- Payment Status & Amount -->
                    <div class="bg-white border border-gray-200 rounded-lg p-3">
                      <div class="flex justify-between items-start mb-2">
                        <div>
                          <p class="text-xs text-gray-500">Status Pembayaran</p>
                          <Badge 
                            variant="outline" 
                            class="text-sm px-3 py-1 mt-1"
                            :class="{
                              'border-yellow-400 text-yellow-600 bg-yellow-50': order.payment.status === 'pending',
                              'border-green-400 text-green-600 bg-green-50': order.payment.status === 'completed',
                              'border-red-400 text-red-600 bg-red-50': order.payment.status === 'failed'
                            }"
                          >
                            {{ getPaymentStatusLabel(order.payment.status) }}
                          </Badge>
                        </div>
                        
                        <!-- Total dengan tombol salin -->
                        <div class="bg-primary-50 border border-primary-100 rounded-lg p-2 flex items-center gap-2">
                          <div>
                            <p class="text-xs text-gray-600">Total:</p>
                            <p class="text-sm font-bold text-primary-600">{{ formatPrice(order.payment.amount) }}</p>
                          </div>
                          <button
                            @click="copyToClipboard(order.payment.amount.toString())"
                            class="text-primary-600 hover:text-primary-700 p-1 rounded-md hover:bg-primary-100"
                            title="Salin jumlah pembayaran"
                          >
                            <ClipboardCopyIcon class="h-4 w-4" />
                          </button>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Daftar Rekening Pembayaran Aktif -->
                    <div v-if="paymentMethods && paymentMethods.length > 0">
                      <p class="text-xs text-gray-500 font-medium mb-2">Rekening Pembayaran:</p>
                      <div v-for="method in paymentMethods.filter(m => m.type === 'bank_transfer')" :key="method.id" 
                          class="border border-gray-200 rounded-lg p-3 bg-white mb-2">
                        <div class="flex items-center mb-2">
                          <img 
                            v-if="method.logo" 
                            :src="`/storage/${method.logo}`" 
                            class="h-6 mr-2 object-contain" 
                            :alt="method.name" 
                          />
                          <p class="font-medium">{{ method.name }}</p>
                        </div>
                        <div class="space-y-2 text-sm">
                          <p><span class="font-medium">Bank:</span> {{ method.bank_name }}</p>
                          
                          <div class="flex items-center justify-between bg-gray-50 rounded p-2">
                            <p><span class="font-medium">No. Rekening:</span> {{ method.account_number }}</p>
                            <button
                              @click="copyToClipboard(method.account_number)"
                              class="text-primary-600 hover:text-primary-700 p-1 rounded-md hover:bg-primary-50 text-xs"
                              title="Salin nomor rekening"
                            >
                              <ClipboardCopyIcon class="h-4 w-4" />
                            </button>
                          </div>
                          
                          <p><span class="font-medium">Atas Nama:</span> {{ method.account_name }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Order Items -->
              <h4 class="text-sm font-medium text-gray-500 mb-2">Produk yang Dipesan</h4>
              <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                <div 
                  v-for="(item, index) in order.items" 
                  :key="item.id" 
                  :class="{'border-t border-gray-200': index > 0}"
                  class="p-4 flex items-center"
                >
                  <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                    <img 
                      :src="`/storage/${item.product.featured_image}`" 
                      :alt="item.product.name" 
                      class="h-full w-full object-cover object-center"
                    />
                  </div>
                  <div class="ml-4 flex-1">
                    <h4 class="text-sm font-medium">{{ item.product.name }}</h4>
                    <div class="mt-1 flex justify-between">
                      <p class="text-sm text-gray-500">{{ formatPrice(item.price) }} x {{ item.quantity }}</p>
                      <p class="text-sm font-medium">{{ formatPrice(item.subtotal) }}</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Notes -->
              <div v-if="order.notes || order.admin_notes" class="mt-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Catatan</h4>
                
                <!-- Customer Notes -->
                <div v-if="order.notes" class="bg-gray-50 rounded-lg p-4 mb-3">
                  <p class="text-xs text-gray-500 mb-1">Catatan Pelanggan:</p>
                  <p class="text-sm text-gray-700">{{ order.notes }}</p>
                </div>
                
                <!-- Admin Notes -->
                <div v-if="order.admin_notes" class="bg-blue-50 rounded-lg p-4">
                  <p class="text-xs text-gray-500 mb-1">Catatan Admin:</p>
                  <p class="text-sm text-gray-700">{{ order.admin_notes }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Not Found State -->
        <div v-else-if="showNotFound" class="max-w-xl mx-auto text-center" data-aos="fade-up" data-aos-delay="200">
          <div class="bg-background/60 backdrop-blur-sm rounded-xl shadow-sm p-8 border border-border">
            <div class="mb-4">
              <div class="mx-auto h-20 w-20 flex items-center justify-center rounded-full bg-red-100">
                <XIcon class="h-10 w-10 text-red-600" />
              </div>
            </div>
            <h2 class="text-xl font-semibold mb-2">Pesanan Tidak Ditemukan</h2>
            <p class="text-gray-600 mb-6">{{ message }}</p>
            <Button variant="outline" colorScheme="outline" @click="resetForm">Coba Lagi</Button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import Breadcrumb from '@/components/ui/breadcrumb.vue';
import MainLayout from '@/components/layout/MainLayout.vue';
import { 
  SearchIcon, 
  XIcon, 
  ClockIcon, 
  CheckCircleIcon, 
  XCircleIcon, 
  ActivityIcon,
  ShoppingCartIcon,
  ClipboardIcon,
  CheckIcon,
  ClipboardCopy as ClipboardCopyIcon
} from 'lucide-vue-next';
import AOS from 'aos';

const props = defineProps({
  order: Object,
  found: {
    type: Boolean,
    default: null
  },
  message: {
    type: String,
    default: 'Pesanan tidak ditemukan dengan nomor pesanan dan nomor telepon yang Anda masukkan.'
  },
  paymentMethods: Array
});

// State
const isSubmitting = ref(false);
const form = ref({
  order_number: '',
  customer_phone: ''
});

// Breadcrumb items
const breadcrumbItems = computed(() => [
  { label: 'Beranda', href: route('home') },
  { label: 'Lacak Pesanan' }
]);

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

const getStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Konfirmasi',
    'processing': 'Sedang Diproses',
    'review': 'Review',
    'completed': 'Selesai',
    'cancelled': 'Dibatalkan'
  };
  
  return statusMap[status] || status;
};

const getPaymentStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Pembayaran',
    'completed': 'Pembayaran Diterima',
    'failed': 'Pembayaran Gagal'
  };
  
  return statusMap[status] || status;
};

const trackOrder = () => {
  if (isSubmitting.value) return;
  
  isSubmitting.value = true;
  
  router.get(route('orders.track', { order: form.value.order_number }), {
    preserveState: true,
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const resetForm = () => {
  form.value = {
    order_number: '',
    customer_phone: ''
  };
};

// Fungsi untuk menyalin teks ke clipboard
const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text)
    .then(() => {
      // Tampilkan notifikasi sukses disalin
      const notification = document.createElement('div');
      notification.textContent = 'Berhasil disalin!';
      notification.className = 'fixed left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white px-4 py-2 rounded-md shadow-md z-50';
      document.body.appendChild(notification);
      
      // Tambahkan animasi fade-in dan fade-out
      notification.style.animation = 'fadeInOut 1.5s ease-in-out';
      
      // Buat style untuk animasi
      const style = document.createElement('style');
      style.textContent = `
        @keyframes fadeInOut {
          0% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
          20% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
          80% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
          100% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
        }
      `;
      document.head.appendChild(style);
      
      // Hilangkan notifikasi setelah 2 detik
      setTimeout(() => {
        notification.remove();
        style.remove();
      }, 1500);
    })
    .catch(err => {
      console.error('Gagal menyalin teks: ', err);
    });
};

// Init form values from URL params
watch(() => window.location.search, (search) => {
  const params = new URLSearchParams(search);
  
  if (params.has('order_number')) {
    form.value.order_number = params.get('order_number');
  }
  
  if (params.has('customer_phone')) {
    form.value.customer_phone = params.get('customer_phone');
  }
}, { immediate: true });

// Initialize AOS
onMounted(() => {
  AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
  });
});
</script> 