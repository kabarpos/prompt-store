<template>
  <Head :title="`Dokumen Pesanan #${order.order_number}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol aksi -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">
            Dokumen Pesanan #{{ order.order_number }}
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Kelola kredensial, informasi domain, dan file untuk pesanan
          </p>
        </div>
        <div>
          <Link :href="route('admin.orders.documents.create', order.id)">
            <Button variant="action" size="sm" class="h-9">
              <PlusIcon class="h-4 w-4 mr-2" />
              Tambah Dokumen
            </Button>
          </Link>
        </div>
      </div>

      <!-- Notifikasi flash message -->
      <div v-if="flashMessage" 
           class="rounded-lg border px-4 py-3" 
           :class="{'bg-green-50 border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300': messageType === 'success', 
                   'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300': messageType === 'error'}">
        <div class="flex items-center">
          <CheckCircle v-if="messageType === 'success'" class="h-5 w-5 mr-2" />
          <XCircle v-else-if="messageType === 'error'" class="h-5 w-5 mr-2" />
          <p>{{ flashMessage }}</p>
        </div>
      </div>

      <!-- Informasi Pesanan -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-slate-200 dark:border-slate-700">
        <h2 class="text-lg font-medium mb-4 text-secondary-900 dark:text-white">Informasi Pesanan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Pelanggan</p>
            <p class="text-base font-medium">{{ getCustomerName(order) }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ order.customer_email }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ order.customer_phone }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tanggal Pesanan</p>
            <p class="text-base font-medium">{{ formatDate(order.created_at) }}</p>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-2">Status</p>
            <Badge :variant="getStatusVariant(order.status)">{{ order.status_label }}</Badge>
          </div>
        </div>
      </div>

      <!-- Daftar Dokumen -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Dokumen & Kredensial</h2>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola semua dokumen terkait pesanan ini</p>
        </div>

        <div v-if="order.documents && order.documents.length > 0" class="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow class="hover:bg-transparent border-b border-secondary-200 dark:border-slate-700">
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Judul</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Jenis</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Tanggal Dibuat</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Kedaluwarsa</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Status Pengiriman</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400 text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="document in order.documents" :key="document.id" class="border-b border-slate-200 dark:border-slate-700">
                <TableCell class="py-4 px-6 font-medium">
                  <div class="flex items-center gap-2">
                    <component :is="getDocumentIcon(document.type)" class="h-4 w-4" />
                    {{ document.title }}
                  </div>
                </TableCell>
                <TableCell class="py-4 px-6">
                  <Badge :variant="getDocumentVariant(document.type)">{{ getDocumentTypeLabel(document.type) }}</Badge>
                </TableCell>
                <TableCell class="py-4 px-6">{{ formatDate(document.created_at) }}</TableCell>
                <TableCell class="py-4 px-6">
                  <span v-if="document.expires_at" :class="{'text-red-600 dark:text-red-400': isExpired(document.expires_at)}">
                    {{ formatDate(document.expires_at) }}
                  </span>
                  <span v-else>-</span>
                </TableCell>
                <TableCell class="py-4 px-6">
                  <Badge v-if="document.is_sent" variant="success">Terkirim</Badge>
                  <Badge v-else variant="outline">Belum Dikirim</Badge>
                </TableCell>
                <TableCell class="py-4 px-6 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <DropdownMenu>
                      <DropdownMenuTrigger asChild>
                        <Button variant="action" size="icon" class="h-8 w-8 rounded-md">
                          <MoreHorizontal class="h-4 w-4" />
                          <span class="sr-only">Menu</span>
                        </Button>
                      </DropdownMenuTrigger>
                      <DropdownMenuContent align="end" class="w-[160px]">
                        <DropdownMenuItem 
                          v-if="document.file_path"
                          @click="downloadDocument(document.id)" 
                          class="flex items-center gap-2 cursor-pointer py-1.5"
                        >
                          <Download class="h-4 w-4" />
                          <span>Unduh</span>
                        </DropdownMenuItem>
                        <DropdownMenuItem 
                          v-if="!document.is_sent"
                          @click="sendDocument(document)"
                          class="flex items-center gap-2 cursor-pointer py-1.5"
                        >
                          <Mail class="h-4 w-4" />
                          <span>Kirim</span>
                        </DropdownMenuItem>
                        <DropdownMenuItem 
                          v-if="document.is_sent"
                          disabled
                          class="flex items-center gap-2 py-1.5 opacity-50 cursor-not-allowed"
                        >
                          <CheckCircle class="h-4 w-4" />
                          <span>Sudah Terkirim</span>
                        </DropdownMenuItem>
                        <DropdownMenuItem 
                          @click="editDocument(document)"
                          class="flex items-center gap-2 cursor-pointer py-1.5"
                        >
                          <Edit class="h-4 w-4" />
                          <span>Edit</span>
                        </DropdownMenuItem>
                        <DropdownMenuItem 
                          @click="confirmDelete(document)"
                          class="flex items-center gap-2 cursor-pointer py-1.5 text-red-600 dark:text-red-400"
                        >
                          <Trash2 class="h-4 w-4" />
                          <span>Hapus</span>
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        <div v-else class="text-center py-12">
          <div class="mx-auto h-12 w-12 text-slate-300 mb-4">
            <FileText class="h-12 w-12" />
          </div>
          <h3 class="text-base font-semibold text-slate-900 dark:text-slate-50 mb-1">Tidak ada dokumen</h3>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 max-w-md mx-auto">
            Tambahkan dokumen, kredensial, atau file untuk pesanan ini
          </p>
          <Link :href="route('admin.orders.documents.create', order.id)">
            <Button variant="action" size="sm">
              <PlusIcon class="w-4 h-4 mr-2" />
              Tambah Dokumen
            </Button>
          </Link>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <Dialog :open="showDeleteModal" @update:open="showDeleteModal = false">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Konfirmasi Hapus</DialogTitle>
          <DialogDescription>
            Apakah Anda yakin ingin menghapus dokumen ini? Tindakan ini tidak dapat dibatalkan.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="showDeleteModal = false">Batal</Button>
          <Button variant="destructive" @click="processDelete" :disabled="isSubmitting">
            <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
            Hapus
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { 
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow 
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { useToast } from '@/composables/useToast';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle
} from '@/components/ui/dialog';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { 
  FileText, 
  Edit, 
  Trash2, 
  Download, 
  Mail, 
  Plus as PlusIcon,
  CheckCircle, 
  XCircle,
  MoreHorizontal,
  Loader2,
  Key,
  Globe, 
  RefreshCw
} from 'lucide-vue-next';

