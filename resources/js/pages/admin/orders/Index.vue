<template>
  <Head title="Kelola Pesanan" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol aksi -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Kelola Pesanan</h1>
        <div class="flex items-center gap-2">
          <Link :href="route('admin.orders.statistics')">
            <Button variant="outline" size="sm" class="h-9">
              <BarChart3Icon class="h-4 w-4 mr-2" />
              Statistik
            </Button>
          </Link>
        </div>
      </div>

      <!-- Filters -->
      <Card class="border border-slate-200 dark:border-slate-700">
        <CardContent class="p-0">
          <div class="flex flex-col md:flex-row gap-4">
            <!-- Search Filter -->
            <div class="flex-1">
              <label for="search" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Cari</label>
              <div class="relative flex items-center">
                <input 
                  id="search"
                  v-model="search" 
                  type="text" 
                  placeholder="Nomor pesanan atau nama pelanggan" 
                  class="w-full h-9 pl-10 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <SearchIcon class="h-4 w-4 text-slate-400" />
                </div>
                <Button @click="applyFilters" size="sm" class="ml-2 h-9" :disabled="loading">
                  <LoaderIcon v-if="loading" class="h-4 w-4 mr-2 animate-spin" />
                  Cari
                </Button>
              </div>
            </div>
            
            <!-- Status Filter -->
            <div class="w-full md:w-64">
              <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
              <div class="relative custom-select-container status-select-container" :class="{ 'active': isStatusSelectOpen }">
                <div 
                  @click="toggleStatusSelect" 
                  class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                >
                  <span>{{ selectedStatusLabel }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isStatusSelectOpen }">
                    <path d="m6 9 6 6 6-6"></path>
                  </svg>
                </div>
                
                <div 
                  v-if="isStatusSelectOpen" 
                  class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                >
                  <div 
                    v-for="option in statusOptions" 
                    :key="option.value"
                    @click="selectStatus(option.value)"
                    class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                    :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': selectedStatus === option.value }"
                  >
                    {{ option.label }}
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex items-end gap-2">
              <Button @click="resetFilters" variant="outline" class="h-9" :disabled="loading">Reset</Button>
            </div>
          </div>
        </CardContent>
      </Card>
      
      <!-- Orders Table -->
      <Card class="border border-slate-200 dark:border-slate-700">
        <CardContent class="p-0">
          <AdminTable 
            :columns="columns" 
            :data="orders" 
            :loading="loading || false"
            emptyMessage="Tidak ada pesanan ditemukan"
          >
            <TableRow 
              v-for="order in orders.data" 
              :key="order.id" 
              class="border-b border-slate-200/60 dark:border-slate-700/60 hover:bg-slate-50 dark:hover:bg-slate-800/50"
            >
              <!-- Order Number -->
              <TableCell class="py-3.5 px-6 align-middle font-medium text-primary-600 dark:text-primary-400">
                {{ order.order_number }}
              </TableCell>
              
              <!-- Customer -->
              <TableCell class="py-3.5 px-6 align-middle">
                <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ order.customer_name }}</div>
                <div class="text-sm text-slate-500 dark:text-slate-400">{{ order.customer_phone }}</div>
              </TableCell>
              
              <!-- Total -->
              <TableCell class="py-3.5 px-6 align-middle text-center font-medium text-slate-900 dark:text-slate-100">
                {{ formatPrice(order.total_amount) }}
              </TableCell>
              
              <!-- Status -->
              <TableCell class="py-3.5 px-6 align-middle text-center">
                <Badge 
                  variant="outline" 
                  :class="{
                    'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400': order.status === 'pending',
                    'border-blue-400 text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400': order.status === 'processing',
                    'border-purple-400 text-purple-600 bg-purple-50 dark:bg-purple-900/20 dark:text-purple-400': order.status === 'review',
                    'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400': order.status === 'completed',
                    'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400': order.status === 'cancelled'
                  }"
                >
                  {{ getStatusLabel(order.status) }}
                </Badge>
              </TableCell>
              
              <!-- Date -->
              <TableCell class="py-3.5 px-6 align-middle text-center text-sm text-slate-500 dark:text-slate-400">
                {{ formatDate(order.created_at) }}
              </TableCell>
              
              <!-- Actions -->
              <TableCell class="py-3.5 px-6 align-middle text-center">
                <div class="flex items-center justify-center">
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="ghost" size="icon">
                        <MoreHorizontal class="h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem>
                        <Link :href="route('admin.orders.show', order.id)" class="flex w-full">
                          <EyeIcon class="h-4 w-4 mr-2" />
                          Lihat detail
                        </Link>
                      </DropdownMenuItem>
                      <DropdownMenuItem>
                        <Link :href="route('admin.orders.documents.index', order.id)" class="flex w-full items-center">
                          <FileText class="h-4 w-4 mr-2" />
                          Lihat dokumen
                        </Link>
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </div>
              </TableCell>
            </TableRow>
          </AdminTable>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, onMounted, ref, nextTick, onUnmounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { TableRow, TableCell } from '@/components/ui/table';
import Pagination from '@/components/Pagination.vue';
import { SearchIcon, EyeIcon, BarChart3Icon, MoreHorizontal, FileText, LoaderIcon } from 'lucide-vue-next';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import AdminTable from '@/components/AdminTable.vue';
import currencies from "@/lib/currency.js";
import { debounce } from 'lodash';
import { useToast } from '@/composables/useToast';

