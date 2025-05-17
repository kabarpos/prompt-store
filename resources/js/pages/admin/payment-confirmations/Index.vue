<template>
  <Head title="Konfirmasi Pembayaran" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Konfirmasi Pembayaran
        </h2>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
        <!-- List Konfirmasi Pembayaran -->
        <Card>
          <CardHeader>
            <CardTitle>Daftar Konfirmasi Pembayaran</CardTitle>
            <CardDescription>
              Daftar konfirmasi pembayaran dari pelanggan yang memerlukan verifikasi.
            </CardDescription>
          </CardHeader>
          
          <CardContent>
            <!-- Table -->
            <AdminTable 
              :columns="confirmationColumns" 
              :data="confirmations" 
              :loading="processing"
              emptyMessage="Belum ada data konfirmasi pembayaran"
            >
              <TableRow 
                v-for="confirmation in confirmations.data" 
                :key="confirmation.id"
                class="hover:bg-secondary-100/50 dark:hover:bg-slate-900/90"
              >
                <TableCell class="text-center py-3.5 px-6 align-middle">
                  {{ confirmation.id }}
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <Link 
                    :href="route('admin.orders.show', confirmation.payment.order.id)"
                    class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 font-medium"
                  >
                    {{ confirmation.payment.order.order_number }}
                  </Link>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <div class="flex flex-col">
                    <span class="font-medium">{{ confirmation.user.name }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ confirmation.user.email }}</span>
                  </div>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <div class="flex flex-col">
                    <span class="font-medium">{{ confirmation.bank_name }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ confirmation.account_name }}</span>
                  </div>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  {{ formatPrice(confirmation.amount) }}
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  {{ formatDate(confirmation.transfer_date) }}
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <Badge 
                    variant="outline" 
                    :class="{
                      'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400': confirmation.status === 'pending',
                      'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400': confirmation.status === 'verified',
                      'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400': confirmation.status === 'rejected',
                    }"
                  >
                    {{ getStatusLabel(confirmation.status) }}
                  </Badge>
                </TableCell>
                <TableCell class="text-right py-3.5 px-6 align-middle">
                  <div class="flex justify-end">
                    <DropdownMenu>
                      <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="icon">
                          <MoreHorizontal class="h-4 w-4" />
                        </Button>
                      </DropdownMenuTrigger>
                      <DropdownMenuContent align="end">
                        <DropdownMenuItem asChild>
                          <Link :href="route('admin.payment-confirmations.show', confirmation.id)">
                            <Eye class="h-4 w-4 mr-2" />
                            Lihat Detail
                          </Link>
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem 
                          v-if="confirmation.status === 'pending'"
                          @click="verifyConfirmation(confirmation)"
                          class="text-green-600 dark:text-green-400"
                        >
                          <CheckCircle class="h-4 w-4 mr-2" />
                          Verifikasi
                        </DropdownMenuItem>
                        <DropdownMenuItem 
                          v-if="confirmation.status === 'pending'"
                          @click="rejectConfirmation(confirmation)"
                          class="text-red-600 dark:text-red-400"
                        >
                          <XCircle class="h-4 w-4 mr-2" />
                          Tolak
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </div>
                </TableCell>
              </TableRow>
            </AdminTable>
          </CardContent>
        </Card>
        
        <!-- Pesanan yang Memerlukan Pembayaran -->
        <Card>
          <CardHeader>
            <CardTitle>Pesanan yang Memerlukan Pembayaran</CardTitle>
            <CardDescription>
              Daftar pesanan yang belum melakukan konfirmasi pembayaran.
            </CardDescription>
          </CardHeader>
          
          <CardContent>
            <AdminTable
              :columns="orderColumns"
              :data="{ data: pendingOrders, links: [] }"
              :loading="processing"
              emptyMessage="Tidak ada pesanan yang memerlukan pembayaran"
            >
              <TableRow 
                v-for="order in pendingOrders" 
                :key="order.id"
                class="hover:bg-secondary-100/50 dark:hover:bg-slate-900/90"
              >
                <TableCell class="py-3.5 px-6 align-middle">
                  <Link 
                    :href="route('admin.orders.show', order.id)"
                    class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 font-medium"
                  >
                    {{ order.order_number }}
                  </Link>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <div class="flex flex-col">
                    <span class="font-medium">{{ order.user?.name }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ order.user?.email }}</span>
                  </div>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ formatPrice(order.total_amount) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ order.payment?.payment_method?.name || '-' }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <Badge 
                    variant="outline" 
                    :class="{
                      'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400': order.payment?.status === 'pending',
                      'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400': order.payment?.status === 'completed',
                      'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400': order.payment?.status === 'failed',
                    }"
                  >
                    {{ getPaymentStatusLabel(order.payment?.status) }}
                  </Badge>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ formatDate(order.created_at) }}</TableCell>
              </TableRow>
            </AdminTable>
          </CardContent>
        </Card>
      </div>
    </div>
    
    <!-- Dialog Verifikasi -->
    <Dialog 
      :open="showVerifyDialog" 
      @update:open="showVerifyDialog = $event"
    >
      <DialogContent class="sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>Verifikasi Konfirmasi Pembayaran</DialogTitle>
          <DialogDescription>
            Pastikan Anda telah memeriksa bukti pembayaran dan detail konfirmasi dengan benar.
          </DialogDescription>
        </DialogHeader>
        <div class="space-y-4 py-4">
          <!-- Loading State -->
          <div v-if="!selectedConfirmation || processing" class="flex flex-col items-center justify-center py-6">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"></div>
            <p class="text-sm text-muted-foreground">Memuat data...</p>
          </div>
          
          <!-- Content -->
          <div v-else>
            <div class="p-4 border rounded-lg mb-4">
              <h4 class="font-semibold mb-2">Informasi Konfirmasi</h4>
              <div class="grid grid-cols-1 gap-2">
                <div class="flex flex-col">
                  <span class="text-sm text-muted-foreground">Status</span>
                  <Badge :color="confirmationStatusColor(selectedConfirmation?.status)">
                    {{ confirmationStatusLabel(selectedConfirmation?.status) }}
                  </Badge>
                </div>
                <div class="flex flex-col">
                  <span class="text-sm text-muted-foreground">Bank</span>
                  <span>{{ selectedConfirmation?.bank_name || '-' }}</span>
                </div>
                <div class="flex flex-col">
                  <span class="text-sm text-muted-foreground">Pemilik Rekening</span>
                  <span>{{ selectedConfirmation?.account_name || '-' }}</span>
                </div>
                <div class="flex flex-col">
                  <span class="text-sm text-muted-foreground">No. Rekening</span>
                  <span>{{ selectedConfirmation?.account_number || '-' }}</span>
                </div>
                <div class="flex flex-col">
                  <span class="text-sm text-muted-foreground">Tanggal Transfer</span>
                  <span>{{ formatDate(selectedConfirmation?.transfer_date) || '-' }}</span>
                </div>
              </div>
            </div>
            
            <div>
              <Label for="admin_notes">Catatan Admin (opsional)</Label>
              <Textarea 
                id="admin_notes" 
                v-model="adminNotes" 
                placeholder="Masukkan catatan untuk customer (jika diperlukan)" 
                class="mt-1"
              />
            </div>
          </div>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="showVerifyDialog = false">Batal</Button>
          <Button @click="updateStatus('verified')" :loading="processing" :disabled="processing || !selectedConfirmation">
            <CheckCircle class="h-4 w-4 mr-2" />
            Verifikasi
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
    
    <!-- Dialog Penolakan -->
    <Dialog :open="showRejectDialog" @update:open="showRejectDialog = $event">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Tolak Konfirmasi Pembayaran</DialogTitle>
          <DialogDescription>
            Berikan alasan penolakan untuk konfirmasi pembayaran ini.
          </DialogDescription>
        </DialogHeader>
        <div class="space-y-4">
          <div v-if="selectedConfirmation">
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-4">
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <p class="text-gray-500 dark:text-gray-400">Nomor Pesanan:</p>
                  <p class="font-medium">{{ selectedConfirmation.payment?.order?.order_number }}</p>
                </div>
                <div>
                  <p class="text-gray-500 dark:text-gray-400">Total Pesanan:</p>
                  <p class="font-medium">{{ formatPrice(selectedConfirmation.payment?.order?.total_amount) }}</p>
                </div>
                <div>
                  <p class="text-gray-500 dark:text-gray-400">Bank Pengirim:</p>
                  <p class="font-medium">{{ selectedConfirmation.bank_name }}</p>
                </div>
                <div>
                  <p class="text-gray-500 dark:text-gray-400">Jumlah Transfer:</p>
                  <p class="font-medium">{{ formatPrice(selectedConfirmation.amount) }}</p>
                </div>
              </div>
            </div>
            
            <div>
              <Label for="reject_reason">Alasan Penolakan <span class="text-red-500">*</span></Label>
              <Textarea 
                id="reject_reason" 
                v-model="adminNotes" 
                placeholder="Masukkan alasan penolakan konfirmasi pembayaran" 
                class="mt-1"
                required
              />
              <p v-if="!adminNotes && showNoteError" class="text-sm text-red-500 mt-1">
                Alasan penolakan harus diisi
              </p>
            </div>
          </div>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="showRejectDialog = false">Batal</Button>
          <Button @click="updateStatus('rejected')" variant="destructive" :loading="processing" :disabled="processing">
            <XCircle class="h-4 w-4 mr-2" />
            Tolak
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
    
    <!-- Dialog Full Image -->
    <Dialog :open="showFullImageDialog" @update:open="showFullImageDialog = $event">
      <DialogContent class="max-w-4xl">
        <DialogHeader>
          <DialogTitle>Bukti Transfer</DialogTitle>
        </DialogHeader>
        <div class="flex justify-center">
          <img :src="fullImageSrc" alt="Bukti Transfer" class="max-w-full max-h-[80vh]" />
        </div>
        <DialogFooter>
          <Button @click="showFullImageDialog = false">Tutup</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TableRow, TableCell } from '@/components/ui/table';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import Pagination from '@/components/Pagination.vue';
