<template>
  <Head title="Checkout" />

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

        <h1 class="text-4xl font-bold mb-8 text-center text-foreground bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-400" data-aos="fade-up">Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Order Form -->
          <div class="lg:col-span-2" data-aos="fade-up" data-aos-delay="100">
            <div class="bg-background/60 backdrop-blur-sm rounded-xl shadow-sm p-6 border border-border">
              <h2 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">Informasi Pesanan</h2>
              
              <form @submit.prevent="submitOrder">
                <!-- Customer Information -->
                <div class="space-y-6">
                  <!-- Customer Name -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Nama Lengkap *</label>
                    <input
                      type="text"
                      v-model="form.customer_name"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 focus:border-primary-700 focus:ring-primary-500 bg-white dark:bg-slate-800 text-gray-900 dark:text-white p-2"
                      :disabled="isSubmitting"
                      required
                    />
                  </div>

                  <!-- Customer Email -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Email *</label>
                    <input
                      type="email"
                      v-model="form.customer_email"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 focus:border-primary-700 focus:ring-primary-500 bg-white dark:bg-slate-800 text-gray-900 dark:text-white p-2"
                      :disabled="isSubmitting"
                      required
                    />
                  </div>

                  <!-- Customer Phone -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Nomor WhatsApp *</label>
                    <input
                      type="tel"
                      v-model="form.customer_phone"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 focus:border-primary-700 focus:ring-primary-500 bg-white dark:bg-slate-800 text-gray-900 dark:text-white p-2"
                      :disabled="isSubmitting"
                      required
                      placeholder="Contoh: 081234567890"
                    />
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Nomor WhatsApp akan digunakan untuk konfirmasi pesanan</p>
                  </div>

                  <!-- Order Notes -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Catatan Pesanan (Opsional)</label>
                    <textarea
                      v-model="form.notes"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 focus:border-primary-700 focus:ring-primary-500 bg-white dark:bg-slate-800 text-gray-900 dark:text-white p-2"
                      :disabled="isSubmitting"
                      placeholder="Contoh: Warna yang diinginkan, ukuran, dll"
                    ></textarea>
                  </div>

                  <!-- Order Items -->
                  <div>
                    <h3 class="text-md font-medium text-gray-900 mb-4 dark:text-white">Produk yang Dipesan</h3>
                    <div class="space-y-4">
                      <div v-for="item in cartItems" :key="item.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-800 rounded-lg">
                        <div class="flex items-center">
                          <img 
                            :src="`/storage/${item.product.featured_image}`" 
                            :alt="item.product.name"
                            class="h-16 w-16 object-cover rounded-md"
                          />
                          <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ item.product.name }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-300">{{ formatPrice(item.product.price) }} x {{ item.quantity }}</p>
                          </div>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatPrice(item.product.price * item.quantity) }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Submit Button -->
                  <button 
                    type="submit"
                    class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="isSubmitting"
                  >
                    <span v-if="!isSubmitting">Proses Pesanan</span>
                    <span v-else class="flex items-center justify-center">
                      <div class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent mr-2"></div>
                      Memproses...
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="lg:col-span-1" data-aos="fade-up" data-aos-delay="200">
            <div class="bg-background/60 backdrop-blur-sm rounded-xl shadow-sm p-6 border border-border">
              <h2 class="text-lg font-medium text-gray-900 mb-4 dark:text-white">Ringkasan Pesanan</h2>
              
              <!-- Summary Items -->
              <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                  <p class="text-gray-600 dark:text-gray-300">Subtotal ({{ summary.itemCount }} item)</p>
                  <p class="font-medium dark:text-white">{{ formatPrice(summary.subtotal) }}</p>
                </div>
                <div class="flex justify-between">
                  <p class="text-gray-600 dark:text-gray-300">Biaya Admin</p>
                  <p class="font-medium dark:text-white">{{ formatPrice(summary.adminFee) }}</p>
                </div>
                <div v-if="summary.discount > 0" class="flex justify-between">
                  <div class="flex flex-col">
                    <p class="text-gray-600 dark:text-gray-300">Diskon</p>
                    <p v-if="coupon" class="text-xs text-gray-500 dark:text-gray-400">{{ coupon.name }}</p>
                  </div>
                  <p class="font-medium text-green-600 dark:text-green-400">-{{ formatPrice(summary.discount) }}</p>
                </div>
              </div>
              
              <!-- Total -->
              <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-lg font-medium text-gray-900 dark:text-white">Total</p>
                <p class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ formatPrice(summary.total) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import Breadcrumb from '@/components/ui/breadcrumb.vue';
import MainLayout from '@/components/layout/MainLayout.vue';
import { computed, ref, onMounted } from 'vue';
import { toast } from 'vue-sonner';
import AOS from 'aos';

