<template>
  <Head title="Dokumen Saya" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan statistik -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Dokumen Saya</h1>
        <div class="flex items-center gap-2">
          <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
              <div class="relative">
                <input 
                  id="search"
                  v-model="searchQuery" 
                  type="text" 
                  placeholder="Cari berdasarkan judul, nomor pesanan..." 
                  class="w-full h-9 pl-10 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Search class="h-4 w-4 text-slate-400" />
                </div>
              </div>
            </div>
            <div v-if="searchQuery" class="flex items-end">
              <Button @click="searchQuery = ''" variant="outline" size="sm" class="h-9">
                <X class="h-4 w-4 mr-1" />
                Reset
              </Button>
            </div>
          </div>
          <Badge variant="secondary" class="h-8 px-3 text-sm">
            {{ totalDocuments }} Dokumen
          </Badge>
          <Badge v-if="totalUnread > 0" variant="destructive" class="h-8 px-3 text-sm">
            {{ totalUnread }} Belum Dibaca
          </Badge>
        </div>
      </div>

      <!-- Dokumen Belum Dibaca -->
      <Card v-if="filteredUnreadDocuments.length > 0" class="mb-6">
        <CardHeader class="pb-3">
          <CardTitle class="flex items-center">
            <AlertTriangle class="h-5 w-5 text-red-500 mr-2" />
            Dokumen Belum Dibaca
          </CardTitle>
          <CardDescription>
            Dokumen-dokumen yang perlu Anda baca
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="doc in filteredUnreadDocuments" :key="doc.id" class="p-4 bg-white dark:bg-slate-800 border border-red-100 dark:border-red-900/30 rounded-md">
              <div class="flex flex-col gap-4">
                <div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 mb-2">
                      <Badge :variant="getBadgeVariant(doc.type)">
                        {{ getDocumentTypeLabel(doc.type) }}
                      </Badge>
                      <Badge variant="destructive" size="sm">
                        Belum Dibaca
                      </Badge>
                    </div>
                  </div>
                  <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-1">{{ doc.title }}</h4>
                  <div class="text-sm text-slate-500 dark:text-slate-400">
                    <p>Pesanan: <span class="font-medium">{{ doc.order?.order_number || 'N/A' }}</span></p>
                    <p>{{ formatDate(doc.created_at) }}</p>
                    <p v-if="doc.expires_at" :class="{ 'text-red-600 dark:text-red-400 font-medium': isExpired(doc.expires_at) }">
                      {{ getExpiryText(doc.expires_at) }}
                    </p>
                  </div>
                </div>
                <div class="flex gap-2 mt-2">
                  <Button 
                    variant="outline" 
                    size="sm"
                    class="flex-1"
                    @click="navigateToDocumentDetail(doc.order_id, doc.id)"
                  >
                    <Eye class="h-4 w-4 mr-1" />
                    Lihat Detail
                  </Button>
                  <Button 
                    variant="secondary" 
                    size="sm"
                    class="flex-1"
                    @click="markAsRead(doc.order_id, doc.id)"
                  >
                    <CheckCircle class="h-4 w-4 mr-1" />
                    Tandai Dibaca
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Semua Dokumen -->
      <Card>
        <CardHeader>
          <CardTitle>Semua Dokumen</CardTitle>
          <CardDescription>
            Dokumen, kredensial, dan informasi terkait pesanan Anda
          </CardDescription>
        </CardHeader>
        <CardContent>
          <!-- Semua Dokumen dalam Satu Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Kredensial -->
            <div 
              v-for="doc in filteredCredentials" 
              :key="'credential-'+doc.id" 
              class="p-4 bg-white dark:bg-slate-800 border border-blue-100 dark:border-slate-700 rounded-md"
            >
              <div class="flex flex-col gap-4">
                <div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 mb-2">
                      <Badge variant="blue">
                        Kredensial
                      </Badge>
                      <Badge v-if="doc.expires_at && isExpired(doc.expires_at)" variant="destructive" size="sm">
                        Kedaluwarsa
                      </Badge>
                    </div>
                  </div>
                  <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-1">{{ doc.title }}</h4>
                  <div class="text-sm text-slate-500 dark:text-slate-400">
                    <p>Pesanan: <span class="font-medium">{{ doc.order?.order_number || 'N/A' }}</span></p>
                    <p>{{ formatDate(doc.created_at) }}</p>
                    <p v-if="doc.expires_at" :class="{ 'text-red-600 dark:text-red-400 font-medium': isExpired(doc.expires_at) }">
                      {{ getExpiryText(doc.expires_at) }}
                    </p>
                  </div>
                </div>
                <div class="mt-2">
                  <Button 
                    variant="outline" 
                    size="sm"
                    class="w-full"
                    @click="navigateToDocumentDetail(doc.order_id, doc.id)"
                  >
                    <Eye class="h-4 w-4 mr-1" />
                    Lihat Detail
                  </Button>
                </div>
              </div>
            </div>

            <!-- Informasi Domain -->
            <div 
              v-for="doc in filteredDomainInfo" 
              :key="'domain-'+doc.id" 
              class="p-4 bg-white dark:bg-slate-800 border border-green-100 dark:border-slate-700 rounded-md"
            >
              <div class="flex flex-col gap-4">
                <div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 mb-2">
                      <Badge variant="green">
                        Domain
                      </Badge>
                      <Badge v-if="doc.expires_at && isExpired(doc.expires_at)" variant="destructive" size="sm">
                        Kedaluwarsa
                      </Badge>
                    </div>
                  </div>
                  <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-1">{{ doc.title }}</h4>
                  <div class="text-sm text-slate-500 dark:text-slate-400">
                    <p>Pesanan: <span class="font-medium">{{ doc.order?.order_number || 'N/A' }}</span></p>
                    <p>{{ formatDate(doc.created_at) }}</p>
                    <p v-if="doc.expires_at" :class="{ 'text-red-600 dark:text-red-400 font-medium': isExpired(doc.expires_at) }">
                      {{ getExpiryText(doc.expires_at) }}
                    </p>
                  </div>
                </div>
                <div class="mt-2">
                  <Button 
                    variant="outline" 
                    size="sm"
                    class="w-full"
                    @click="navigateToDocumentDetail(doc.order_id, doc.id)"
                  >
                    <Eye class="h-4 w-4 mr-1" />
                    Lihat Detail
                  </Button>
                </div>
              </div>
            </div>

            <!-- Pembaruan -->
            <div 
              v-for="doc in filteredUpdates" 
              :key="'update-'+doc.id" 
              class="p-4 bg-white dark:bg-slate-800 border border-orange-100 dark:border-slate-700 rounded-md"
            >
              <div class="flex flex-col gap-4">
                <div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 mb-2">
                      <Badge variant="orange">
                        Pembaruan
                      </Badge>
                    </div>
                  </div>
                  <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-1">{{ doc.title }}</h4>
                  <div class="text-sm text-slate-500 dark:text-slate-400">
                    <p>Pesanan: <span class="font-medium">{{ doc.order?.order_number || 'N/A' }}</span></p>
                    <p>{{ formatDate(doc.created_at) }}</p>
                  </div>
                </div>
                <div class="mt-2">
                  <Button 
                    variant="outline" 
                    size="sm"
                    class="w-full"
                    @click="navigateToDocumentDetail(doc.order_id, doc.id)"
                  >
                    <Eye class="h-4 w-4 mr-1" />
                    Lihat Detail
                  </Button>
                </div>
              </div>
            </div>

            <!-- Unduhan -->
            <div 
              v-for="doc in filteredDownloads" 
              :key="'download-'+doc.id" 
              class="p-4 bg-white dark:bg-slate-800 border border-purple-100 dark:border-slate-700 rounded-md"
            >
              <div class="flex flex-col gap-4">
                <div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 mb-2">
                      <Badge variant="purple">
                        Unduhan
                      </Badge>
                    </div>
                  </div>
                  <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-1">{{ doc.title }}</h4>
                  <div class="text-sm text-slate-500 dark:text-slate-400">
                    <p>Pesanan: <span class="font-medium">{{ doc.order?.order_number || 'N/A' }}</span></p>
                    <p>{{ formatDate(doc.created_at) }}</p>
                  </div>
                </div>
                <div class="mt-2">
                  <Button 
                    variant="outline" 
                    size="sm"
                    class="w-full"
                    @click="navigateToDocumentDetail(doc.order_id, doc.id)"
                  >
                    <Eye class="h-4 w-4 mr-1" />
                    Lihat Detail
                  </Button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State - Dokumen Terfilter -->
          <div v-if="hasDocuments && !hasFilteredDocuments" class="text-center py-12">
            <div class="mx-auto h-16 w-16 text-slate-300">
              <Search class="h-16 w-16" />
            </div>
            <h3 class="mt-4 text-xl font-medium text-slate-900 dark:text-white">Tidak Ditemukan</h3>
            <p class="mt-2 text-slate-500 dark:text-slate-400 max-w-md mx-auto">
              Tidak ada dokumen yang cocok dengan kata kunci "{{ searchQuery }}". Coba dengan kata kunci lain.
            </p>
            <div class="mt-4">
              <Button @click="searchQuery = ''" variant="outline">Reset Pencarian</Button>
            </div>
          </div>

          <!-- Empty State - Tidak Ada Dokumen -->
          <div v-if="!hasDocuments" class="text-center py-12">
            <div class="mx-auto h-16 w-16 text-slate-300">
              <File class="h-16 w-16" />
            </div>
            <h3 class="mt-4 text-xl font-medium text-slate-900 dark:text-white">Tidak Ada Dokumen</h3>
            <p class="mt-2 text-slate-500 dark:text-slate-400 max-w-md mx-auto">
              Anda belum memiliki dokumen terkait pesanan. Dokumen akan muncul di sini saat tersedia.
            </p>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { toRefs } from '@vueuse/core';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { 
  CheckCircle, Download, Eye, File, RefreshCw, AlertTriangle, Key, Globe,
  Search, X
} from 'lucide-vue-next';

