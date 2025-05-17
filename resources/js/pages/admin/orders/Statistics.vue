<template>
  <Head title="Statistik Pesanan" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol aksi -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Statistik Pesanan</h1>
        <div class="flex items-center gap-2">
          <Link :href="route('admin.orders.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <Button variant="outline" size="sm" class="h-9">
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Kembali ke Daftar Pesanan
            </Button>
          </Link>
        </div>
      </div>

      <!-- Order Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Total Orders -->
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center mb-1">
              <ReceiptIcon class="h-5 w-5 text-gray-400 dark:text-gray-500 mr-2" />
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pesanan</h3>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ statistics.totalOrders }}</p>
          </CardContent>
        </Card>
        
        <!-- Pending Orders -->
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center mb-1">
              <ClockIcon class="h-5 w-5 text-yellow-400 mr-2" />
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu Konfirmasi</h3>
            </div>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-500">{{ statistics.pendingOrders }}</p>
          </CardContent>
        </Card>
        
        <!-- Processing Orders -->
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center mb-1">
              <ActivityIcon class="h-5 w-5 text-blue-400 mr-2" />
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Sedang Diproses</h3>
            </div>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-500">{{ statistics.processingOrders }}</p>
          </CardContent>
        </Card>
        
        <!-- Completed Orders -->
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center mb-1">
              <CheckCircleIcon class="h-5 w-5 text-green-400 mr-2" />
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Selesai</h3>
            </div>
            <p class="text-3xl font-bold text-green-600 dark:text-green-500">{{ statistics.completedOrders }}</p>
          </CardContent>
        </Card>
        
        <!-- Cancelled Orders -->
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center mb-1">
              <XCircleIcon class="h-5 w-5 text-red-400 mr-2" />
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibatalkan</h3>
            </div>
            <p class="text-3xl font-bold text-red-600 dark:text-red-500">{{ statistics.cancelledOrders }}</p>
          </CardContent>
        </Card>
      </div>
      
      <!-- Revenue Card -->
      <Card class="mb-8">
        <CardContent class="p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Total Pendapatan</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">Total pendapatan dari semua pesanan</p>
            </div>
            <div class="mt-2 md:mt-0">
              <span class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ formatPrice(statistics.totalRevenue) }}</span>
            </div>
          </div>
          
          <!-- Revenue Chart Placeholder - Future Enhancement -->
          <div class="h-64 bg-gray-50 dark:bg-slate-900 rounded-lg flex items-center justify-center">
            <div class="text-center">
              <BarChart3Icon class="h-12 w-12 text-gray-300 dark:text-gray-700 mb-2 mx-auto" />
              <p class="text-gray-500 dark:text-gray-400">Grafik pendapatan akan tersedia segera</p>
            </div>
          </div>
        </CardContent>
      </Card>
      
      <!-- Recent Orders -->
      <Card>
        <CardHeader>
          <CardTitle>Pesanan Terbaru</CardTitle>
        </CardHeader>
        
        <CardContent>
          <div class="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Nomor Pesanan</TableHead>
                  <TableHead>Pelanggan</TableHead>
                  <TableHead class="text-center">Total</TableHead>
                  <TableHead class="text-center">Status</TableHead>
                  <TableHead class="text-center">Tanggal</TableHead>
                  <TableHead class="text-center">Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="order in recentOrders" :key="order.id">
                  <!-- Order Number -->
                  <TableCell class="font-medium text-primary-600 dark:text-primary-400">
                    {{ order.order_number }}
                  </TableCell>
                  
                  <!-- Customer -->
                  <TableCell>
                    <div class="font-medium">{{ order.customer_name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ order.customer_phone }}</div>
                  </TableCell>
                  
                  <!-- Total -->
                  <TableCell class="text-center font-medium">
                    {{ formatPrice(order.total_amount) }}
                  </TableCell>
                  
                  <!-- Status -->
                  <TableCell class="text-center">
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
                  <TableCell class="text-center text-gray-500 dark:text-gray-400">
                    {{ formatDate(order.created_at) }}
                  </TableCell>
                  
                  <!-- Actions -->
                  <TableCell class="text-center">
                    <Link :href="route('admin.orders.show', order.id)" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                      <Button size="sm" variant="ghost" class="h-8 px-2">
                        <EyeIcon class="h-4 w-4" />
                        <span class="sr-only">Lihat</span>
                      </Button>
                    </Link>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
          
          <div class="flex justify-center mt-6">
            <Link :href="route('admin.orders.index')" class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300">
              <Button variant="outline">Lihat Semua Pesanan</Button>
            </Link>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  ArrowLeftIcon, 
  EyeIcon, 
  ReceiptIcon, 
  ClockIcon, 
  ActivityIcon, 
  CheckCircleIcon, 
  XCircleIcon, 
  BarChart3Icon
} from 'lucide-vue-next';
import { 
  Card, 
  CardHeader, 
  CardTitle, 
  CardContent 
} from '@/components/ui/card';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table';

const props = defineProps({
  statistics: Object,
  recentOrders: Array
});

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
  {
    title: 'Statistik',
    href: route('admin.orders.statistics'),
  }
];

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
  const statusMap = {
    'pending': 'Menunggu Konfirmasi',
    'processing': 'Sedang Diproses',
    'review': 'Review',
    'completed': 'Selesai',
    'cancelled': 'Dibatalkan'
  };
  
  return statusMap[status] || status;
};
</script> 