const props = defineProps({
  orders: Object,
  filters: Object,
  statuses: Object
});

const { toast } = useToast();

// Breadcrumbs untuk navigasi
const breadcrumbs = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Pesanan',
    href: route('admin.orders.index'),
  },
];

// State
const search = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || '');
const loading = ref(false);

// Status dropdown state
const isStatusSelectOpen = ref(false);
const statusSelectRef = ref(null);

// Status options
const statusOptions = [
  { value: '', label: 'Semua Status' }
];

// Populate status options from props
onMounted(() => {
  // Add status options from props
  Object.entries(props.statuses).forEach(([value, label]) => {
    statusOptions.push({ value, label });
  });
});

// Toggle dropdown status
const toggleStatusSelect = () => {
  isStatusSelectOpen.value = !isStatusSelectOpen.value;
};

// Pilih status
const selectStatus = (value) => {
  selectedStatus.value = value;
  isStatusSelectOpen.value = false;
  applyFilters();
};

// Handle click outside untuk status
const handleStatusClickOutside = (evt) => {
  if (statusSelectRef.value && !statusSelectRef.value.contains(evt.target)) {
    isStatusSelectOpen.value = false;
  }
};

// Setup dan cleanup event listener
onMounted(() => {
  document.addEventListener('click', handleStatusClickOutside);
  
  nextTick(() => {
    statusSelectRef.value = document.querySelector('.status-select-container');
  });
});

onUnmounted(() => {
  document.removeEventListener('click', handleStatusClickOutside);
});

// Computed property untuk label status terpilih
const selectedStatusLabel = computed(() => {
  if (!selectedStatus.value) return 'Semua Status';
  const status = statusOptions.find(option => option.value === selectedStatus.value);
  return status ? status.label : 'Semua Status';
});

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

const getStatusLabel = (status) => {
  return props.statuses[status] || status;
};

const buildFilterPayload = () => {
  const payload = {};
  if (search.value) payload.search = search.value;
  if (selectedStatus.value) payload.status = selectedStatus.value;
  return payload;
};

const updateSearch = debounce((value) => {
    router.get(route('admin.orders.index'), buildFilterPayload(), {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            if (page.props.orders && page.props.orders.data && page.props.orders.data.length === 0) {
                toast({
                    title: 'Tidak ada hasil',
                    description: 'Data pesanan tidak ditemukan.',
                    variant: 'destructive',
                });
            }
        }
    });
}, 500);

watch(search, (value) => {
    updateSearch(value);
});

const applyFilters = () => {
    loading.value = true;
    router.get(route('admin.orders.index'), buildFilterPayload(), {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            if (page.props.orders && page.props.orders.data && page.props.orders.data.length === 0) {
                toast({
                    title: 'Tidak ada hasil',
                    description: 'Data pesanan tidak ditemukan.',
                    variant: 'destructive',
                });
            }
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};

const resetFilters = () => {
    search.value = '';
    selectedStatus.value = '';
    loading.value = true;
    router.get(route('admin.orders.index'), {}, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            if (page.props.orders && page.props.orders.data && page.props.orders.data.length === 0) {
                toast({
                    title: 'Tidak ada hasil',
                    description: 'Data pesanan tidak ditemukan.',
                    variant: 'destructive',
                });
            }
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};

// Initialize filters from URL
onMounted(() => {
  if (props.filters) {
    search.value = props.filters.search || '';
    selectedStatus.value = props.filters.status || '';
  }
});

// Columns for AdminTable
const columns = [
  { label: 'Nomor Pesanan' },
  { label: 'Pelanggan' },
  { label: 'Total', headerClass: 'text-center' },
  { label: 'Status', headerClass: 'text-center' },
  { label: 'Tanggal', headerClass: 'text-center' },
  { label: 'Aksi', headerClass: 'text-center' }
];
</script>

<style>
/* Custom select styling */
.custom-select-container {
  position: relative;
  width: 100%;
  -webkit-tap-highlight-color: transparent;
  border-radius: 0.375rem;
}

.custom-select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  animation: slideDown 0.15s ease-out;
  z-index: 50;
}

.custom-select-option:first-child {
  border-top-left-radius: 0.375rem;
  border-top-right-radius: 0.375rem;
}

.custom-select-option:last-child {
  border-bottom-left-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Perbaikan outline saat fokus */
.custom-select-trigger {
  outline: none !important;
  -webkit-appearance: none;
  -webkit-tap-highlight-color: transparent !important;
}

.custom-select-trigger:focus,
.custom-select-trigger:focus-visible,
.custom-select-trigger:active,
.custom-select-trigger:hover,
.custom-select-trigger:-moz-focusring {
  outline: none !important;
  box-shadow: none !important;
  border-color: #0ea5e9 !important;
}

/* Fix untuk Firefox */
.custom-select-trigger:-moz-focusring {
  outline: none !important;
}

/* Fix untuk Safari dan Chrome */
.custom-select-trigger::-webkit-focus-inner {
  border: 0;
}

/* Fix tambahan untuk Chrome */
*:focus {
  outline-color: transparent !important;
}
</style> 