import AdminTable from '@/components/AdminTable.vue';
import { toast } from 'vue-sonner';
import { 
  MoreHorizontal, Eye, CheckCircle, XCircle, RefreshCcw, Maximize, X, XIcon,
} from 'lucide-vue-next';

const props = defineProps({
  confirmations: Object,
  pendingOrders: Array,
});

// Breadcrumbs untuk navigasi
const breadcrumbs = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Konfirmasi Pembayaran',
    href: route('admin.payment-confirmations.index'),
  },
];

// State
const showVerifyDialog = ref(false);
const showRejectDialog = ref(false);
const showFullImageDialog = ref(false);
const selectedConfirmation = ref(null);
const adminNotes = ref('');
const processing = ref(false);
const showNoteError = ref(false);
const fullImageSrc = ref('');
const isLoadingDetails = ref(false);

// Debugging untuk trace perubahan showVerifyDialog
watch(showVerifyDialog, (newVal, oldVal) => {
  console.log(`showVerifyDialog berubah dari ${oldVal} menjadi ${newVal}`);
});

// Columns for AdminTable
const confirmationColumns = [
  { label: '#', headerClass: 'w-16 text-center' },
  { label: 'Nomor Pesanan' },
  { label: 'Pelanggan' },
  { label: 'Bank' },
  { label: 'Jumlah' },
  { label: 'Tanggal Transfer' },
  { label: 'Status' },
  { label: 'Aksi', headerClass: 'w-24 text-right' }
];