const props = defineProps({
  documents: {
    type: Array,
    default: () => []
  },
  breadcrumbs: {
    type: Array,
    default: () => [
      { title: 'Dashboard', href: route('dashboard') },
      { title: 'Dokumen Saya', href: route('my-documents') }
    ]
  }
});

const { documents } = toRefs(props);
const { toast } = useToast();

// Search state
const searchQuery = ref('');

// Filter dokumen berdasarkan kata kunci pencarian
const filteredDocuments = computed(() => {
  if (!searchQuery.value.trim()) return documents.value || [];
  
  const query = searchQuery.value.toLowerCase().trim();
  return (documents.value || []).filter(doc => {
    const matchTitle = doc.title?.toLowerCase().includes(query);
    const matchOrderNumber = doc.order?.order_number?.toLowerCase().includes(query);
    const matchType = getDocumentTypeLabel(doc.type).toLowerCase().includes(query);
    
    return matchTitle || matchOrderNumber || matchType;
  });
});

// Computed properties untuk filter dokumen berdasarkan tipe
const filteredCredentials = computed(() => {
  return filteredDocuments.value.filter(doc => doc.type === 'credential' && doc.is_read);
});

const filteredDomainInfo = computed(() => {
  return filteredDocuments.value.filter(doc => doc.type === 'domain' && doc.is_read);
});

