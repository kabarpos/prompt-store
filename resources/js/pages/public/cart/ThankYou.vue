<template>
  <Head title="Terima Kasih" />

  <MainLayout>
    <div class="bg-background py-24 relative overflow-hidden">
      <!-- Background Decorations -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-1/2 -right-1/2 w-96 h-96 rounded-full bg-primary/5 blur-3xl"></div>
        <div class="absolute -bottom-1/2 -left-1/2 w-96 h-96 rounded-full bg-blue-500/5 blur-3xl"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-grid-slate-200/40 dark:bg-grid-slate-800/40 bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_80%_80%_at_50%_50%,black,transparent)]"></div>
      </div>

      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <!-- Breadcrumb -->
        <div class="bg-background/60 backdrop-blur-sm rounded-lg shadow-sm mb-8 border border-border px-6 py-4" data-aos="fade-up">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
        
        <!-- Success Message -->
        <div class="bg-background/60 backdrop-blur-sm rounded-xl shadow-sm p-8 text-center mb-8 border border-border" data-aos="fade-up" data-aos-delay="100">
          <div class="mb-4">
            <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-green-100">
              <CheckCircleIcon class="h-16 w-16 text-green-600" />
            </div>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesanan Berhasil!</h1>
          <p class="text-lg text-gray-600 mb-6">Terima kasih telah melakukan pemesanan di toko kami.</p>
          <div class="inline-block bg-gray-100 rounded-lg px-6 py-3 mb-6">
            <p class="text-gray-700 font-medium">Nomor Pesanan:</p>
            <p class="text-2xl font-bold text-primary-600">{{ order.order_number }}</p>
          </div>
          <p class="text-gray-600 mb-8">Silakan simpan nomor pesanan ini sebagai referensi. Anda juga dapat melacak status pesanan Anda menggunakan tombol di bawah.</p>
          <div class="flex flex-col sm:flex-row justify-center gap-4">
            <Link :href="route('orders.track', { order: order.order_number })" class="inline-flex items-center justify-center">
              <Button colorScheme="primary" class="px-6">
                <SearchIcon class="h-4 w-4 mr-2" />
                Lacak Pesanan
              </Button>
            </Link>
            <Link :href="route('home')" class="inline-flex items-center justify-center">
              <Button variant="outline" colorScheme="outline" class="px-6">
                <ShoppingBagIcon class="h-4 w-4 mr-2" />
                Lanjut Belanja
              </Button>
            </Link>
          </div>
        </div>
        
        <!-- Order Details -->
        <div class="bg-background/60 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden border border-border" data-aos="fade-up" data-aos-delay="200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold mb-0">Detail Pesanan</h2>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
            <!-- Customer Info -->
            <div>
              <h3 class="text-lg font-medium mb-3">Informasi Pemesan</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="mb-3">
                  <p class="text-sm text-gray-500">Nama</p>
                  <p class="font-medium">{{ order.customer_name }}</p>
                </div>
                <div class="mb-3">
                  <p class="text-sm text-gray-500">Nomor WhatsApp</p>
                  <p class="font-medium">{{ order.customer_phone }}</p>
                </div>
                <div v-if="order.customer_email">
                  <p class="text-sm text-gray-500">Email</p>
                  <p class="font-medium">{{ order.customer_email }}</p>
                </div>
              </div>
            </div>
            
            <!-- Order Summary -->
            <div>
              <h3 class="text-lg font-medium mb-3">Ringkasan Pembayaran</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="space-y-2 mb-3">
                  <div class="flex justify-between">
                    <p class="text-sm text-gray-500">Subtotal</p>
                    <p class="font-medium">{{ formatPrice(order.subtotal) }}</p>
                  </div>
                  <div class="flex justify-between">
                    <p class="text-sm text-gray-500">Biaya Admin</p>
                    <p class="font-medium">{{ formatPrice(order.admin_fee) }}</p>
                  </div>
                  <div class="flex justify-between">
                    <p class="text-sm text-gray-500">Diskon</p>
                    <p class="font-medium text-green-600">-{{ formatPrice(order.discount) }}</p>
                  </div>
                </div>
                <div class="pt-3 border-t border-gray-200 flex justify-between">
                  <p class="font-medium">Total</p>
                  <p class="font-bold text-primary-600">{{ formatPrice(order.total_amount) }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Order Items -->
          <div class="p-6 border-t border-gray-200">
            <h3 class="text-lg font-medium mb-4">Produk yang Dipesan</h3>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
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
          </div>
          
          <!-- Notes and Status -->
          <div class="p-6 border-t border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Notes -->
            <div>
              <h3 class="text-lg font-medium mb-3">Catatan Pesanan</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <p v-if="order.notes" class="text-gray-700">{{ order.notes }}</p>
                <p v-else class="text-gray-500 italic">Tidak ada catatan</p>
              </div>
            </div>
            
            <!-- Status -->
            <div>
              <h3 class="text-lg font-medium mb-3">Status Pesanan</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="inline-flex items-center">
                  <span 
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                    :class="{
                      'bg-yellow-100 text-yellow-800': order.status === 'pending',
                      'bg-blue-100 text-blue-800': order.status === 'processing',
                      'bg-purple-100 text-purple-800': order.status === 'review',
                      'bg-green-100 text-green-800': order.status === 'completed',
                      'bg-red-100 text-red-800': order.status === 'cancelled'
                    }"
                  >
                    <Badge 
                      variant="outline" 
                      :class="{
                        'border-yellow-400 text-yellow-600': order.status === 'pending',
                        'border-blue-400 text-blue-600': order.status === 'processing',
                        'border-purple-400 text-purple-600': order.status === 'review',
                        'border-green-400 text-green-600': order.status === 'completed',
                        'border-red-400 text-red-600': order.status === 'cancelled'
                      }"
                    >
                      {{ getStatusLabel(order.status) }}
                    </Badge>
                  </span>
                </div>
                <p class="mt-2 text-sm text-gray-500">Pesanan Anda akan segera diproses oleh tim kami.</p>
              </div>
            </div>
          </div>
          
          <!-- Payment Information -->
          <div class="p-6 border-t border-gray-200">
            <h3 class="text-lg font-medium mb-3">Informasi Pembayaran</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Bank Transfer Info -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-base mb-3">Metode Pembayaran</h4>
                
                <!-- Daftar Rekening Pembayaran Aktif -->
                <div v-if="paymentMethods && paymentMethods.length > 0">
                  <div v-for="method in paymentMethods.filter(m => m.type === 'bank_transfer')" :key="method.id" 
                      class="border border-gray-200 rounded-lg p-3 bg-white mb-3">
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
                
                <div v-else class="text-gray-500 italic">
                  Silakan hubungi admin kami untuk informasi pembayaran.
                </div>
                
                <!-- Payment Status -->
                <div v-if="order.payment" class="bg-white border border-gray-200 rounded-lg p-3 mt-3">
                  <p class="text-sm font-medium text-gray-600 mb-2">Status Pembayaran</p>
                  <Badge 
                    variant="outline" 
                    class="text-sm px-3 py-1"
                    :class="{
                      'border-yellow-400 text-yellow-600 bg-yellow-50': order.payment.status === 'pending',
                      'border-green-400 text-green-600 bg-green-50': order.payment.status === 'completed',
                      'border-red-400 text-red-600 bg-red-50': order.payment.status === 'failed'
                    }"
                  >
                    {{ getPaymentStatusLabel(order.payment.status) }}
                  </Badge>
                </div>
              </div>
              
              <!-- Payment Instructions -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-base mb-3">Petunjuk Pembayaran</h4>
                
                <!-- Jumlah yang harus dibayar dengan tombol salin -->
                <div class="bg-white border border-primary-100 rounded-lg p-3 mb-4 flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pembayaran:</p>
                    <p class="text-xl font-bold text-primary-600">{{ formatPrice(order.total_amount) }}</p>
                  </div>
                  <button
                    @click="copyToClipboard(order.total_amount.toString())"
                    class="bg-primary-50 text-primary-600 hover:bg-primary-100 p-2 rounded-md"
                    title="Salin jumlah pembayaran"
                  >
                    <ClipboardCopyIcon class="h-5 w-5" />
                  </button>
                </div>
                
                <ol class="list-decimal list-inside space-y-2 text-sm ml-2">
                  <li>Lakukan pembayaran sesuai dengan total pesanan ({{ formatPrice(order.total_amount) }}).</li>
                  <li>Sertakan nomor pesanan ({{ order.order_number }}) pada keterangan transfer.</li>
                  <li>Setelah melakukan pembayaran, simpan bukti pembayaran Anda.</li>
                  <li>Konfirmasi pembayaran dengan mengirimkan bukti transfer ke WhatsApp admin.</li>
                  <li>Tim kami akan segera memproses pesanan Anda setelah pembayaran dikonfirmasi.</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import Breadcrumb from '@/components/ui/breadcrumb.vue';
import MainLayout from '@/components/layout/MainLayout.vue';
import { computed, ref, onMounted } from 'vue';
import { CheckCircleIcon, ShoppingBagIcon, SearchIcon, ClipboardCopy as ClipboardCopyIcon } from 'lucide-vue-next';
import AOS from 'aos';

const props = defineProps({
  order: Object,
  paymentMethods: Array
});

// State
const copySuccess = ref(false);

// Breadcrumb items
const breadcrumbItems = computed(() => [
  { label: 'Beranda', href: route('home') },
  { label: 'Pesanan Berhasil' }
]);

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

const getStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Konfirmasi',
    'processing': 'Sedang Diproses',
    'review': 'Review',
    'completed': 'Selesai',
    'cancelled': 'Dibatalkan'
  };
  
  return status ? (statusMap[status] || status) : '';
};

const getPaymentStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Pembayaran',
    'completed': 'Pembayaran Diterima',
    'failed': 'Pembayaran Gagal'
  };
  
  return status ? (statusMap[status] || status) : '';
};

// Fungsi untuk menyalin teks ke clipboard
const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text)
    .then(() => {
      copySuccess.value = true;
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

// Initialize AOS
onMounted(() => {
  AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
  });
});
</script>