const props = defineProps({
  cartItems: {
    type: Array,
    required: true,
    validator: (value) => value.length > 0
  },
  summary: {
    type: Object,
    required: true,
    validator: (value) => {
      return value && 
        typeof value.subtotal === 'number' && 
        typeof value.total === 'number' &&
        typeof value.itemCount === 'number';
    }
  },
  user: {
    type: Object,
    default: null
  },
  coupon: {
    type: Object,
    default: null
  }
});

// State
const isSubmitting = ref(false);
const form = ref({
  notes: '',
  customer_name: props.user?.name || '',
  customer_phone: props.user?.whatsapp || '',
  customer_email: props.user?.email || ''
});

// Validasi form
const validateForm = () => {
  // Debug: Log validasi
  console.log('Validating form:', {
    cartItems: props.cartItems,
    summary: props.summary,
    user: props.user,
    form: form.value
  });

  if (!props.cartItems || props.cartItems.length === 0) {
    console.log('Cart is empty');
    router.visit(route('cart.index'));
    return false;
  }

  if (!props.summary || !props.summary.total) {
    console.log('Invalid summary');
    toast.error('Terjadi kesalahan saat menghitung total belanja');
    return false;
  }

  // Validasi data customer
  if (!form.value.customer_name) {
    console.log('Name missing');
    toast.error('Silakan masukkan nama lengkap Anda');
    return false;
  }

  if (!form.value.customer_email) {
    console.log('Email missing');
    toast.error('Silakan masukkan email Anda');
    return false;
  }

  // Validasi format email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(form.value.customer_email)) {
    console.log('Invalid email format');
    toast.error('Format email tidak valid');
    return false;
  }

  // Validasi nomor WhatsApp
  if (!form.value.customer_phone) {
    console.log('Phone number missing');
    toast.error('Silakan masukkan nomor WhatsApp Anda');
    return false;
  }

  // Bersihkan format nomor telepon sebelum validasi
  const cleanedPhone = cleanPhoneNumber(form.value.customer_phone);
  
  // Validasi format nomor WhatsApp
  const phoneRegex = /^(0|62|\+62)8[1-9][0-9]{8,11}$/;
  if (!phoneRegex.test(cleanedPhone)) {
    console.log('Invalid phone number format:', cleanedPhone);
    toast.error('Format nomor WhatsApp tidak valid. Gunakan format: 08xx, 62xx, atau +62xx');
    return false;
  }

  return true;
};

// Breadcrumb items
const breadcrumbItems = computed(() => [
  { label: 'Beranda', href: route('home') },
  { label: 'Keranjang', href: route('cart.index') },
  { label: 'Checkout' }
]);

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

// Fungsi untuk membersihkan format nomor WhatsApp
const cleanPhoneNumber = (phone) => {
  if (!phone) return '';
  // Hapus semua karakter non-digit
  return phone.replace(/[^\d]/g, '');
};

