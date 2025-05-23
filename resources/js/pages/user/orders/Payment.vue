<template>
  <Head title="Pilih Metode Pembayaran" />
  
  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Pilih Metode Pembayaran</h1>
      </div>

      <!-- Detail Order -->
      <Card class="border border-slate-200 dark:border-slate-700">
        <CardHeader>
          <CardTitle>Detail Pesanan #{{ order.order_number }}</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <h3 class="text-lg font-medium">Informasi Pesanan</h3>
              <div class="mt-2 space-y-2">
                <p><span class="font-medium">Nomor Pesanan:</span> {{ order.order_number }}</p>
                <p><span class="font-medium">Tanggal:</span> {{ formatDate(order.created_at) }}</p>
                <p><span class="font-medium">Status:</span> {{ order.status_label }}</p>
                <p><span class="font-medium">Total:</span> {{ formatPrice(order.total_amount) }}</p>
              </div>
            </div>
            <div>
              <h3 class="text-lg font-medium">Informasi Pelanggan</h3>
              <div class="mt-2 space-y-2">
                <p><span class="font-medium">Nama:</span> {{ order.customer_name }}</p>
                <p><span class="font-medium">Email:</span> {{ order.customer_email }}</p>
                <p><span class="font-medium">Telepon:</span> {{ order.customer_phone }}</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Metode Pembayaran -->
      <Card class="border border-slate-200 dark:border-slate-700">
        <CardHeader>
          <CardTitle>Pilih Metode Pembayaran</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="updatePaymentMethod">
            <div class="space-y-4">
              <div 
                v-for="method in paymentMethods" 
                :key="method.id"
                class="border rounded-md p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer"
                :class="{'border-primary-500 bg-primary-50 dark:bg-primary-900/20': selectedMethod === method.id}"
                @click="selectedMethod = method.id"
              >
                <div class="flex items-center gap-2">
                  <input
                    type="radio"
                    :id="`method-${method.id}`"
                    name="payment-method"
                    :value="method.id"
                    :checked="selectedMethod === method.id"
                    @change="selectedMethod = method.id"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500"
                  />
                  <label :for="`method-${method.id}`" class="font-medium">{{ method.name }}</label>
                </div>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-300 pl-6">
                  {{ method.description }}
                </div>
                <div v-if="method.account_number" class="mt-1 text-sm pl-6">
                  <strong>No. Rekening:</strong> {{ method.account_number }}
                </div>
                <div v-if="method.account_name" class="mt-1 text-sm pl-6">
                  <strong>Atas Nama:</strong> {{ method.account_name }}
                </div>
              </div>

              <div class="flex justify-end mt-6">
                <Button type="submit" :disabled="!selectedMethod || loading">
                  <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Pilih Metode Pembayaran
                </Button>
              </div>
            </div>
          </form>
        </CardContent>
      </Card>
      
      <Toaster />
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Toaster } from '@/components/ui/sonner';

const props = defineProps({
  order: Object,
  paymentMethods: Array,
});

// State
const selectedMethod = ref(props.order?.payment?.payment_method_id || null);
const loading = ref(false);

// Computed
const breadcrumbItems = computed(() => [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
  {
    title: 'Pesanan Saya',
    href: route('orders.index'),
  },
  {
    title: `Pesanan #${props.order.order_number}`,
    href: route('orders.show', props.order.id),
  },
  {
    title: 'Metode Pembayaran',
    href: route('orders.payment', props.order.id),
  },
]);

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

const updatePaymentMethod = () => {
  if (!selectedMethod.value) return;
  
  loading.value = true;
  
  useForm({
    payment_method_id: selectedMethod.value
  }).post(route('orders.payment.update', props.order.id), {
    onFinish: () => {
      loading.value = false;
    }
  });
};
</script> 