const orderColumns = [
  { label: 'Nomor Pesanan' },
  { label: 'Pelanggan' },
  { label: 'Total' },
  { label: 'Metode Pembayaran' },
  { label: 'Status' },
  { label: 'Tanggal' }
];

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price || 0);
};

const formatDate = (date) => {
  if (!date) return '';
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
    'rejected': 'Ditolak',
  };
  
  return statusMap[status] || status;
};

const getOrderStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu',
    'processing': 'Diproses',
    'review': 'Review', 
    'completed': 'Selesai',
    'cancelled': 'Dibatalkan',
  };
  
  return statusMap[status] || status;
};

const getPaymentStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Pembayaran',
    'completed': 'Pembayaran Diterima',
    'failed': 'Pembayaran Gagal',
  };
  
  return statusMap[status] || status;
};

// Buka dialog verifikasi
const openVerifyDialog = (confirmation) => {
  console.log('openVerifyDialog dipanggil dengan:', confirmation);
  
  // Reset state
  selectedConfirmation.value = {...confirmation};
  adminNotes.value = '';
  
  // Buka dialog
  console.log('Sebelum set showVerifyDialog:', showVerifyDialog.value);
  setTimeout(() => {
    showVerifyDialog.value = true;
    console.log('Setelah set showVerifyDialog (dalam timeout):', showVerifyDialog.value);
  }, 10);
};