const submitOrder = async () => {
  if (isSubmitting.value) return;
  
  console.group('=== DEBUG CHECKOUT [START] ===');
  console.log('1. Form Data:', form.value);
  console.log('2. Cart Items:', props.cartItems);
  console.log('3. Summary:', props.summary);
  console.log('4. User:', props.user);
  console.log('5. Route URL:', route('orders.store'));
  
  // Bersihkan format nomor telepon
  form.value.customer_phone = cleanPhoneNumber(form.value.customer_phone);
  
  // Debug alert
  toast.info('Memproses pesanan Anda...');
  console.log('Memproses pesanan. Form data:', form.value);
  
  // Cek jika keranjang kosong, tambahkan ulang ke form data
  if (!props.cartItems || props.cartItems.length === 0) {
    console.error('CRITICAL: Cart items are missing!');
    toast.error('Data keranjang tidak ditemukan. Silakan refresh halaman dan coba lagi.');
    console.groupEnd();
    return;
  }

  // Pastikan data cart dikirim
  form.value.cart_items = props.cartItems.map(item => ({
    product_id: item.product_id,
    quantity: item.quantity,
    price: item.product.price
  }));
  console.log('6. Cart items data attached to form');
  
  // Validasi form
  console.log('7. Validating form data');
  if (!validateForm()) {
    console.error('Form validation failed');
    console.groupEnd();
    return;
  }
  
  try {
    console.log('8. Form validation passed');
    isSubmitting.value = true;
    
    // Debug: Log request URL dan headers
    console.log('9. Sending request to:', route('orders.store'));
    console.log('10. Form data being sent:', form.value);
    
    // Tambahkan meta info untuk debugging
    form.value._meta = {
      timestamp: Date.now(),
      browser: navigator.userAgent,
      cart_count: props.cartItems.length,
      total: props.summary.total,
      request_id: Math.random().toString(36).substring(2, 15)
    };
    
    console.log('11. About to send POST request, showing processing...');
    
    // Gunakan Inertia form untuk menangani CSRF token secara otomatis
    const response = await router.post(route('orders.store'), form.value, {
      preserveScroll: true,
      preserveState: true,
      onBefore: () => {
        console.log('12. Before request - CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        toast.loading('Mengirim data pesanan ke server...');
      },
      onSuccess: (page) => {
        console.log('13. Success Response:', page);
        console.log('14. Flash Message:', page.props?.flash);
        
        toast.dismiss();
        toast.success('Pesanan berhasil diterima!');
        
        try {
          console.log('15. Processing redirect options');
          // Cek flash message untuk data order
          if (page.props?.flash?.order) {
            console.log('16a. Redirecting to thank you page with order:', page.props.flash.order);
            router.visit(route('orders.thankyou', { order: page.props.flash.order.id }));
            console.groupEnd();
            return;
          } 
          
          // Cek flash message untuk order_id (fallback)
          if (page.props?.flash?.order_id) {
            console.log('16b. Redirecting to thank you page with order_id:', page.props.flash.order_id);
            router.visit(route('orders.thankyou', { order: page.props.flash.order_id }));
            console.groupEnd();
            return;
          }
          
          if (page.props?.flash?.redirect_url) {
            console.log('16c. Redirecting using explicit redirect URL:', page.props.flash.redirect_url);
            window.location.href = page.props.flash.redirect_url;
            console.groupEnd();
            return;
          }
          
          if (page.props?.flash?.redirect) {
            console.log('16d. Redirecting using standard redirect URL:', page.props.flash.redirect);
            router.visit(page.props.flash.redirect);
            console.groupEnd();
            return;
          }
          
          // Coba ekstrak order ID dari URL - jika ada
          let orderId = null;
          
          // Cek apakah ada key 'order' di URL parameters
          const urlParams = new URLSearchParams(window.location.search);
          if (urlParams.has('order')) {
            orderId = urlParams.get('order');
            console.log('16e. Found order ID in URL parameters:', orderId);
          }
          
          // Jika tidak ditemukan, coba ekstrak dari page URL
          if (!orderId && page.url && page.url.includes('thank-you')) {
            const match = page.url.match(/\/orders\/thank-you\/(\d+)/);
            if (match && match[1]) {
              orderId = match[1];
              console.log('16f. Extracted order ID from URL:', orderId);
            }
          }
          
          // Jika berhasil ekstrak ID, redirect ke halaman thank you
          if (orderId) {
            console.log('16g. Redirecting to thank you page with extracted ID:', orderId);
            router.visit(route('orders.thankyou', { order: orderId }));
            console.groupEnd();
            return;
          }

          // Fallback untuk emergency
          console.log('16h. EMERGENCY FALLBACK - Redirecting to orders index');
          toast.success('Pesanan berhasil dibuat! Mengalihkan ke daftar pesanan...');
          
          console.groupEnd();
          // Kembali ke semua order sebagai pilihan terakhir
          window.location.href = '/orders';
          
        } catch (redirectError) {
          console.error('17. Redirect error:', redirectError);
          toast.error('Terjadi error pada redirect, mengalihkan ke daftar pesanan');
          
          console.groupEnd();
          window.location.href = '/orders';
        }
      },
      onError: (errors) => {
        console.error('18. Error Response:', errors);
        console.error('19. Error Details:', {
          message: errors.message,
          errors: errors.errors,
          status: errors.status
        });
        
        toast.dismiss();
        
        if (errors.notes) {
          toast.error(errors.notes);
        } else if (errors.cart) {
          toast.error('Terjadi kesalahan pada keranjang belanja');
        } else if (errors.message === 'Sesi Anda telah berakhir. Silakan coba lagi.') {
          toast.error('Sesi Anda berakhir. Menyegarkan halaman...');
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        } else {
          toast.error('Gagal membuat pesanan: ' + (errors.message || 'Unknown error'));
        }
        
        console.groupEnd();
      },
      onFinish: () => {
        console.log('20. Request finished');
        isSubmitting.value = false;
      }
    });
    
  } catch (error) {
    console.error('21. System Error:', error);
    console.error('22. Error Stack:', error.stack);
    
    toast.dismiss();
    toast.error('Terjadi kesalahan sistem. Silakan coba lagi dalam beberapa saat.');
    
    console.groupEnd();
    isSubmitting.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  // Debug: Log mounted
  console.log('Component mounted:', {
    cartItems: props.cartItems,
    summary: props.summary,
    user: props.user
  });

  // Set form data dari user jika sudah login
  form.value = {
    notes: '',
    customer_name: props.user?.name || '',
    customer_phone: props.user?.whatsapp || '',
    customer_email: props.user?.email || ''
  };

  // Initialize AOS
  AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
  });
});
</script> 