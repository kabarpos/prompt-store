<template>
  <Head title="Detail Pengeluaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4 md:p-6 pb-12">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-50">Detail Pengeluaran</h1>
        <div class="flex gap-2">
          <Link 
            :href="route('admin.expenses.edit', expense.id)" 
            class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-blue-600 text-white shadow-sm hover:bg-blue-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
          >
            <Edit class="mr-2 h-4 w-4" />
            Edit
          </Link>
          <Link 
            :href="route('admin.expenses.index')" 
            class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-slate-100 text-slate-900 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-50 dark:hover:bg-slate-700"
          >
            <ArrowLeft class="mr-2 h-4 w-4" />
            Kembali
          </Link>
        </div>
      </div>

      <Card class="border border-slate-200 dark:border-slate-700">
        <CardContent class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Nama Pengeluaran</h3>
              <p class="text-base font-medium text-slate-900 dark:text-slate-50">{{ expense.name }}</p>
            </div>

            <div class="space-y-1">
              <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Kategori</h3>
              <p class="text-base font-medium text-slate-900 dark:text-slate-50">{{ expense.category?.name || '-' }}</p>
            </div>

            <div class="space-y-1">
              <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Jumlah</h3>
              <p class="text-base font-medium text-slate-900 dark:text-slate-50">Rp {{ formatCurrency(expense.amount) }}</p>
            </div>

            <div class="space-y-1">
              <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Tanggal</h3>
              <p class="text-base font-medium text-slate-900 dark:text-slate-50">{{ formatDate(expense.expense_date) }}</p>
            </div>

            <div class="space-y-1">
              <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Metode Pembayaran</h3>
              <p class="text-base font-medium text-slate-900 dark:text-slate-50">
                {{ paymentMethodNames[expense.payment_method] || expense.payment_method }}
              </p>
            </div>

            <div class="space-y-1">
              <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Status</h3>
              <div class="flex items-center">
                <span 
                  class="px-2 py-1 rounded-full text-xs"
                  :class="{
                    'bg-yellow-100 text-yellow-800': expense.status === 'pending',
                    'bg-green-100 text-green-800': expense.status === 'completed',
                    'bg-red-100 text-red-800': expense.status === 'cancelled',
                  }"
                >
                  {{ expense.status }}
                </span>
              </div>
            </div>

            <div class="space-y-1">
              <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Pesanan Terkait</h3>
              <p class="text-base font-medium text-slate-900 dark:text-slate-50">
                {{ expense.order ? expense.order.order_number : 'Tidak ada' }}
              </p>
            </div>

            <div class="space-y-1">
              <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Dibuat oleh</h3>
              <p class="text-base font-medium text-slate-900 dark:text-slate-50">{{ expense.user.name }}</p>
            </div>
          </div>

          <div class="space-y-2 pt-4 border-t border-slate-200 dark:border-slate-700">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Deskripsi</h3>
            <div class="text-slate-900 dark:text-slate-50">
              <p v-if="expense.description">{{ expense.description }}</p>
              <p v-else class="text-slate-500 dark:text-slate-400 italic">Tidak ada deskripsi</p>
            </div>
          </div>

          <div v-if="expense.receipt_image" class="space-y-2 pt-4 border-t border-slate-200 dark:border-slate-700">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Bukti Pembayaran</h3>
            <div>
              <img 
                :src="'/storage/' + expense.receipt_image" 
                class="max-w-md rounded shadow-sm border border-slate-200 dark:border-slate-700" 
                alt="Bukti Pembayaran" 
              />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Edit, ArrowLeft } from 'lucide-vue-next';

// Definisikan tipe
interface Category {
  id: number;
  name: string;
}

interface Order {
  id: number;
  order_number: string;
}

interface Expense {
  id: number;
  name: string;
  amount: number;
  expense_date: string;
  expense_category_id: number;
  order_id: number | null;
  user_id: number;
  description: string | null;
  receipt_image: string | null;
  payment_method: 'cash' | 'transfer' | 'credit_card' | 'other';
  status: 'pending' | 'completed' | 'cancelled';
  category: Category | null;
  order: Order | null;
  user: {
    id: number;
    name: string;
  };
}

interface ExpenseShowProps {
  expense: Expense;
}

// Definisikan props dengan tipe
const props = defineProps<ExpenseShowProps>();

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Expenses',
    href: route('admin.expenses.index'),
  },
  {
    title: 'Detail',
    href: route('admin.expenses.show', props.expense.id),
  },
];

// Format currency
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('id-ID').format(value);
};

// Format date
const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  }).format(date);
};

// Payment method names
const paymentMethodNames = {
  cash: 'Tunai',
  transfer: 'Transfer',
  credit_card: 'Kartu Kredit',
  other: 'Lainnya'
};
</script> 