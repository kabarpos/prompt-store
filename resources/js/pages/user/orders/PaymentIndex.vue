<template>
  <Head title="Konfirmasi Pembayaran" />
  
  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Konfirmasi Pembayaran</h1>
      </div>

      <!-- Pesanan yang Memerlukan Pembayaran -->
      <Card class="border border-slate-200 dark:border-slate-700">
        <CardHeader>
          <CardTitle>Pesanan yang Memerlukan Pembayaran</CardTitle>
        </CardHeader>
        <CardContent>
          <div v-if="pendingOrders.length === 0" class="text-center py-10">
            <p class="text-slate-500 dark:text-slate-400">Tidak ada pesanan yang memerlukan pembayaran</p>
          </div>
          
          <div v-else>
            <AdminTable 
              :columns="pendingOrderColumns" 
              :data="{ data: pendingOrders, links: [] }" 
              :loading="false"
              emptyMessage="Tidak ada pesanan yang memerlukan pembayaran"
            >
              <TableRow v-for="order in pendingOrders" :key="order.id" class="hover:bg-secondary-100/50 dark:hover:bg-slate-900/90">
                <TableCell class="py-3.5 px-6 align-middle font-medium text-primary-600 dark:text-primary-400">
                  <Link 
                    :href="route('orders.show', order.id)" 
                    class="hover:text-primary-500 dark:hover:text-primary-300"
                  >
                    {{ order.order_number }}
                  </Link>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ formatDate(order.created_at) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ formatPrice(order.total_amount) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <Badge 
                    variant="outline" 
                    class="border-blue-400 text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700"
                  >
                    {{ order.payment?.payment_method?.type === 'payment_gateway' ? 'Payment Gateway' : 'Transfer Bank' }}
                  </Badge>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <Badge 
                    variant="outline" 
                    :class="{
                      'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': order.payment?.status === 'pending',
                      'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': order.payment?.status === 'completed',
                      'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': order.payment?.status === 'failed'
                    }"
                  >
                    {{ getPaymentStatusLabel(order.payment?.status) }}
                  </Badge>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <div class="flex space-x-2">
                    <Button
                      v-if="order.payment?.payment_method"
                      variant="outline"
                      size="sm"
                      asChild
                    >
                      <Link :href="route('orders.payment.confirm', order.id)">
                        Konfirmasi Pembayaran
                      </Link>
                    </Button>
                    <Button
                      v-else
                      variant="outline"
                      size="sm"
                      asChild
                    >
                      <Link :href="route('orders.payment', order.id)">
                        Pilih Metode Pembayaran
                      </Link>
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </AdminTable>
          </div>
        </CardContent>
      </Card>
      
      <!-- Riwayat Konfirmasi Pembayaran -->
      <Card class="border border-slate-200 dark:border-slate-700">
        <CardHeader>
          <CardTitle>Riwayat Konfirmasi Pembayaran</CardTitle>
        </CardHeader>
        <CardContent>
          <div v-if="confirmations.length === 0" class="text-center py-10">
            <p class="text-slate-500 dark:text-slate-400">Belum ada riwayat konfirmasi pembayaran</p>
          </div>
          
          <div v-else>
            <AdminTable 
              :columns="confirmationColumns" 
              :data="{ data: confirmations, links: [] }" 
              :loading="false"
              emptyMessage="Belum ada riwayat konfirmasi pembayaran"
            >
              <TableRow v-for="confirmation in confirmations" :key="confirmation.id" class="hover:bg-secondary-100/50 dark:hover:bg-slate-900/90">
                <TableCell class="py-3.5 px-6 align-middle font-medium text-primary-600 dark:text-primary-400">
                  <Link 
                    :href="route('orders.show', confirmation.payment.order.id)" 
                    class="hover:text-primary-500 dark:hover:text-primary-300"
                  >
                    {{ confirmation.payment.order.order_number }}
                  </Link>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ formatDate(confirmation.created_at) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ confirmation.bank_name }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ formatPrice(confirmation.amount) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
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
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <div class="flex space-x-2">
                    <Button
                      v-if="confirmation.status === 'rejected'"
                      variant="outline"
                      size="sm"
                      asChild
                    >
                      <Link :href="route('orders.payment.confirm', confirmation.payment.order.id)">
                        Kirim Ulang
                      </Link>
                    </Button>
                    <span v-else>-</span>
                  </div>
                </TableCell>
              </TableRow>
            </AdminTable>
          </div>
        </CardContent>
      </Card>
      
      <Toaster />
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { TableRow, TableCell } from '@/components/ui/table';
import AdminTable from '@/components/AdminTable.vue';
import { Toaster } from '@/components/ui/sonner';

const props = defineProps({
  pendingOrders: Array,
  confirmations: Array,
});

// Computed
const breadcrumbItems = computed(() => [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
  {
    title: 'Konfirmasi Pembayaran',
    href: route('orders.payment.index'),
  },
]);

// Columns definition for AdminTable
const pendingOrderColumns = [
  { label: 'Nomor Pesanan' },
  { label: 'Tanggal' },
  { label: 'Total' },
  { label: 'Metode Pembayaran' },
  { label: 'Status' },
  { label: 'Aksi' }
];

const confirmationColumns = [
  { label: 'Nomor Pesanan' },
  { label: 'Tanggal Konfirmasi' },
  { label: 'Bank' },
  { label: 'Jumlah' },
  { label: 'Status' },
  { label: 'Aksi' }
];

// Loading state
const loading = ref(false);

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};

const getPaymentStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Pembayaran',
    'completed': 'Pembayaran Diterima',
    'failed': 'Pembayaran Gagal'
  };
  
  return status ? (statusMap[status] || status) : '';
};

const getConfirmationStatusLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu Verifikasi',
    'verified': 'Terverifikasi',
    'rejected': 'Ditolak'
  };
  
  return status ? (statusMap[status] || status) : '';
};
</script> 