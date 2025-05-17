<template>
  <Head title="Pesanan Saya" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul halaman -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Pesanan Saya</h1>
      </div>
      
      <!-- Filters Card -->
      <Card>
        <CardContent className="p-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Filter -->
            <div class="flex-1">
              <Label for="search" class="mb-1.5">Cari</Label>
              <div class="relative">
                <Input 
                  id="search"
                  v-model="search" 
                  type="text" 
                  placeholder="Cari nomor pesanan..." 
                  class="pl-10"
                  @keyup.enter="applyFilters"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <SearchIcon class="h-4 w-4 text-slate-400" />
                </div>
              </div>
            </div>
            
            <!-- Status Filter -->
            <div class="w-full md:w-64">
              <div class="space-y-1.5">
                <Label for="status">Status</Label>
                <Select v-model="selectedStatus" @update:modelValue="applyFilters">
                  <SelectTrigger id="status">
                    <SelectValue :placeholder="selectedStatusLabel" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="">Semua Status</SelectItem>
                    <SelectItem v-for="(label, status) in statuses" :key="status" :value="status">
                      {{ label }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex items-end gap-2">
              <Button @click="resetFilters" variant="outline">Reset</Button>
            </div>
          </div>
        </CardContent>
      </Card>
      
      <!-- Order List -->
      <Card v-if="orders.data.length > 0">
        <AdminTable 
          :columns="columns" 
          :data="orders" 
          :loading="loading || false"
          emptyMessage="Belum ada pesanan"
        >
          <TableRow v-for="order in orders.data" :key="order.id" class="hover:bg-secondary-100/50 dark:hover:bg-slate-900/90">
            <!-- Order Number -->
            <TableCell class="py-3.5 px-6 align-middle font-medium text-primary-600 dark:text-primary-400">
              {{ order.order_number }}
            </TableCell>
            
            <!-- Total -->
            <TableCell class="py-3.5 px-6 align-middle text-center font-medium">
              {{ formatPrice(order.total_amount) }}
            </TableCell>
            
            <!-- Status -->
            <TableCell class="py-3.5 px-6 align-middle text-center">
              <Badge 
                variant="outline" 
                :class="{
                  'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': order.status === 'pending',
                  'border-blue-400 text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700': order.status === 'processing',
                  'border-purple-400 text-purple-600 bg-purple-50 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700': order.status === 'review',
                  'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': order.status === 'completed',
                  'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': order.status === 'cancelled'
                }"
              >
                {{ getStatusLabel(order.status) }}
              </Badge>
            </TableCell>
            
            <!-- Date -->
            <TableCell class="py-3.5 px-6 align-middle text-center text-slate-500 dark:text-slate-400">
              {{ formatDate(order.created_at) }}
            </TableCell>
            
            <!-- Actions -->
            <TableCell class="py-3.5 px-6 align-middle text-center">
              <Link :href="route('orders.track', { order: order.order_number })">
                <Button size="sm" variant="ghost" class="h-8 px-2">
                  <EyeIcon class="h-4 w-4" />
                  <span class="sr-only">Lihat Detail</span>
                </Button>
              </Link>
            </TableCell>
          </TableRow>
        </AdminTable>
      </Card>
      
      <!-- Empty State -->
      <Card v-else class="text-center py-16">
        <CardContent>
          <div class="text-6xl mb-4">📦</div>
          <h3 class="text-xl font-semibold mb-2 text-slate-900 dark:text-white">Belum ada pesanan</h3>
          <p class="text-slate-500 dark:text-slate-400 mb-6 max-w-md mx-auto">Anda belum melakukan pemesanan atau tidak ada pesanan yang sesuai dengan filter yang dipilih.</p>
          <Button v-if="search || selectedStatus" @click="resetFilters">Reset Filter</Button>
          <div v-else>
            <Link :href="route('products.index')">
              <Button>Mulai Belanja</Button>
            </Link>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed, nextTick, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { TableRow, TableCell } from '@/components/ui/table';
import Pagination from '@/components/Pagination.vue';
import AdminTable from '@/components/AdminTable.vue';
import { SearchIcon, EyeIcon } from 'lucide-vue-next';

const props = defineProps({
  orders: Object,
  filters: Object,
  statuses: Object
});

// Breadcrumbs untuk navigasi
const breadcrumbs = [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
  {
    title: 'Pesanan Saya',
    href: route('orders.index'),
  },
];

// State
const search = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || '');

// Computed property untuk label status terpilih
const selectedStatusLabel = computed(() => {
  if (!selectedStatus.value) return 'Semua Status';
  return props.statuses[selectedStatus.value] || 'Semua Status';
});

// Columns definition for AdminTable
const columns = [
  { label: 'No. Pesanan' },
  { label: 'Total', headerClass: 'text-center' },
  { label: 'Status', headerClass: 'text-center' },
  { label: 'Tanggal', headerClass: 'text-center' },
  { label: 'Aksi', headerClass: 'text-center' }
];

// Loading state
const loading = ref(false);

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

const getStatusLabel = (status) => {
  if (!status) return '';
  if (!props.statuses) return status;
  return props.statuses[status] || status;
};

const applyFilters = () => {
  router.get(
    route('orders.index'),
    { 
      search: search.value, 
      status: selectedStatus.value 
    },
    { 
      preserveState: true,
      replace: true,
      only: ['orders']
    }
  );
};

const resetFilters = () => {
  search.value = '';
  selectedStatus.value = '';
  
  // Pastikan UI diupdate sebelum mengirim request
  nextTick(() => {
    // Gunakan router.visit untuk memuat ulang halaman tanpa filter
    router.visit(route('orders.index'), {
      preserveScroll: false
    });
  });
};

onMounted(() => {
  // Set filters from URL if any
  if (props.filters) {
    search.value = props.filters.search || '';
    selectedStatus.value = props.filters.status || '';
  }
});
</script> 