<template>
  <Head :title="`Dokumen Pesanan ${order.order_number}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul halaman -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Dokumen Pesanan</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Nomor Pesanan: {{ order.order_number }}
          </p>
        </div>
        
        <div>
          <Button 
            variant="outline" 
            size="sm"
            @click="router.visit(route('orders.show', order.id))"
          >
            <ArrowLeft class="h-4 w-4 mr-1" />
            Kembali ke Detail Pesanan
          </Button>
        </div>
      </div>

      <!-- Kredensial Login -->
      <div v-if="credentials && credentials.length > 0" class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
          <h2 class="text-lg font-medium text-slate-900 dark:text-white flex items-center">
            <Key class="h-5 w-5 mr-2 text-blue-500" />
            Kredensial Login
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Informasi login dan kredensial untuk akses ke layanan
          </p>
        </div>
        
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
          <div v-for="document in credentials" :key="document.id" class="p-4 sm:p-6" :class="{'bg-red-50/30 dark:bg-red-900/10': !document.is_read}">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <Key class="h-5 w-5 text-blue-500" />
                  <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ document.title }}</h3>
                  <Badge variant="blue">Kredensial</Badge>
                  <Badge v-if="!document.is_read" variant="destructive">Belum Dibaca</Badge>
                </div>
                
                <div class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                  <div class="mb-1">
                    <span v-if="document.expires_at" :class="{'text-danger-500': isExpired(document.expires_at)}">
                      {{ getExpiryText(document.expires_at) }}
                    </span>
                  </div>
                  <div>{{ formatDate(document.created_at) }}</div>
                </div>
                
                <div v-if="document.content" class="border rounded-md p-3 bg-slate-50 dark:bg-slate-800/70 mb-4 max-h-24 overflow-y-auto">
                  <p class="text-sm whitespace-pre-wrap">{{ truncateContent(document.content, 150) }}</p>
                </div>
              </div>
              
              <div class="flex flex-wrap sm:flex-col gap-2 sm:min-w-[160px] justify-end">
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
                  @click="navigateToDocumentDetail(order.id, document.id)"
                >
                  <Eye class="h-4 w-4 mr-1" />
                  Lihat Detail
                </Button>
                <Button 
                  v-if="document.file_path"
                  variant="outline" 
                  size="sm"
                  @click="downloadDocument(order.id, document.id)"
                >
                  <Download class="h-4 w-4 mr-1" />
                  Unduh File
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Informasi Domain -->
      <div v-if="domainInfo && domainInfo.length > 0" class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
          <h2 class="text-lg font-medium text-slate-900 dark:text-white flex items-center">
            <Globe class="h-5 w-5 mr-2 text-green-500" />
            Informasi Domain & Hosting
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Detail domain dan layanan hosting yang terkait dengan pesanan Anda
          </p>
        </div>
        
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
          <div v-for="document in domainInfo" :key="document.id" class="p-4 sm:p-6" :class="{'bg-red-50/30 dark:bg-red-900/10': !document.is_read}">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <Globe class="h-5 w-5 text-green-500" />
                  <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ document.title }}</h3>
                  <Badge variant="green">Domain</Badge>
                  <Badge v-if="!document.is_read" variant="destructive">Belum Dibaca</Badge>
                </div>
                
                <div class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                  <div class="mb-1">
                    <span v-if="document.expires_at" :class="{'text-danger-500': isExpired(document.expires_at)}">
                      {{ getExpiryText(document.expires_at) }}
                    </span>
                  </div>
                  <div>{{ formatDate(document.created_at) }}</div>
                </div>
                
                <div v-if="document.content" class="border rounded-md p-3 bg-slate-50 dark:bg-slate-800/70 mb-4 max-h-24 overflow-y-auto">
                  <p class="text-sm whitespace-pre-wrap">{{ truncateContent(document.content, 150) }}</p>
                </div>
              </div>
              
              <div class="flex flex-wrap sm:flex-col gap-2 sm:min-w-[160px] justify-end">
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
                  @click="navigateToDocumentDetail(order.id, document.id)"
                >
                  <Eye class="h-4 w-4 mr-1" />
                  Lihat Detail
                </Button>
                <Button 
                  v-if="document.file_path"
                  variant="outline" 
                  size="sm"
                  @click="downloadDocument(order.id, document.id)"
                >
                  <Download class="h-4 w-4 mr-1" />
                  Unduh File
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pembaruan -->
      <div v-if="updates && updates.length > 0" class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
          <h2 class="text-lg font-medium text-slate-900 dark:text-white flex items-center">
            <RefreshCw class="h-5 w-5 mr-2 text-orange-500" />
            Pembaruan & Informasi
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Pembaruan dan informasi penting terkait layanan Anda
          </p>
        </div>
        
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
          <div v-for="document in updates" :key="document.id" class="p-4 sm:p-6" :class="{'bg-red-50/30 dark:bg-red-900/10': !document.is_read}">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <RefreshCw class="h-5 w-5 text-orange-500" />
                  <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ document.title }}</h3>
                  <Badge variant="orange">Pembaruan</Badge>
                  <Badge v-if="!document.is_read" variant="destructive">Belum Dibaca</Badge>
                </div>
                
                <div class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                  <div>{{ formatDate(document.created_at) }}</div>
                </div>
                
                <div v-if="document.content" class="border rounded-md p-3 bg-slate-50 dark:bg-slate-800/70 mb-4 max-h-24 overflow-y-auto">
                  <p class="text-sm whitespace-pre-wrap">{{ truncateContent(document.content, 150) }}</p>
                </div>
              </div>
              
              <div class="flex flex-wrap sm:flex-col gap-2 sm:min-w-[160px] justify-end">
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
                  @click="navigateToDocumentDetail(order.id, document.id)"
                >
                  <Eye class="h-4 w-4 mr-1" />
                  Lihat Detail
                </Button>
                <Button 
                  v-if="document.file_path"
                  variant="outline" 
                  size="sm"
                  @click="downloadDocument(order.id, document.id)"
                >
                  <Download class="h-4 w-4 mr-1" />
                  Unduh File
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- File Unduhan -->
      <div v-if="downloads && downloads.length > 0" class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
          <h2 class="text-lg font-medium text-slate-900 dark:text-white flex items-center">
            <Download class="h-5 w-5 mr-2 text-purple-500" />
            File Unduhan
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            File dan dokumen yang dapat diunduh terkait dengan pesanan Anda
          </p>
        </div>
        
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
          <div v-for="document in downloads" :key="document.id" class="p-4 sm:p-6" :class="{'bg-red-50/30 dark:bg-red-900/10': !document.is_read}">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <Download class="h-5 w-5 text-purple-500" />
                  <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ document.title }}</h3>
                  <Badge variant="purple">Unduhan</Badge>
                  <Badge v-if="!document.is_read" variant="destructive">Belum Dibaca</Badge>
                </div>
                
                <div class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                  <div>{{ formatDate(document.created_at) }}</div>
                </div>
                
                <div v-if="document.content" class="border rounded-md p-3 bg-slate-50 dark:bg-slate-800/70 mb-4 max-h-24 overflow-y-auto">
                  <p class="text-sm whitespace-pre-wrap">{{ truncateContent(document.content, 150) }}</p>
                </div>
              </div>
              
              <div class="flex flex-wrap sm:flex-col gap-2 sm:min-w-[160px] justify-end">
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
                  @click="navigateToDocumentDetail(order.id, document.id)"
                >
                  <Eye class="h-4 w-4 mr-1" />
                  Lihat Detail
                </Button>
                <Button 
                  v-if="document.file_path"
                  variant="outline" 
                  size="sm"
                  @click="downloadDocument(order.id, document.id)"
                >
                  <Download class="h-4 w-4 mr-1" />
                  Unduh File
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!hasDocuments" class="text-center py-16 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="mx-auto h-20 w-20 text-slate-300">
          <File class="h-20 w-20" />
        </div>
        <h3 class="mt-4 text-xl font-semibold text-slate-900 dark:text-white">Tidak ada dokumen</h3>
        <p class="mt-2 text-slate-500 dark:text-slate-400 max-w-md mx-auto">
          Saat ini tidak ada dokumen atau informasi tambahan yang tersedia untuk pesanan ini.
        </p>
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
  Key, Globe, RefreshCw, Download, File, CheckCircle, Eye, ArrowLeft
} from 'lucide-vue-next';

