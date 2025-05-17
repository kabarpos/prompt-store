<template>
  <Head title="Detail Dokumen" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol kembali -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Detail Dokumen</h1>
        <div class="flex gap-2">
          <Link :href="route('admin.documents.all')">
            <Button variant="outline" size="sm" class="h-9">
              <ArrowLeft class="h-4 w-4 mr-2" />
              Kembali
            </Button>
          </Link>
          <Link :href="route('admin.orders.documents.edit', [order.id, document.id])">
            <Button variant="outline" size="sm" class="h-9">
              <Edit class="h-4 w-4 mr-2" />
              Edit Dokumen
            </Button>
          </Link>
        </div>
      </div>

      <!-- Card Detail Dokumen -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-secondary-200 dark:border-slate-700">
        <div class="p-6 border-b border-secondary-200 dark:border-slate-700">
          <h2 class="text-lg font-semibold text-secondary-900 dark:text-white">Informasi Dokumen</h2>
        </div>
        <div class="p-6 grid gap-6 md:grid-cols-2">
          <div class="space-y-4">
            <div>
              <h3 class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Judul Dokumen</h3>
              <p class="mt-1 text-secondary-900 dark:text-white text-base">{{ document.title }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Tipe Dokumen</h3>
              <Badge :variant="getBadgeVariant(document.type)" class="mt-1">
                {{ getDocumentType(document.type) }}
              </Badge>
            </div>
            <div>
              <h3 class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Status Pengiriman</h3>
              <Badge :variant="document.is_sent ? 'success' : 'outline'" class="mt-1">
                {{ document.is_sent ? 'Terkirim' : 'Belum Terkirim' }}
              </Badge>
            </div>
            <div v-if="document.is_sent && document.sent_at">
              <h3 class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Dikirim Pada</h3>
              <p class="mt-1 text-secondary-900 dark:text-white text-base">{{ formatDate(document.sent_at) }}</p>
            </div>
          </div>
          <div class="space-y-4">
            <div>
              <h3 class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Pesanan Terkait</h3>
              <p class="mt-1 text-secondary-900 dark:text-white text-base">
                <Link :href="route('admin.orders.show', order.id)" class="text-primary-600 hover:underline">
                  {{ order.order_number }}
                </Link>
              </p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Customer</h3>
              <p class="mt-1 text-secondary-900 dark:text-white text-base">{{ getCustomerName(order) }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Dibuat Pada</h3>
              <p class="mt-1 text-secondary-900 dark:text-white text-base">{{ formatDate(document.created_at) }}</p>
            </div>
            <div v-if="document.file_path">
              <h3 class="text-sm font-medium text-secondary-500 dark:text-secondary-400">File Attachment</h3>
              <div class="mt-1 flex items-center gap-2">
                <Button size="sm" variant="outline" @click="handleDownload" class="h-8 px-3 text-sm">
                  <Download class="h-4 w-4 mr-2" />
                  Unduh File ({{ formatFileSize(document.file_size) }})
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Konten Dokumen -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-secondary-200 dark:border-slate-700">
        <div class="p-6 border-b border-secondary-200 dark:border-slate-700">
          <h2 class="text-lg font-semibold text-secondary-900 dark:text-white">Konten Dokumen</h2>
        </div>
        <div class="p-6">
          <div class="prose dark:prose-invert max-w-none">
            <div class="p-4 rounded-md bg-secondary-50 dark:bg-slate-900 border border-secondary-200 dark:border-slate-700">
              {{ document.content }}
            </div>
          </div>
        </div>
      </div>

      <!-- Tombol Aksi -->
      <div class="flex justify-end gap-2 mt-4">
        <Button 
          v-if="!document.is_sent" 
          variant="action" 
          @click="confirmSend"
          class="h-10"
        >
          <Mail class="h-4 w-4 mr-2" />
          Kirim ke Pelanggan
        </Button>
        <Button 
          variant="destructive" 
          @click="confirmDelete"
          class="h-10"
        >
          <Trash2 class="h-4 w-4 mr-2" />
          Hapus Dokumen
        </Button>
      </div>
    </div>

    <!-- Dialog Konfirmasi Kirim -->
    <Dialog :open="showSendDialog" @update:open="showSendDialog = false">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Kirim Dokumen ke Pelanggan</DialogTitle>
          <DialogDescription>
            Dokumen ini akan dikirim melalui email ke {{ order.user?.name }}. Konfirmasi untuk melanjutkan.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="showSendDialog = false">Batal</Button>
          <Button @click="sendDocument" :disabled="isSubmitting">
            <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
            <Mail class="mr-2 h-4 w-4" />
            Kirim Dokumen
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Dialog Konfirmasi Hapus -->
    <Dialog :open="showDeleteDialog" @update:open="showDeleteDialog = false">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Konfirmasi Hapus</DialogTitle>
          <DialogDescription>
            Apakah Anda yakin ingin menghapus dokumen "{{ document.title }}"? Tindakan ini tidak dapat dibatalkan.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="showDeleteDialog = false">Batal</Button>
          <Button variant="destructive" @click="deleteDocument" :disabled="isSubmitting">
            <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
            Hapus
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Download, Edit, Mail, Trash2, ArrowLeft, Loader2 } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const props = defineProps({
  order: Object,
  document: Object,
  documentTypes: Object
});

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: route('admin.dashboard') },
  { title: 'Orders', href: route('admin.orders.index') },
  { title: 'Order ' + props.order.order_number, href: route('admin.orders.show', props.order.id) },
  { title: 'Dokumen', href: route('admin.orders.documents.index', props.order.id) },
  { title: props.document.title },
];

