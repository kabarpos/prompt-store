<template>
  <Head :title="document ? document.title : 'Detail Dokumen'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <div v-if="document" class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Header dokumen -->
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <component :is="getDocumentIcon(document.type)" class="h-6 w-6" :class="getIconColorClass(document.type)" />
              <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">{{ document.title }}</h1>
            </div>
            <div class="flex items-center gap-2">
              <Badge :variant="getBadgeVariant(document.type)">
                {{ getDocumentTypeLabel(document.type) }}
              </Badge>
              <Badge v-if="!document.is_read" variant="destructive">Belum Dibaca</Badge>
              <Badge v-else variant="outline" class="text-green-600 dark:text-green-400">Telah Dibaca</Badge>
            </div>
          </div>
          
          <div class="text-sm text-slate-600 dark:text-slate-400 mb-3">
            <div class="mb-1">
              Pesanan: <Link :href="route('orders.show', order.id)" class="font-medium text-primary hover:underline">{{ order.order_number }}</Link>
              <span v-if="document.expires_at" class="ml-3" :class="{'text-red-500': isExpired(document.expires_at)}">
                {{ getExpiryText(document.expires_at) }}
              </span>
            </div>
            <div>
              <span class="font-medium">Dibuat:</span> {{ formatDate(document.created_at) }}
              <span v-if="document.is_read" class="ml-3">
                <span class="font-medium">Dibaca:</span> {{ formatDate(document.read_at) }}
              </span>
            </div>
          </div>
          
          <div class="flex flex-wrap gap-2 mt-4">
            <Button 
              v-if="!document.is_read"
              variant="outline" 
              size="sm"
              @click="markAsRead(order.id, document.id)"
            >
              <CheckCircle class="h-4 w-4 mr-1" />
              Tandai Dibaca
            </Button>
            <Button 
              variant="outline" 
              size="sm"
              @click="router.visit(route('my-documents'))"
            >
              <ArrowLeft class="h-4 w-4 mr-1" />
              Kembali
            </Button>
          </div>
        </div>
        
        <!-- Konten dokumen -->
        <div class="p-4 sm:p-6">
          <!-- Konten/Pesan Dokumen -->
          <div v-if="document.content" class="mb-6">
            <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-3">Pesan</h2>
            <div class="p-4 bg-slate-50 dark:bg-slate-800/70 rounded-md border border-slate-200 dark:border-slate-700 whitespace-pre-wrap text-sm">
              {{ document.content }}
            </div>
          </div>
          
          <!-- Catatan Tambahan -->
          <div v-if="document.notes" class="mb-6">
            <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-3">Catatan Tambahan</h2>
            <div class="p-4 bg-slate-50 dark:bg-slate-800/70 rounded-md border border-slate-200 dark:border-slate-700 whitespace-pre-wrap text-sm">
              {{ document.notes }}
            </div>
          </div>
          
          <!-- File Dokumen -->
          <div v-if="document.file_path" class="mb-6">
            <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-3">File Dokumen</h2>
            <div class="p-4 bg-slate-50 dark:bg-slate-800/70 rounded-md border border-slate-200 dark:border-slate-700">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <FileIcon class="h-6 w-6 text-blue-500" />
                  <div>
                    <p class="font-medium text-slate-900 dark:text-white">{{ getFileName(document.file_path) }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatFileSize(document.file_size) }}</p>
                  </div>
                </div>
                <Button 
                  variant="outline" 
                  size="sm"
                  @click="downloadDocument(order.id, document.id)"
                >
                  <Download class="h-4 w-4 mr-1" />
                  Unduh
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Loading state -->
      <div v-else class="flex flex-col items-center justify-center py-12">
        <div class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-slate-100 dark:bg-slate-800">
          <Loader2Icon class="h-8 w-8 text-primary-500 animate-spin" />
        </div>
        <p class="text-lg font-medium text-slate-900 dark:text-white">Memuat dokumen...</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  Key, Globe, RefreshCw, Download, File as FileIcon, CheckCircle, 
  ArrowLeft, Loader2Icon
} from 'lucide-vue-next';

// Props yang diterima dari controller
const props = defineProps({
  order: Object,
  document: Object,
});

// Toast notifikasi
const { toast } = useToast();