// Props yang diterima dari controller
const props = defineProps({
  order: Object,
  credentials: Array,
  domainInfo: Array,
  updates: Array,
  downloads: Array,
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
      title: 'Pesanan Saya',
      href: route('orders.index'),
    },
    {
      title: `Pesanan ${props.order ? props.order.order_number : ''}`,
      href: props.order ? route('orders.show', props.order.id) : '#',
    },
    {
      title: 'Dokumen',
      href: props.order ? route('orders.documents.index', props.order.id) : '#',
    },
  ];
});

// Periksa apakah ada dokumen
const hasDocuments = computed(() => {
  return (
    (props.credentials && props.credentials.length > 0) ||
    (props.domainInfo && props.domainInfo.length > 0) ||
    (props.updates && props.updates.length > 0) ||
    (props.downloads && props.downloads.length > 0)
  );
});

// Format tanggal
const formatDate = (dateString) => {
  if (!dateString) return '-';
  
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric'
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

// Fungsi untuk memotong teks yang terlalu panjang
const truncateContent = (text, maxLength = 150) => {
  if (!text) return '';
  
  if (text.length <= maxLength) {
    return text;
  }
  
  return text.substring(0, maxLength) + '...';
};

// Navigasi ke halaman detail dokumen
const navigateToDocumentDetail = (orderId, documentId) => {
  window.location.href = route('orders.documents.show', [orderId, documentId]);
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