// Props yang diterima dari controller
const props = defineProps({
  order: Object,
  documentTypes: Object,
  errors: Object,
  flash: Object,
});

// Toast untuk notifikasi
const { toast } = useToast();

// State untuk modal dan loading
const showDeleteModal = ref(false);
const isSubmitting = ref(false);
const selectedDocument = ref(null);

// Breadcrumbs untuk navigasi
const breadcrumbs = [
  {
    title: 'Dashboard',
    href: route('admin.dashboard'),
  },
  {
    title: 'Pesanan',
    href: route('admin.orders.index'),
  },
  {
    title: `#${props.order.order_number}`,
    href: route('admin.orders.show', props.order.id),
  },
  {
    title: 'Dokumen',
    href: route('admin.orders.documents.index', props.order.id),
  },
];

// Flash message dari controller
const flashMessage = computed(() => props.flash?.message || props.flash?.error);
const messageType = computed(() => props.flash?.error ? 'error' : 'success');

// Format tanggal menjadi format lokal
const formatDate = (dateString) => {
  if (!dateString) return '-';
  
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  }).format(date);
};

// Cek apakah tanggal sudah expired
const isExpired = (dateString) => {
  if (!dateString) return false;
  
  const expiryDate = new Date(dateString);
  return expiryDate < new Date();
};

// Dapatkan variant badge berdasarkan status pesanan
const getStatusVariant = (status) => {
  switch (status) {
    case 'pending': return 'warning';
    case 'processing': return 'info';
    case 'review': return 'purple';
    case 'completed': return 'success';
    case 'cancelled': return 'destructive';
    default: return 'secondary';
  }
};

// Dapatkan variant badge berdasarkan tipe dokumen
const getDocumentVariant = (type) => {
  switch (type) {
    case 'credential': return 'credential';
    case 'domain': return 'domain';
    case 'update': return 'orange';
    case 'download': return 'download';
    default: return 'secondary';
  }
};

// Get document icon
const getDocumentIcon = (type) => {
  switch (type) {
    case 'credential': return Key;
    case 'domain': return Globe;
    case 'update': return RefreshCw;
    case 'download': return Download;
    default: return FileText;
  }
};

// Dapatkan label tipe dokumen
const getDocumentTypeLabel = (type) => {
  return props.documentTypes[type] || type;
};

// Download dokumen
const downloadDocument = (id) => {
  window.open(route('admin.orders.documents.download', [props.order.id, id]));
};

// Mengedit dokumen
const editDocument = (document) => {
  router.visit(route('admin.orders.documents.edit', [props.order.id, document.id]));
};

// Konfirmasi penghapusan dokumen
const confirmDelete = (document) => {
  selectedDocument.value = document;
  showDeleteModal.value = true;
};

// Proses penghapusan dokumen dengan modal
const processDelete = () => {
  if (!selectedDocument.value) return;
  
  isSubmitting.value = true;
  
  router.delete(route('admin.orders.documents.destroy', [props.order.id, selectedDocument.value.id]), {
    onSuccess: () => {
      showDeleteModal.value = false;
      selectedDocument.value = null;
      isSubmitting.value = false;
      
      toast({
        title: 'Dokumen dihapus',
        description: 'Dokumen berhasil dihapus',
      });
    },
    onError: () => {
      isSubmitting.value = false;
      toast({
        title: 'Gagal menghapus',
        description: 'Terjadi kesalahan saat menghapus dokumen',
        variant: 'destructive',
      });
    }
  });
};

// Kirim dokumen melalui email
const sendDocument = (document) => {
  isSubmitting.value = true;
  
  router.post(route('admin.orders.documents.send', [props.order.id, document.id]), {}, {
    onSuccess: () => {
      toast({
        title: 'Dokumen dikirim',
        description: 'Dokumen berhasil dikirim ke pelanggan',
      });
      
      // Gunakan visit untuk memuat ulang halaman setelah operasi sukses
      router.visit(window.location.href, { preserveScroll: true });
    },
    onError: () => {
      toast({
        title: 'Gagal mengirim',
        description: 'Terjadi kesalahan saat mengirim dokumen',
        variant: 'destructive',
      });
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
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