// State
const showSendDialog = ref(false);
const showDeleteDialog = ref(false);
const isSubmitting = ref(false);
const { toast } = useToast();

// Dokumentasi tipe dan badge color
const badgeVariants = {
  credential: 'default',
  domain: 'secondary',
  update: 'outline',
  download: 'destructive',
  other: 'secondary'
};

// Get badge variant
const getBadgeVariant = (type) => {
  return badgeVariants[type] || 'default';
};

// Get document type display name
const getDocumentType = (type) => {
  return props.documentTypes[type] || 'Lainnya';
};

// Format file size
const formatFileSize = (bytes) => {
  if (!bytes) return '-';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Format tanggal
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

// Konfirmasi pengiriman
const confirmSend = () => {
  showSendDialog.value = true;
};

// Kirim dokumen
const sendDocument = () => {
  isSubmitting.value = true;
  
  router.post(route('admin.orders.documents.send', [props.order.id, props.document.id]), {}, {
    onSuccess: () => {
      showSendDialog.value = false;
      toast({
        title: 'Dokumen terkirim',
        description: 'Dokumen berhasil dikirim ke pelanggan',
      });
    },
    onError: (errors) => {
      toast({
        title: 'Gagal mengirim dokumen',
        description: errors.message || 'Terjadi kesalahan saat mengirim dokumen',
        variant: 'destructive',
      });
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

// Konfirmasi hapus
const confirmDelete = () => {
  showDeleteDialog.value = true;
};

// Hapus dokumen
const deleteDocument = () => {
  isSubmitting.value = true;
  
  router.delete(route('admin.orders.documents.destroy', [props.order.id, props.document.id]), {
    onSuccess: () => {
      router.visit(route('admin.orders.documents.index', props.order.id));
      toast({
        title: 'Dokumen dihapus',
        description: 'Dokumen berhasil dihapus',
      });
    },
    onError: (errors) => {
      toast({
        title: 'Gagal menghapus dokumen',
        description: errors.message || 'Terjadi kesalahan saat menghapus dokumen',
        variant: 'destructive',
      });
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

// Download dokumen dengan penanganan error
const handleDownload = () => {
  try {
    // Tampilkan notifikasi proses
    toast({
      title: 'Mengunduh dokumen',
      description: 'Memulai proses unduhan...',
    });
    
    // Gunakan URL lengkap dengan prefix admin
    const downloadUrl = `/admin/documents/${props.document.id}/download`;
    
    // Buka URL download di tab baru
    window.open(downloadUrl, '_blank');
    
    console.log('Attempting download from URL:', downloadUrl);
  } catch (error) {
    console.error('Download error:', error);
    toast({
      title: 'Gagal mengunduh',
      description: 'Terjadi kesalahan saat mengunduh dokumen',
      variant: 'destructive',
    });
  }
};

// Tambahkan fungsi getCustomerName
const getCustomerName = (order) => {
  // Cek nama customer dengan prioritas
  if (order.customer_name && order.customer_name.trim() !== '') {
    return order.customer_name;
  } else if (order.user && order.user.name && order.user.name.trim() !== '') {
    return order.user.name;
  } else {
    return 'Tanpa Nama';
  }
};
</script> 