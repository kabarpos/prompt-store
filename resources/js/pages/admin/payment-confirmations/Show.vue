<template>
  <Head title="Detail Konfirmasi Pembayaran" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container mx-auto py-6 px-4 md:px-6">
      <!-- Header dengan judul dan tombol aksi -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold tracking-tight">Detail Konfirmasi Pembayaran</h1>
        <div class="flex flex-col sm:flex-row items-center gap-2">
          <Link :href="route('admin.payment-confirmations.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <Button variant="outline" size="sm" class="h-9">
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Kembali ke Daftar Konfirmasi
            </Button>
          </Link>

          <div v-if="confirmation?.status === 'pending'" class="flex items-center gap-2">
            <Button 
              variant="destructive" 
              size="sm" 
              class="h-9"
              @click="showRejectDialog = true"
            >
              <XIcon class="h-4 w-4 mr-2" />
              Tolak
            </Button>
            
            <Button 
              variant="primary" 
              size="sm" 
              class="h-9"
              @click="showVerifyDialog = true"
            >
              <CheckIcon class="h-4 w-4 mr-2" />
              Verifikasi
            </Button>
          </div>
        </div>
      </div>

      <!-- Tampilkan pesan jika tidak ada data konfirmasi -->
      <div v-if="!confirmation" class="flex flex-col items-center justify-center py-12">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center max-w-md mx-auto">
          <h2 class="text-xl font-semibold text-yellow-700 dark:text-yellow-400 mb-2">Data tidak tersedia</h2>
          <p class="text-yellow-600 dark:text-yellow-300 mb-4">
            Data konfirmasi pembayaran tidak ditemukan atau masih dimuat.
          </p>
        </div>
      </div>

      <!-- Konten utama jika data konfirmasi tersedia -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Info Pesanan dan Pembeli -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Order Info -->
          <Card>
            <CardHeader>
              <CardTitle>Informasi Pesanan</CardTitle>
              <CardDescription>Detail pesanan yang terkait dengan konfirmasi pembayaran ini</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nomor Pesanan</h4>
                  <p class="text-base font-semibold">{{ confirmation?.payment?.order?.order_number || '-' }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status Pesanan</h4>
                  <Badge 
                    variant="outline" 
                    :class="{
                      'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': confirmation?.payment?.order?.status === 'pending',
                      'border-blue-400 text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700': confirmation?.payment?.order?.status === 'processing',
                      'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': confirmation?.payment?.order?.status === 'completed',
                      'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': confirmation?.payment?.order?.status === 'cancelled'
                    }"
                  >
                    {{ getOrderStatusLabel(confirmation?.payment?.order?.status) }}
                  </Badge>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total</h4>
                  <p class="text-base font-semibold">{{ formatPrice(confirmation?.payment?.order?.total_amount || 0) }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal Pesanan</h4>
                  <p class="text-base">{{ formatDate(confirmation?.payment?.order?.created_at) }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Metode Pembayaran</h4>
                  <p class="text-base">{{ confirmation?.payment?.payment_method?.name || '-' }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status Pembayaran</h4>
                  <Badge 
                    variant="outline" 
                    :class="{
                      'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': confirmation?.payment?.status === 'pending',
                      'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': confirmation?.payment?.status === 'paid',
                      'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': confirmation?.payment?.status === 'failed'
                    }"
                  >
                    {{ getPaymentStatusLabel(confirmation?.payment?.status) }}
                  </Badge>
                </div>
              </div>

              <!-- Produk Pesanan -->
              <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Produk yang Dibeli</h3>
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Produk</TableHead>
                      <TableHead>Harga</TableHead>
                      <TableHead>Jumlah</TableHead>
                      <TableHead class="text-right">Subtotal</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="item in confirmation?.payment?.order?.order_items" :key="item.id">
                      <TableCell>
                        <div class="font-medium">{{ item?.product?.name }}</div>
                        <div v-if="item?.product_variant" class="text-sm text-gray-500">
                          Variasi: {{ item?.product_variant?.name }}
                        </div>
                      </TableCell>
                      <TableCell>{{ formatPrice(item?.price || 0) }}</TableCell>
                      <TableCell>{{ item?.quantity }}</TableCell>
                      <TableCell class="text-right">{{ formatPrice((item?.price || 0) * (item?.quantity || 0)) }}</TableCell>
                    </TableRow>
                    <TableRow v-if="!confirmation?.payment?.order?.order_items?.length">
                      <TableCell colspan="4" class="text-center py-6 text-gray-500">
                        Tidak ada data produk
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
              </div>
            </CardContent>
          </Card>

          <!-- Customer Info -->
          <Card>
            <CardHeader>
              <CardTitle>Informasi Pelanggan</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nama</h4>
                  <p class="text-base font-semibold">{{ confirmation?.user?.name || '-' }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email</h4>
                  <p class="text-base">{{ confirmation?.user?.email || '-' }}</p>
                </div>
                <div class="md:col-span-2">
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Whatsapp</h4>
                  <p class="text-base">{{ confirmation?.user?.whatsapp || '-' }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Kolom Kanan: Info Konfirmasi Pembayaran -->
        <div class="space-y-6">
          <!-- Confirmation Info -->
          <Card>
            <CardHeader>
              <CardTitle>Detail Konfirmasi</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</h4>
                  <Badge 
                    variant="outline" 
                    :class="{
                      'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': confirmation.status === 'pending',
                      'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': confirmation.status === 'verified',
                      'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': confirmation.status === 'rejected'
                    }"
                  >
                    {{ getConfirmationStatusLabel(confirmation.status) }}
                  </Badge>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Bank</h4>
                  <p class="text-base font-semibold">{{ confirmation?.bank_name || '-' }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nomor Rekening</h4>
                  <p class="text-base">{{ confirmation?.account_number || '-' }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nama Pemilik Rekening</h4>
                  <p class="text-base">{{ confirmation?.account_name || '-' }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Jumlah Transfer</h4>
                  <p class="text-base font-semibold">{{ formatPrice(confirmation?.amount || 0) }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal Transfer</h4>
                  <p class="text-base">{{ formatDate(confirmation?.transfer_date || '') }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Catatan</h4>
                  <p class="text-base">{{ confirmation?.notes || '-' }}</p>
                </div>
                <div v-if="confirmation?.admin_notes">
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Catatan Admin</h4>
                  <p class="text-base">{{ confirmation?.admin_notes }}</p>
                </div>
                <div v-if="confirmation?.rejection_reason">
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Alasan Penolakan</h4>
                  <p class="text-base text-red-600 dark:text-red-400">{{ confirmation?.rejection_reason }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Bukti Transfer -->
          <Card>
            <CardHeader>
              <CardTitle>Bukti Transfer</CardTitle>
            </CardHeader>
            <CardContent>
              <div v-if="confirmation?.proof_image" class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <img 
                  :src="`/storage/${confirmation?.proof_image}`" 
                  alt="Bukti Transfer" 
                  class="w-full h-auto object-cover cursor-pointer" 
                  @click="openImageLightbox(`/storage/${confirmation?.proof_image}`)"
                />
              </div>
              <div v-else class="py-10 text-center text-gray-500 border border-dashed rounded-lg">
                Tidak ada bukti transfer yang diunggah
              </div>
              <div v-if="confirmation?.proof_image" class="mt-4 text-center">
                <Button variant="outline" size="sm" @click="openImageLightbox(`/storage/${confirmation?.proof_image}`)">
                  <Maximize class="h-4 w-4 mr-2" />
                  Lihat Gambar Penuh
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>

    <!-- Dialog Verifikasi Pembayaran -->
    <Dialog :open="showVerifyDialog" @update:open="showVerifyDialog = $event">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Verifikasi Konfirmasi Pembayaran</DialogTitle>
          <DialogDescription>
            Pastikan informasi pembayaran sudah sesuai sebelum memverifikasi konfirmasi pembayaran
          </DialogDescription>
        </DialogHeader>
        
        <div class="grid gap-4 py-4">
          <Alert>
            <AlertCircleIcon class="h-4 w-4" />
            <AlertTitle>Penting</AlertTitle>
            <AlertDescription>
              Tindakan ini akan mengubah status pembayaran menjadi "Dibayar" dan status pesanan menjadi "Sedang Diproses"
            </AlertDescription>
          </Alert>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Pesanan</h4>
              <p class="font-medium">{{ confirmation?.payment?.order?.order_number || '-' }}</p>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pesanan</h4>
              <p class="font-medium">{{ formatPrice(confirmation?.payment?.order?.total_amount || 0) }}</p>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Transfer</h4>
              <p class="font-medium">{{ formatPrice(confirmation?.amount || 0) }}</p>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Transfer</h4>
              <p>{{ formatDate(confirmation?.transfer_date) }}</p>
            </div>
          </div>
          
          <div>
            <Label for="admin_notes">Catatan Admin (Opsional)</Label>
            <Textarea 
              id="admin_notes" 
              v-model="verifyForm.admin_notes" 
              placeholder="Masukkan catatan admin jika diperlukan"
              class="mt-1"
            />
          </div>
        </div>
        
        <DialogFooter>
          <Button 
            variant="outline" 
            @click="showVerifyDialog = false"
          >
            Batal
          </Button>
          <Button 
            variant="primary"
            :disabled="verifyProcessing"
            @click="verifyConfirmation"
          >
            <Loader2Icon v-if="verifyProcessing" class="mr-2 h-4 w-4 animate-spin" />
            Verifikasi Pembayaran
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Dialog Penolakan Pembayaran -->
    <Dialog :open="showRejectDialog" @update:open="showRejectDialog = $event">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Tolak Konfirmasi Pembayaran</DialogTitle>
          <DialogDescription>
            Harap berikan alasan penolakan agar pelanggan dapat mengirimkan konfirmasi pembayaran yang benar
          </DialogDescription>
        </DialogHeader>
        
        <div class="grid gap-4 py-4">
          <Alert variant="destructive">
            <AlertCircleIcon class="h-4 w-4" />
            <AlertTitle>Perhatian</AlertTitle>
            <AlertDescription>
              Tindakan ini akan menolak konfirmasi pembayaran dan pelanggan perlu mengirimkan konfirmasi pembayaran baru
            </AlertDescription>
          </Alert>
          
          <div>
            <Label for="rejection_reason" class="required">Alasan Penolakan</Label>
            <Textarea 
              id="rejection_reason" 
              v-model="rejectForm.rejection_reason" 
              placeholder="Masukkan alasan penolakan"
              class="mt-1"
              required
            />
            <div v-if="rejectErrors.rejection_reason" class="text-sm text-red-500 mt-1">
              {{ rejectErrors.rejection_reason }}
            </div>
          </div>
          
          <div>
            <Label for="admin_notes">Catatan Admin (Opsional)</Label>
            <Textarea 
              id="admin_notes" 
              v-model="rejectForm.admin_notes" 
              placeholder="Masukkan catatan admin jika diperlukan"
              class="mt-1"
            />
          </div>
        </div>
        
        <DialogFooter>
          <Button 
            variant="outline" 
            @click="showRejectDialog = false"
          >
            Batal
          </Button>
          <Button 
            variant="destructive"
            :disabled="rejectProcessing"
            @click="rejectConfirmation"
          >
            <Loader2Icon v-if="rejectProcessing" class="mr-2 h-4 w-4 animate-spin" />
            Tolak Pembayaran
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Tambahkan modal lightbox di dekat bagian bawah template -->
    <Dialog :open="showImageLightbox" @update:open="showImageLightbox = $event">
      <DialogContent class="max-w-4xl p-0 overflow-hidden">
        <div class="relative">
          <div class="absolute top-2 right-2 z-10">
            <Button variant="ghost" size="icon" class="h-8 w-8 rounded-full bg-black/30 hover:bg-black/50 text-white" @click="showImageLightbox = false">
              <XIcon class="h-5 w-5" />
            </Button>
          </div>
          <img 
            :src="lightboxImageSrc" 
            alt="Bukti Transfer" 
            class="w-full h-auto max-h-[80vh]" 
          />
        </div>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  ArrowLeftIcon, 
  CheckIcon, 
  XIcon, 
  ExternalLinkIcon,
  AlertCircleIcon,
  Loader2Icon,
  Maximize
} from 'lucide-vue-next';
import {
  Card,
  CardHeader, 
  CardTitle, 
  CardDescription, 
  CardContent 
} from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogFooter, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";

const props = defineProps({
  confirmation: Object,
});

// Log the payment, order, and user data to help debug structure
onMounted(() => {
  console.log('Confirmation data:', props.confirmation);
  if (props.confirmation) {
    console.log('User data:', props.confirmation.user);
    console.log('Payment data:', props.confirmation.payment);
    if (props.confirmation.payment) {
      console.log('Order data:', props.confirmation.payment.order);
    }
  }
});

// Data untuk dialog
const showVerifyDialog = ref(false);
const showRejectDialog = ref(false);
const verifyProcessing = ref(false);
const rejectProcessing = ref(false);
const verifyForm = reactive({
  admin_notes: ''
});
const rejectForm = reactive({
  rejection_reason: '',
  admin_notes: ''
});
const rejectErrors = reactive({});

// Tambahkan state untuk lightbox
const showImageLightbox = ref(false);
const lightboxImageSrc = ref('');

// Breadcrumbs
const breadcrumbs = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Konfirmasi Pembayaran',
    href: route('admin.payment-confirmations.index'),
  },
  {
    title: 'Detail',
    href: route('admin.payment-confirmations.show', props.confirmation?.id),
  }
];

// Format harga (Rupiah)
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

// Format tanggal
const formatDate = (date) => {
  return new Date(date).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Label status pesanan
const getOrderStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Konfirmasi',
    'processing': 'Sedang Diproses',
    'completed': 'Selesai',
    'cancelled': 'Dibatalkan'
  };
  
  return statusMap[status] || status;
};

// Label status pembayaran
const getPaymentStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Pembayaran',
    'paid': 'Dibayar',
    'failed': 'Gagal'
  };
  
  return statusMap[status] || status;
};

// Label status konfirmasi
const getConfirmationStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Verifikasi',
    'verified': 'Terverifikasi',
    'rejected': 'Ditolak'
  };
  
  return statusMap[status] || status;
};

// Verifikasi pembayaran
const verifyConfirmation = () => {
  // Tambahkan validasi sebelum melakukan verifikasi
  if (!props.confirmation) {
    toast.error("Gagal!", {
      description: "Data konfirmasi tidak tersedia."
    });
    return;
  }
  
  verifyProcessing.value = true;
  
  router.patch(route('admin.payment-confirmations.status.update', props.confirmation?.id), {
    status: 'verified',
    admin_notes: verifyForm.admin_notes
  }, {
    onSuccess: () => {
      showVerifyDialog.value = false;
      verifyProcessing.value = false;
      toast.success("Berhasil!", {
        description: "Konfirmasi pembayaran telah diverifikasi."
      });
      // Refresh halaman untuk menampilkan perubahan
      router.reload();
    },
    onError: (errors) => {
      verifyProcessing.value = false;
      toast.error("Gagal!", {
        description: "Terjadi kesalahan saat memverifikasi pembayaran."
      });
      console.error('Error verifying payment:', errors);
    }
  });
};

// Tolak pembayaran
const rejectConfirmation = () => {
  // Validasi input
  if (!rejectForm.rejection_reason?.trim()) {
    rejectErrors.rejection_reason = 'Alasan penolakan harus diisi';
    return;
  }
  
  rejectProcessing.value = true;
  rejectErrors.rejection_reason = '';
  
  router.patch(route('admin.payment-confirmations.status.update', props.confirmation?.id), {
    status: 'rejected',
    admin_notes: rejectForm.admin_notes || rejectForm.rejection_reason // Gunakan admin_notes atau rejection_reason sebagai catatan
  }, {
    onSuccess: () => {
      showRejectDialog.value = false;
      rejectProcessing.value = false;
      toast.success("Berhasil!", {
        description: "Konfirmasi pembayaran telah ditolak."
      });
      // Refresh halaman untuk menampilkan perubahan
      router.reload();
    },
    onError: (errors) => {
      rejectProcessing.value = false;
      
      if (errors.rejection_reason) {
        rejectErrors.rejection_reason = errors.rejection_reason;
      }
      
      toast.error("Gagal!", {
        description: "Terjadi kesalahan saat menolak pembayaran."
      });
      console.error('Error rejecting payment:', errors);
    }
  });
};

// Fungsi untuk membuka lightbox
const openImageLightbox = (imageSrc) => {
  lightboxImageSrc.value = imageSrc;
  showImageLightbox.value = true;
};
</script> 