// Buka dialog penolakan
const openRejectDialog = (confirmation) => {
  // Reset state
  selectedConfirmation.value = {...confirmation};
  adminNotes.value = '';
  showNoteError.value = false;
  
  // Buka dialog
  setTimeout(() => {
    showRejectDialog.value = true;
  }, 10);
};

// Fungsi yang dipanggil dari dropdown
const verifyConfirmation = (confirmation) => {
  console.log('verifyConfirmation dipanggil:', confirmation);
  
  // Reset state
  selectedConfirmation.value = {...confirmation};
  adminNotes.value = '';
  
  // Jika data tidak lengkap, fetch data detail konfirmasi
  if (!confirmation.payment?.order || !confirmation.user) {
    viewConfirmation(confirmation.id);
  }
  
  // Buka dialog setelah state di-reset
  setTimeout(() => {
    console.log('Membuka dialog verifikasi...');
    showVerifyDialog.value = true;
    console.log('Dialog verifikasi seharusnya terbuka. Status showVerifyDialog =', showVerifyDialog.value);
  }, 50);
};

const rejectConfirmation = (confirmation) => {
  selectedConfirmation.value = {...confirmation};
  adminNotes.value = '';
  showNoteError.value = false;
  showRejectDialog.value = true;
  
  // Jika data tidak lengkap, fetch data detail konfirmasi
  if (!confirmation.payment?.order || !confirmation.user) {
    viewConfirmation(confirmation.id);
  }
};

const updateStatus = (status) => {
  if (status === 'rejected' && !adminNotes.value) {
    showNoteError.value = true;
    return;
  }
  
  processing.value = true;
  
  router.post(route('admin.payment-confirmations.update-status', selectedConfirmation.value.id), {
    status: status,
    admin_notes: adminNotes.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Berhasil', {
        description: status === 'verified' 
          ? 'Konfirmasi pembayaran berhasil diverifikasi' 
          : 'Konfirmasi pembayaran berhasil ditolak',
      });
      showVerifyDialog.value = false;
      showRejectDialog.value = false;
    },
    onError: (errors) => {
      console.error('Error:', errors);
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat memperbarui status konfirmasi',
      });
    },
    onFinish: () => {
      processing.value = false;
    }
  });
};

const showFullImage = (imageSrc) => {
  console.log('Membuka gambar penuh:', imageSrc);
  fullImageSrc.value = imageSrc;
  showFullImageDialog.value = true;
};

const getBadgeVariant = (status) => {
  const statusMap = {
    'pending': 'default',
    'verified': 'default',
    'rejected': 'destructive',
  };
  
  return statusMap[status] || 'default';
};

const getConfirmationBadgeVariant = (status) => {
  const statusMap = {
    'pending': 'default',
    'verified': 'default',
    'rejected': 'destructive',
  };
  
  return statusMap[status] || 'default';
};

const getConfirmationStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Verifikasi',
    'verified': 'Terverifikasi',
    'rejected': 'Ditolak',
  };
  
  return statusMap[status] || status;
};

const getOrderStatusVariant = (status) => {
  const variants = {
    'pending': 'outline',
    'processing': 'secondary',
    'completed': 'success',
    'cancelled': 'destructive'
  };
  return variants[status] || 'outline';
};

const getConfirmationStatusVariant = (status) => {
  const variants = {
    'pending': 'outline',
    'verified': 'success',
    'rejected': 'destructive'
  };
  return variants[status] || 'outline';
};
</script> 