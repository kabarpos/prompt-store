<template>
  <Head title="Detail Kategori Pengeluaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol kembali -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Detail Kategori Pengeluaran</h1>
        <div class="flex gap-2">
          <Link :href="route('admin.expense-categories.edit', category.id)" class="cursor-pointer">
            <Button variant="action" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
              <Pencil class="h-4 w-4" />
              Edit
            </Button>
          </Link>
          <Link :href="route('admin.expense-categories.index')" class="cursor-pointer">
            <Button variant="outline" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
              <ArrowLeft class="h-4 w-4" />
              Kembali
            </Button>
          </Link>
        </div>
      </div>
      
      <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <div>
            <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Informasi Kategori</h2>
            <p class="text-secondary-600 dark:text-secondary-400 mt-1">Detail lengkap kategori pengeluaran</p>
          </div>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Detail Kategori -->
            <div class="space-y-4">
              <div>
                <h3 class="text-sm font-medium text-secondary-600 dark:text-secondary-400">Nama Kategori</h3>
                <p class="mt-1 text-base font-medium text-secondary-900 dark:text-white">{{ category.name }}</p>
              </div>
              
              <div>
                <h3 class="text-sm font-medium text-secondary-600 dark:text-secondary-400">Deskripsi</h3>
                <p class="mt-1 text-base text-secondary-900 dark:text-white">{{ category.description || '-' }}</p>
              </div>
              
              <div>
                <h3 class="text-sm font-medium text-secondary-600 dark:text-secondary-400">Status</h3>
                <div class="mt-1">
                  <div v-if="category.is_active" 
                       class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-900 dark:bg-green-900 dark:text-green-300 border border-emerald-300 dark:border-green-800 w-fit">
                    <span class="size-2 bg-emerald-600 dark:bg-emerald-400 rounded-full"></span>
                    <span>Aktif</span>
                  </div>
                  <div v-else
                       class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-900 dark:bg-yellow-900 dark:text-yellow-300 border border-amber-300 dark:border-yellow-800 w-fit">
                    <span class="size-2 bg-amber-600 dark:bg-amber-400 rounded-full"></span>
                    <span>Nonaktif</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Bagian Kanan -->
            <div class="space-y-4">
              <div>
                <h3 class="text-sm font-medium text-secondary-600 dark:text-secondary-400">Warna</h3>
                <div class="mt-1 flex items-center gap-2">
                  <div 
                    v-if="category.color" 
                    class="w-8 h-8 rounded" 
                    :style="{ backgroundColor: category.color }"
                  ></div>
                  <span v-if="category.color" class="text-base text-secondary-900 dark:text-white">{{ category.color }}</span>
                  <span v-else class="text-base text-secondary-900 dark:text-white">-</span>
                </div>
              </div>
              
              <div>
                <h3 class="text-sm font-medium text-secondary-600 dark:text-secondary-400">Jumlah Pengeluaran</h3>
                <p class="mt-1 text-base font-medium text-secondary-900 dark:text-white">{{ category.expenses_count }}</p>
              </div>
              
              <div>
                <h3 class="text-sm font-medium text-secondary-600 dark:text-secondary-400">Tanggal Dibuat</h3>
                <p class="mt-1 text-base text-secondary-900 dark:text-white">{{ formatDate(category.created_at) }}</p>
              </div>
              
              <div>
                <h3 class="text-sm font-medium text-secondary-600 dark:text-secondary-400">Terakhir Diperbarui</h3>
                <p class="mt-1 text-base text-secondary-900 dark:text-white">{{ formatDate(category.updated_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Pengeluaran Terkait kategori ini -->
      <div v-if="category.expenses && category.expenses.length > 0" class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <div>
            <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Pengeluaran Terkait</h2>
            <p class="text-secondary-600 dark:text-secondary-400 mt-1">Daftar pengeluaran yang menggunakan kategori ini</p>
          </div>
        </div>
        <div class="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow class="hover:bg-transparent border-b border-secondary-200 dark:border-slate-700">
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Nama</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Jumlah</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Tanggal</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Status</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400 text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow 
                v-for="expense in category.expenses" 
                :key="expense.id" 
                class="border-b border-secondary-200/60 dark:border-slate-700/60 hover:bg-secondary-100/50 dark:hover:bg-secondary-800/50"
              >
                <TableCell class="py-3.5 px-6 align-middle font-medium text-secondary-900 dark:text-white">
                  {{ expense.name }}
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ formatCurrency(expense.amount) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">{{ formatDate(expense.expense_date) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <div 
                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="{
                      'bg-emerald-100 text-emerald-900 dark:bg-green-900 dark:text-green-300 border border-emerald-300 dark:border-green-800': expense.status === 'completed',
                      'bg-amber-100 text-amber-900 dark:bg-yellow-900 dark:text-yellow-300 border border-amber-300 dark:border-yellow-800': expense.status === 'pending',
                      'bg-red-100 text-red-900 dark:bg-red-900 dark:text-red-300 border border-red-300 dark:border-red-800': expense.status === 'cancelled'
                    }"
                  >
                    <span 
                      class="size-2 rounded-full"
                      :class="{
                        'bg-emerald-600 dark:bg-emerald-400': expense.status === 'completed',
                        'bg-amber-600 dark:bg-amber-400': expense.status === 'pending',
                        'bg-red-600 dark:bg-red-400': expense.status === 'cancelled'
                      }"
                    ></span>
                    <span>{{ getStatusLabel(expense.status) }}</span>
                  </div>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle text-right">
                  <Link :href="route('admin.expenses.show', expense.id)" class="inline-flex items-center gap-1.5 text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                    <Eye class="h-4 w-4" />
                    <span>Lihat</span>
                  </Link>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>
      
      <div v-else class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden p-6">
        <div class="text-center py-8">
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-secondary-100 dark:bg-secondary-800 mb-4">
            <ReceiptIcon class="h-6 w-6 text-secondary-600 dark:text-secondary-400" />
          </div>
          <h3 class="text-lg font-medium text-secondary-900 dark:text-white mb-2">Tidak ada pengeluaran</h3>
          <p class="text-secondary-600 dark:text-secondary-400">Belum ada pengeluaran yang menggunakan kategori ini</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Pencil, Eye, ReceiptIcon } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';

interface Expense {
  id: number;
  name: string;
  amount: number;
  expense_date: string;
  status: 'pending' | 'completed' | 'cancelled';
}

interface ExpenseCategory {
  id: number;
  name: string;
  description: string | null;
  color: string | null;
  is_active: boolean;
  expenses_count: number;
  created_at: string;
  updated_at: string;
  expenses?: Expense[];
}

interface Props {
  category: ExpenseCategory;
}

const props = defineProps<Props>();

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Expense Categories',
    href: route('admin.expense-categories.index'),
  },
  {
    title: props.category.name,
    href: route('admin.expense-categories.show', props.category.id),
  }
];

// Format tanggal
const formatDate = (dateString: string): string => {
  if (!dateString) return '-';
  
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

// Format currency
const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
};

// Get status label
const getStatusLabel = (status: string): string => {
  switch (status) {
    case 'completed':
      return 'Selesai';
    case 'pending':
      return 'Pending';
    case 'cancelled':
      return 'Dibatalkan';
    default:
      return status;
  }
};
</script> 