// Breadcrumbs untuk navigasi
const breadcrumbs = computed(() => {
  return [
    {
      title: 'Dashboard',
      href: route('dashboard'),
    },
    {
      title: 'Dokumen Saya',
      href: route('my-documents'),
    },
    {
      title: props.document ? props.document.title : 'Detail Dokumen',
      href: props.document ? route('orders.documents.show', [props.order.id, props.document.id]) : '#',
    },
  ];
});

// Format tanggal
const formatDate = (dateString) => {
  if (!dateString) return '-';
  
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric',
    hour: 'numeric',
    minute: 'numeric'
  }).format(date);
};

// Cek apakah tanggal sudah expired
const isExpired = (dateString) => {
  if (!dateString) return false;
  
  const expiryDate = new Date(dateString);
  return expiryDate < new Date();
};

// Teks untuk tanggal kedaluwarsa
const getExpiryText = (dateString) => {
  if (!dateString) return '';
  
  const expiryDate = new Date(dateString);
  const now = new Date();
  
  if (isExpired(dateString)) {
    return `Kedaluwarsa pada ${formatDate(dateString)}`;
  }
  
  const diffTime = Math.abs(expiryDate - now);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays <= 30) {
    return `Kedaluwarsa dalam ${diffDays} hari (${formatDate(dateString)})`;
  }
  
  return `Masa aktif hingga ${formatDate(dateString)}`;
};

// Label tipe dokumen
const getDocumentTypeLabel = (type) => {
  switch (type) {
    case 'credential': return 'Kredensial';
    case 'domain': return 'Domain';
    case 'update': return 'Pembaruan';
    case 'download': return 'Unduhan';
    default: return 'Dokumen';
  }
};

// Icon berdasarkan tipe
const getDocumentIcon = (type) => {
  switch (type) {
    case 'credential': return Key;
    case 'domain': return Globe;
    case 'update': return RefreshCw;
    case 'download': return Download;
    default: return FileIcon;
  }
};

// Kelas warna icon berdasarkan tipe
const getIconColorClass = (type) => {
  switch (type) {
    case 'credential': return 'text-blue-500';
    case 'domain': return 'text-green-500';
    case 'update': return 'text-orange-500';
    case 'download': return 'text-purple-500';
    default: return 'text-slate-500';
  }
};

// Variant badge berdasarkan tipe
const getBadgeVariant = (type) => {
  switch (type) {
    case 'credential': return 'blue';
    case 'domain': return 'green';
    case 'update': return 'orange';
    case 'download': return 'purple';
    default: return 'secondary';
  }
};

// Format ukuran file
const formatFileSize = (sizeInBytes) => {
  if (!sizeInBytes) return 'Tidak diketahui';
  
  if (sizeInBytes < 1024) {
    return `${sizeInBytes} bytes`;
  } else if (sizeInBytes < 1024 * 1024) {
    return `${(sizeInBytes / 1024).toFixed(2)} KB`;
  } else if (sizeInBytes < 1024 * 1024 * 1024) {
    return `${(sizeInBytes / (1024 * 1024)).toFixed(2)} MB`;
  } else {
    return `${(sizeInBytes / (1024 * 1024 * 1024)).toFixed(2)} GB`;
  }
};

// Mendapatkan nama file dari path
const getFileName = (filePath) => {
  if (!filePath) return 'Tidak ada file';
  
  // Split path by / dan mengambil bagian terakhir
  const parts = filePath.split('/');
  return parts[parts.length - 1];
};

// Download dokumen
const downloadDocument = (orderId, documentId) => {
  window.location.href = route('orders.documents.download', [orderId, documentId]);
};

// Tandai dokumen sebagai dibaca
const markAsRead = (orderId, documentId) => {
  router.post(route('orders.documents.mark-as-read', [orderId, documentId]), {}, {
    preserveScroll: true,
    onSuccess: () => {
      toast({
        title: 'Dokumen dibaca',
        description: 'Dokumen berhasil ditandai sebagai telah dibaca',
      });
      
      // Refresh halaman untuk memperbarui data
      router.reload();
    },
    onError: () => {
      toast({
        title: 'Gagal',
        description: 'Terjadi kesalahan saat menandai dokumen',
        variant: 'destructive',
      });
    }
  });
};
</script> 