const filteredUpdates = computed(() => {
  return filteredDocuments.value.filter(doc => doc.type === 'update' && doc.is_read);
});

const filteredDownloads = computed(() => {
  return filteredDocuments.value.filter(doc => doc.type === 'download' && doc.is_read);
});

const filteredUnreadDocuments = computed(() => {
  return filteredDocuments.value.filter(doc => !doc.is_read);
});

const totalDocuments = computed(() => {
  return (documents.value || []).length;
});

const totalUnread = computed(() => {
  return (documents.value || []).filter(doc => !doc.is_read).length;
});

const hasDocuments = computed(() => {
  return totalDocuments.value > 0;
});

// Cek apakah ada dokumen yang terfilter
const hasFilteredDocuments = computed(() => {
  return filteredCredentials.value.length > 0 || 
         filteredDomainInfo.value.length > 0 || 
         filteredUpdates.value.length > 0 || 
         filteredDownloads.value.length > 0 || 
         filteredUnreadDocuments.value.length > 0;
});

// Fungsi untuk mendapatkan label tipe dokumen sesuai bahasa Indonesia
function getDocumentTypeLabel(type) {
  const types = {
    'credential': 'Kredensial',
    'domain': 'Domain',
    'update': 'Pembaruan',
    'download': 'Unduhan',
    'invoice': 'Faktur',
    'receipt': 'Kuitansi'
  };
  
  return types[type] || 'Dokumen';
}

// Fungsi untuk mendapatkan variant badge berdasarkan tipe dokumen
function getBadgeVariant(type) {
  const variants = {
    'credential': 'blue',
    'domain': 'green',
    'update': 'orange',
    'download': 'purple',
    'invoice': 'yellow',
    'receipt': 'default'
  };
  
  return variants[type] || 'default';
}

// Fungsi untuk memformat tanggal
function formatDate(dateString) {
  if (!dateString) return '';
  
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
}

// Fungsi untuk mengecek apakah dokumen sudah kedaluwarsa
function isExpired(expiryDate) {
  if (!expiryDate) return false;
  
  const now = new Date();
  const expiry = new Date(expiryDate);
  return now > expiry;
}

// Fungsi untuk mendapatkan teks kedaluwarsa
function getExpiryText(expiryDate) {
  if (!expiryDate) return '';
  
  const now = new Date();
  const expiry = new Date(expiryDate);
  
  if (now > expiry) {
    return 'Kedaluwarsa: ' + formatDate(expiryDate);
  }
  
  return 'Kedaluwarsa pada: ' + formatDate(expiryDate);
}

// Fungsi untuk menandai dokumen sebagai dibaca
async function markAsRead(orderId, documentId) {
  try {
    await router.post(route('orders.documents.mark-as-read', [orderId, documentId]));
    toast({
      title: 'Dokumen ditandai sebagai dibaca',
      description: 'Dokumen berhasil ditandai sebagai telah dibaca',
      variant: 'success'
    });
  } catch (error) {
    toast({
      title: 'Error',
      description: 'Gagal menandai dokumen sebagai dibaca',
      variant: 'destructive'
    });
  }
}

// Fungsi untuk navigasi ke halaman detail dokumen
function navigateToDocumentDetail(orderId, documentId) {
  router.visit(route('orders.documents.show', [orderId, documentId]));
}
</script> 