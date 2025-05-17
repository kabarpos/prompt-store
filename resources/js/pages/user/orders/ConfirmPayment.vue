<template>
  <Head title="Konfirmasi Pembayaran" />
  
  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Konfirmasi Pembayaran</h1>
      </div>

      <div v-if="isLoading" class="flex items-center justify-center py-12">
        <Loader2Icon class="h-6 w-6 animate-spin text-primary-500 mr-2" />
        <p class="text-slate-500 dark:text-slate-400">Memuat data pesanan...</p>
      </div>
      
      <div v-else-if="!orderData" class="flex items-center justify-center py-12">
        <AlertCircleIcon class="h-6 w-6 text-destructive mr-2" />
        <p class="text-slate-500 dark:text-slate-400">Data pesanan tidak ditemukan atau tidak tersedia.</p>
      </div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Detail Pesanan -->
        <Card class="border border-slate-200 dark:border-slate-700">
          <CardHeader>
            <CardTitle>Detail Pesanan</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Nomor Pesanan</h4>
                <p class="text-base font-semibold">{{ orderData.order_number }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Status</h4>
                <Badge 
                  variant="outline" 
                  :class="{
                    'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': orderData.status === 'pending',
                    'border-blue-400 text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700': orderData.status === 'processing',
                    'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': orderData.status === 'completed',
                    'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': orderData.status === 'cancelled'
                  }"
                >
                  {{ orderData.status_label || getOrderStatusLabel(orderData.status) }}
                </Badge>
              </div>
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Tanggal</h4>
                <p class="text-base">{{ formatDate(orderData.created_at) }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total</h4>
                <p class="text-base font-semibold">{{ formatPrice(orderData.total_amount) }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Metode Pembayaran</h4>
                <Badge variant="outline" class="border-blue-400 text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700">
                  {{ orderData.payment_method === 'payment_gateway' ? 'Payment Gateway' : 'Transfer Bank' }}
                </Badge>
              </div>
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Status Pembayaran</h4>
                <Badge 
                  variant="outline" 
                  :class="{
                    'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': orderData.payment?.status === 'pending',
                    'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': orderData.payment?.status === 'completed',
                    'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': orderData.payment?.status === 'failed'
                  }"
                >
                  {{ orderData.payment?.status ? getPaymentStatusLabel(orderData.payment.status) : 'Menunggu Pembayaran' }}
                </Badge>
              </div>
            </div>
          </CardContent>
        </Card>
        
        <!-- Informasi Rekening -->
        <Card class="border border-slate-200 dark:border-slate-700">
          <CardHeader>
            <CardTitle>Metode Pembayaran</CardTitle>
            <CardDescription>
              Silakan transfer ke salah satu rekening di bawah ini
            </CardDescription>
          </CardHeader>
          <CardContent>
         

            <!-- Jika tidak ada metode pembayaran -->
            <Alert v-if="displayPaymentMethods.length === 0" variant="destructive">
              <AlertCircleIcon class="h-4 w-4" />
              <AlertTitle>Tidak ada metode pembayaran</AlertTitle>
              <AlertDescription>
                Tidak ada metode pembayaran tersedia saat ini. Silahkan coba lagi nanti atau hubungi admin.
              </AlertDescription>
            </Alert>

            <!-- Daftar Bank yang tersedia  -->
            <div class="space-y-4">
              <div v-for="method in displayPaymentMethods" :key="method.id" class="border rounded-md p-4 bg-slate-50 dark:bg-slate-800/50">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <img v-if="method.logo" :src="`/storage/${method.logo}`" :alt="method.name" class="w-6 h-6 object-cover rounded" />
                    <span class="font-semibold text-blue-700 dark:text-blue-400">
                      {{ method.name }}
                      <span v-if="method.bank_name" class="text-slate-500">({{ method.bank_name }})</span>
                    </span>
                    <Badge variant="outline" class="ml-2">{{ method.type === 'bank_transfer' ? 'Transfer Bank' : method.type }}</Badge>
                  </div>
                </div>
                
                <div class="space-y-4">
                  <!-- Rekening Bank Info -->
                  <div v-if="method.type === 'bank_transfer' && method.account_number" class="grid grid-cols-2 gap-3">
                    <div>
                      <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Nomor Rekening</h4>
                      <div class="flex items-center">
                        <p class="text-base font-semibold">{{ method.account_number }}</p>
                        <Button 
                          @click="copyToClipboard(method.account_number)" 
                          variant="ghost"
                          size="sm"
                          class="ml-2"
                        >
                          <ClipboardCopyIcon class="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                    <div>
                      <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Atas Nama</h4>
                      <p class="text-base font-semibold">{{ method.account_name }}</p>
                    </div>
                    <div class="col-span-2">
                      <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Transfer</h4>
                      <p class="text-base font-semibold">{{ formattedAmount }}</p>
                    </div>
                  </div>
                  
                  <!-- Payment Gateway Info -->
                  <div v-else-if="method.type === 'payment_gateway'" class="text-sm text-slate-600 dark:text-slate-400">
                    <p>Pembayaran melalui {{ method.name }}.</p>
                  </div>
                </div>
              </div>
              
              <!-- Tambahan Instruksi -->
              <Alert>
                <InfoIcon class="h-4 w-4" />
                <AlertTitle>Petunjuk Pembayaran</AlertTitle>
                <AlertDescription>
                  <ul class="list-disc pl-4 space-y-1">
                    <li>Silakan transfer tepat sampai 3 digit terakhir untuk memudahkan verifikasi</li>
                    <li>Konfirmasi pembayaran setelah Anda melakukan transfer</li>
                    <li>Simpan bukti pembayaran Anda</li>
                  </ul>
                </AlertDescription>
              </Alert>
            </div>
          </CardContent>
        </Card>
        
        <!-- Konfirmasi Pembayaran Sebelumnya -->
        <Card v-if="isValidLastConfirmation" class="border border-slate-200 dark:border-slate-700 md:col-span-2">
          <CardHeader>
            <CardTitle>Konfirmasi Pembayaran Terakhir</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Bank</h4>
                <p class="text-base font-semibold">{{ confirmationData?.bank_name || '-' }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Atas Nama</h4>
                <p class="text-base">{{ confirmationData?.account_name || '-' }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Jumlah</h4>
                <p class="text-base font-semibold">{{ confirmationData?.amount ? formatPrice(confirmationData.amount) : '-' }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Status</h4>
                <Badge 
                  variant="outline" 
                  :class="{
                    'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': confirmationData?.status === 'pending',
                    'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': confirmationData?.status === 'verified',
                    'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': confirmationData?.status === 'rejected'
                  }"
                >
                  {{ confirmationData?.status ? getStatusLabel(confirmationData.status) : '-' }}
                </Badge>
              </div>
            </div>
            
            <div v-if="confirmationData?.proof_image" class="mt-6">
              <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">Bukti Transfer:</h4>
              <div class="rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 cursor-pointer" @click="openLightbox">
                <img 
                  :src="`/storage/${confirmationData.proof_image}`" 
                  alt="Bukti Transfer" 
                  class="max-h-60 w-auto object-contain mx-auto"
                />
              </div>
            </div>
          </CardContent>
        </Card>
        
        <!-- Form Konfirmasi Pembayaran -->
        <Card v-if="!isValidLastConfirmation || confirmationData?.status === 'rejected'" class="border border-slate-200 dark:border-slate-700 md:col-span-2">
          <CardHeader>
            <CardTitle>Konfirmasi Pembayaran</CardTitle>
            <CardDescription>Silakan isi form di bawah ini setelah Anda melakukan transfer</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submitForm" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="bank_name">Bank Pengirim</Label>
                  <Input
                    id="bank_name"
                    v-model="form.bank_name"
                    type="text"
                    placeholder="Contoh: BCA, Mandiri, BNI, dll"
                    required
                  />
                </div>
                
                <div class="space-y-2">
                  <Label for="account_name">Nama Pemilik Rekening</Label>
                  <Input
                    id="account_name"
                    v-model="form.account_name"
                    type="text"
                    placeholder="Nama pemilik rekening pengirim"
                    required
                  />
                </div>
                
                <div class="space-y-2">
                  <Label for="account_number">Nomor Rekening</Label>
                  <Input
                    id="account_number"
                    v-model="form.account_number"
                    type="text"
                    placeholder="Nomor rekening pengirim"
                    required
                  />
                </div>
                
                <div class="space-y-2">
                  <Label for="amount">Jumlah Transfer</Label>
                  <Input
                    id="amount"
                    v-model="form.amount"
                    type="number"
                    step="0.01"
                    min="1"
                    :placeholder="`Min ${formatPrice(1)}`"
                    required
                  />
                  <p class="text-sm text-slate-500">
                    Total pesanan: {{ formatPrice(orderData.total_amount) }}
                  </p>
                </div>
                
                <div class="space-y-2">
                  <Label for="transfer_date">Tanggal Transfer</Label>
                  <Input
                    id="transfer_date"
                    v-model="form.transfer_date"
                    type="date"
                    :max="new Date().toISOString().substr(0, 10)"
                    required
                  />
                </div>
                
                <div class="space-y-2">
                  <Label for="notes">Catatan (opsional)</Label>
                  <Textarea
                    id="notes"
                    v-model="form.notes"
                    placeholder="Tulis catatan tambahan jika diperlukan"
                  />
                </div>
              </div>
              
              <div class="space-y-2 mt-4">
                <Label for="proof_image">Bukti Transfer</Label>
                <div class="mt-1 flex justify-center p-6 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-md">
                  <div class="space-y-1 text-center">
                    <div v-if="!imagePreview">
                      <UploadIcon class="mx-auto h-12 w-12 text-slate-400" />
                      <p class="text-sm text-slate-500 mt-2">
                        <span class="font-medium text-primary-600 hover:text-primary-500 focus:outline-none focus:underline transition duration-150 ease-in-out cursor-pointer" @click="$refs.fileInput.click()">Upload file</span>
                        atau drag and drop
                      </p>
                      <p class="text-xs text-slate-500 mt-1">PNG, JPG, GIF (MAX. 2MB)</p>
                    </div>
                    <img v-else :src="imagePreview" alt="Preview" class="mx-auto h-40 object-contain" />
                    <input ref="fileInput" type="file" class="hidden" accept="image/*" @change="handleFileChange" required />
                  </div>
                </div>
              </div>
              
              <div class="flex justify-end space-x-2 mt-6">
                <Button
                  type="button"
                  variant="outline"
                  @click="goBack"
                >
                  Batal
                </Button>
                <Button
                  type="submit"
                  :disabled="processing"
                >
                  <Loader2Icon v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
                  {{ processing ? 'Mengirim...' : 'Kirim Konfirmasi' }}
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
      
      <!-- Image Lightbox Dialog -->
      <Dialog :open="isLightboxOpen" @update:open="isLightboxOpen = $event">
        <DialogContent class="max-w-4xl p-1 bg-white dark:bg-slate-950 rounded-lg max-h-[90vh] overflow-y-auto">
          <div class="sticky top-0 z-10 bg-white dark:bg-slate-950 p-2">
            <DialogHeader>
              <div class="flex justify-between items-center">
                <DialogTitle>Bukti Transfer</DialogTitle>
                <Button 
                  variant="ghost" 
                  size="icon" 
                  class="rounded-full hover:bg-slate-200 dark:hover:bg-slate-800"
                  @click="isLightboxOpen = false"
                >
                  <XIcon class="h-5 w-5" />
                </Button>
              </div>
              <DialogDescription>
                Bukti transfer untuk pesanan #{{ orderData?.order_number }}
              </DialogDescription>
            </DialogHeader>
          </div>
          <div class="relative bg-slate-100 dark:bg-slate-900 rounded overflow-hidden p-4">
            <img 
              v-if="confirmationData?.proof_image" 
              :src="`/storage/${confirmationData.proof_image}`" 
              alt="Bukti Transfer" 
              class="w-full h-auto object-contain mx-auto"
            />
          </div>
          <div class="p-4 text-center">
            <span class="text-sm text-slate-500 dark:text-slate-400">Klik di luar gambar atau tombol X untuk menutup</span>
          </div>
        </DialogContent>
      </Dialog>
      
      <Toaster />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Toaster } from "@/components/ui/sonner";
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { 
  Card, 
  CardHeader, 
  CardTitle, 
  CardDescription, 
  CardContent 
} from '@/components/ui/card';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/components/ui/dialog';
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import { 
  Loader2Icon, 
  ClipboardCopyIcon, 
  InfoIcon,
  UploadIcon,
  XIcon,
  AlertCircleIcon,
  CheckCircleIcon
} from 'lucide-vue-next';

const props = defineProps({
  order: Object,
  lastConfirmation: Object,
  paymentMethods: Array,
});

// State Management
const isLoading = ref(true);
const orderData = ref(null);
const confirmationData = ref(null);
const isLightboxOpen = ref(false);
const fileInput = ref(null);
const imagePreview = ref(null);
const processing = ref(false);
const { toast } = useToast();

// Initialize data
onMounted(async () => {
  try {
    // Initialize data with defensive programming approach
    if (props.order) {
      orderData.value = { ...props.order };
    }
    
    if (props.lastConfirmation) {
      confirmationData.value = { ...props.lastConfirmation };
    }
  } catch (error) {
    console.error('Error initializing data:', error);
    toast({
      title: "Kesalahan",
      description: "Terjadi kesalahan saat memuat data. Silakan muat ulang halaman.",
      variant: "destructive"
    });
  } finally {
    isLoading.value = false;
  }
});

// Computed Properties
const breadcrumbItems = computed(() => [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Pesanan', href: route('orders.index') },
  { 
    title: orderData.value?.order_number ? `Pesanan #${orderData.value.order_number}` : 'Detail Pesanan',
    href: orderData.value?.id ? route('orders.show', orderData.value.id) : '#'
  },
  {
    title: 'Konfirmasi Pembayaran',
    href: orderData.value?.id ? route('orders.payment.confirm', orderData.value.id) : '#',
  },
]);

const displayPaymentMethods = computed(() => {
  // Jika ada data payment methods dari server, gunakan itu
  if (props.paymentMethods && props.paymentMethods.length > 0) {
    return props.paymentMethods;
  }
  
  // Jika tidak ada data dari server, buat fallback data
  const fallbackMethods = [
    {
      id: 'bca-fallback',
      name: 'Bank BCA',
      bank_name: 'BCA',
      type: 'bank_transfer',
      account_number: '1234567890',
      account_name: 'PT Dilogif Indonesia',
      logo: null
    },
    {
      id: 'bni-fallback',
      name: 'Bank BNI',
      bank_name: 'BNI',
      type: 'bank_transfer',
      account_number: '0987654321',
      account_name: 'PT Dilogif Indonesia',
      logo: null
    },
    {
      id: 'mandiri-fallback',
      name: 'Bank Mandiri',
      bank_name: 'Mandiri',
      type: 'bank_transfer',
      account_number: '1122334455',
      account_name: 'PT Dilogif Indonesia',
      logo: null
    }
  ];
  
  // Log pesan untuk debugging
  console.warn('Menggunakan data fallback untuk metode pembayaran karena data dari server kosong');
  
  return fallbackMethods;
});

const formattedAmount = computed(() => {
  return formatPrice(orderData.value?.total_amount || 0);
});

const isValidLastConfirmation = computed(() => {
  return confirmationData.value && Object.keys(confirmationData.value).length > 0;
});

// Form 
const form = useForm({
  bank_name: '',
  account_name: '',
  account_number: '',
  amount: orderData.value?.total_amount || 0,
  transfer_date: new Date().toISOString().substr(0, 10),
  proof_image: null,
  notes: '',
});

// Update amount when orderData changes
watch(() => orderData.value?.total_amount, (newValue) => {
  if (newValue !== undefined && form.amount === 0) {
    form.amount = newValue;
  }
}, { immediate: true });

// Methods
const formatPrice = (price) => {
  if (!price && price !== 0) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price);
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};

const getStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Verifikasi',
    'verified': 'Terverifikasi',
    'rejected': 'Ditolak'
  };
  
  return status ? (statusMap[status] || status) : 'Tidak Diketahui';
};

const getPaymentStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Pembayaran',
    'completed': 'Pembayaran Diterima',
    'failed': 'Pembayaran Gagal'
  };
  
  return status ? (statusMap[status] || status) : 'Tidak Diketahui';
};

const getOrderStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Konfirmasi',
    'processing': 'Sedang Diproses',
    'review': 'Review',
    'completed': 'Selesai',
    'cancelled': 'Dibatalkan'
  };
  
  return status ? (statusMap[status] || status) : 'Tidak Diketahui';
};

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text).then(() => {
    toast({
      title: "Berhasil!",
      description: "Nomor rekening berhasil disalin",
      variant: "success"
    });
  }).catch(err => {
    console.error('Gagal menyalin teks: ', err);
    toast({
      title: "Gagal!",
      description: "Tidak dapat menyalin nomor rekening",
      variant: "destructive"
    });
  });
};

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  // Validate file size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    toast({
      title: "Ukuran file terlalu besar",
      description: "Maksimal ukuran file adalah 2MB",
      variant: "destructive"
    });
    if (fileInput.value) fileInput.value.value = '';
    return;
  }
  
  // Create image preview
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target.result;
  };
  reader.readAsDataURL(file);
  
  form.proof_image = file;
};

const submitForm = () => {
  if (!orderData.value?.id) {
    toast({
      title: "Kesalahan",
      description: "ID pesanan tidak ditemukan",
      variant: "destructive"
    });
    return;
  }
  
  processing.value = true;
  form.post(route('orders.payment.confirm.store', orderData.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast({
        title: "Berhasil!",
        description: "Konfirmasi pembayaran berhasil dikirim",
        variant: "success"
      });
      processing.value = false;
    },
    onError: (errors) => {
      toast({
        title: "Gagal!",
        description: "Terjadi kesalahan saat mengirim konfirmasi pembayaran",
        variant: "destructive"
      });
      console.error('Form errors:', errors);
      processing.value = false;
    },
  });
};

const goBack = () => {
  if (orderData.value?.id) {
    router.visit(route('orders.show', orderData.value.id));
  } else {
    router.visit(route('orders.index'));
  }
};

const openLightbox = () => {
  isLightboxOpen.value = true;
};
</script> 