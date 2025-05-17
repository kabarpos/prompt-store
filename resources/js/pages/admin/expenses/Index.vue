<template>
  <Head title="Daftar Pengeluaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol tambah -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Daftar Pengeluaran</h1>
        <div class="flex gap-2">
          <Button 
            variant="outline" 
            @click="navigateToCategories"
            class="flex items-center gap-1.5 h-10"
          >
          <TagIcon class="h-4 w-4 mr-1" />
          Kategori
          </Button>
          <Link 
            :href="route('admin.expenses.create')" 
            class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-primary-600 text-white shadow-sm hover:bg-primary-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
          >
            <PlusIcon class="mr-2 h-4 w-4" />
            Tambah Pengeluaran
          </Link>
        </div>
      </div>

      <!-- Filter dan pencarian -->
      <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
        <form @submit.prevent="submit" class="grid gap-4 sm:grid-cols-2 md:grid-cols-4">
          <div class="space-y-2">
            <Label for="search">Cari</Label>
            <div class="relative">
              <Input 
                id="search" 
                v-model="form.search" 
                placeholder="Cari nama atau deskripsi" 
                class="w-full pl-9" 
              />
              <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <SearchIcon class="h-4 w-4" />
              </div>
            </div>
          </div>
          <div class="space-y-2">
            <Label for="category">Kategori</Label>
            <div class="relative custom-select-container category-select-container" :class="{ 'active': isCategorySelectOpen }">
              <div 
                @click="toggleCategorySelect" 
                class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
              >
                <span>{{ selectedCategoryLabel }}</span>
                <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isCategorySelectOpen }" />
              </div>
              
              <div 
                v-if="isCategorySelectOpen" 
                class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
              >
                <div 
                  class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                  @click="selectCategoryString('')"
                  :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.category_id === '' }"
                >
                  Semua Kategori
                </div>
                <div 
                  v-for="category in categories" 
                  :key="category.id"
                  @click="selectCategoryNumber(category.id)"
                  class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                  :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': Number(form.category_id) === category.id }"
                >
                  {{ category.name }}
                </div>
              </div>
            </div>
          </div>
          <div class="space-y-2">
            <Label for="status">Status</Label>
            <div class="relative custom-select-container status-select-container" :class="{ 'active': isStatusSelectOpen }">
              <div 
                @click="toggleStatusSelect" 
                class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
              >
                <span>{{ selectedStatusLabel }}</span>
                <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isStatusSelectOpen }" />
              </div>
              
              <div 
                v-if="isStatusSelectOpen" 
                class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
              >
                <div 
                  class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                  @click="selectStatus('')"
                  :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.status === '' }"
                >
                  Semua Status
                </div>
                <div 
                  class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                  @click="selectStatus('pending')"
                  :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.status === 'pending' }"
                >
                  Pending
                </div>
                <div 
                  class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                  @click="selectStatus('completed')"
                  :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.status === 'completed' }"
                >
                  Completed
                </div>
                <div 
                  class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                  @click="selectStatus('cancelled')"
                  :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.status === 'cancelled' }"
                >
                  Cancelled
                </div>
              </div>
            </div>
          </div>
          <div class="flex gap-2 items-end">
            <Button type="submit" variant="action">Filter</Button>
            <Button variant="action-outline" @click="resetFilters">Reset</Button>
          </div>
        </form>
      </div>

      <!-- Tabel Pengeluaran dengan AdminTable -->
      <AdminTable 
        :columns="columns" 
        :data="expenses" 
        :loading="loading"
        emptyMessage="Tidak ada data pengeluaran ditemukan"
      >
        <TableRow v-for="expense in expenses.data" :key="expense.id" class="hover:bg-secondary-100/50 dark:hover:bg-slate-900/90">
          <TableCell class="py-3.5 px-6 align-middle font-medium text-secondary-900 dark:text-white">
            {{ expense.name }}
          </TableCell>
          <TableCell class="py-3.5 px-6 align-middle">{{ expense.category?.name || '-' }}</TableCell>
          <TableCell class="py-3.5 px-6 align-middle">Rp {{ formatCurrency(expense.amount) }}</TableCell>
          <TableCell class="py-3.5 px-6 align-middle">{{ formatDate(expense.expense_date) }}</TableCell>
          <TableCell class="py-3.5 px-6 align-middle">
            <span 
              class="px-2.5 py-0.5 rounded-full text-xs font-medium inline-flex items-center gap-1.5"
              :class="{
                'bg-yellow-100 text-yellow-800 border border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800': expense.status === 'pending',
                'bg-green-100 text-green-800 border border-green-300 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800': expense.status === 'completed',
                'bg-red-100 text-red-800 border border-red-300 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800': expense.status === 'cancelled',
              }"
            >
              <span class="size-2 rounded-full" 
                :class="{
                  'bg-yellow-600 dark:bg-yellow-400': expense.status === 'pending',
                  'bg-green-600 dark:bg-green-400': expense.status === 'completed',
                  'bg-red-600 dark:bg-red-400': expense.status === 'cancelled',
                }"
              ></span>
              {{ expense.status }}
            </span>
          </TableCell>
          <TableCell class="py-3.5 px-6 align-middle text-right">
            <div class="flex justify-end">
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="action" size="icon" class="h-8 w-8 rounded-md">
                    <MoreHorizontal class="h-4 w-4" />
                    <span class="sr-only">Menu</span>
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-[160px]">
                  <DropdownMenuItem @click="viewExpense(expense)" class="flex items-center gap-2 cursor-pointer py-1.5">
                    <Eye class="h-4 w-4" />
                    <span>Lihat</span>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="editExpense(expense)" class="flex items-center gap-2 cursor-pointer py-1.5">
                    <Pencil class="h-4 w-4" />
                    <span>Edit</span>
                  </DropdownMenuItem>
                  <DropdownMenuItem 
                    @click="confirmDelete(expense)" 
                    variant="destructive" 
                    class="flex items-center gap-2 cursor-pointer py-1.5 text-red-600 dark:text-red-400"
                  >
                    <Trash class="h-4 w-4" />
                    <span>Hapus</span>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </TableCell>
        </TableRow>
      </AdminTable>
    </div>
    
    <!-- Dialog Konfirmasi Hapus -->
    <ConfirmationDialog
      :open="showDeleteDialog"
      @update:open="showDeleteDialog = $event"
      title="Konfirmasi Penghapusan"
      dangerMode
      :icon="Trash2"
      :loading="loading"
      confirmLabel="Hapus"
      @confirm="deleteExpense()"
    >
      <p class="mb-2 text-secondary-900 dark:text-secondary-200">
        Apakah Anda yakin ingin menghapus pengeluaran "{{ expenseToDelete?.name }}"?
      </p>
    </ConfirmationDialog>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { SearchIcon, Edit, Eye, PlusIcon, Trash, Trash2, Receipt, ChevronDownIcon, MoreHorizontal, Pencil, TagIcon } from 'lucide-vue-next';
import { TableRow, TableCell } from '@/components/ui/table';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import AdminTable from '@/components/AdminTable.vue';
import Pagination from '@/components/Pagination.vue';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { ref } from 'vue';

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

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface PaginatedData<T> {
  data: T[];
  links: PaginationLink[];
  current_page: number;
  from: number;
  to: number;
  total: number;
  last_page: number;
  per_page: number;
}

interface ExpensesProps {
  expenses: PaginatedData<Expense>;
  categories: Category[];
  orders: Order[];
  filters: {
    search?: string;
    category_id?: string;
    status?: string;
    date_from?: string;
    date_to?: string;
    order_id?: string;
  };
}

// Definisikan props dengan tipe
const props = defineProps<ExpensesProps>();

// State untuk loading dan dialog hapus
const loading = ref(false);
const showDeleteDialog = ref(false);
const expenseToDelete = ref<Expense | null>(null);

// Kolom tabel untuk AdminTable
const columns = [
  { label: 'Nama' },
  { label: 'Kategori' },
  { label: 'Jumlah' },
  { label: 'Tanggal' },
  { label: 'Status' },
  { label: 'Aksi', headerClass: 'text-right' }
];

// Form untuk filter
const form = useForm({
  search: props.filters.search || '',
  category_id: props.filters.category_id || '',
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  order_id: props.filters.order_id || '',
});

// Submit form filter
const submit = () => {
  form.get(route('admin.expenses.index'), {
    preserveState: true,
    preserveScroll: true,
  });
};

// Reset filter
const resetFilters = () => {
  form.search = '';
  form.category_id = '';
  form.status = '';
  form.date_from = '';
  form.date_to = '';
  form.order_id = '';
  submit();
};

// Konfirmasi hapus
const confirmDelete = (expense: Expense) => {
  expenseToDelete.value = expense;
  showDeleteDialog.value = true;
};

const deleteExpense = () => {
  if (!expenseToDelete.value) return;
  
  loading.value = true;
  
  useForm({}).delete(route('admin.expenses.destroy', expenseToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      expenseToDelete.value = null;
      showDeleteDialog.value = false;
    },
    onError: () => {
      // Notifikasi error akan ditangani oleh sistem
    },
    onFinish: () => {
      loading.value = false;
    }
  });
};

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

// New refs for custom select
const isCategorySelectOpen = ref(false);
const isStatusSelectOpen = ref(false);
const selectedCategoryLabel = ref('');
const selectedStatusLabel = ref('');

// Functions for custom select
const toggleCategorySelect = () => {
  isCategorySelectOpen.value = !isCategorySelectOpen.value;
};

const toggleStatusSelect = () => {
  isStatusSelectOpen.value = !isStatusSelectOpen.value;
};

// Inisialisasi label
selectedCategoryLabel.value = 'Semua Kategori';
selectedStatusLabel.value = 'Semua Status';

// Fungsi select untuk kategori dengan string (untuk opsi "")
const selectCategoryString = (id: string) => {
  form.category_id = id;
  isCategorySelectOpen.value = false;
  selectedCategoryLabel.value = 'Semua Kategori';
};

// Fungsi select untuk kategori dengan number (untuk opsi kategori)
const selectCategoryNumber = (id: number) => {
  form.category_id = id.toString();
  isCategorySelectOpen.value = false;
  selectedCategoryLabel.value = props.categories.find(c => c.id === id)?.name || '';
};

// Fungsi untuk status
const selectStatus = (status: string) => {
  form.status = status;
  isStatusSelectOpen.value = false;
  selectedStatusLabel.value = status === '' ? 'Semua Status' : status === 'pending' ? 'Pending' : status === 'completed' ? 'Completed' : 'Cancelled';
};

// Fungsi untuk melihat pengeluaran
const viewExpense = (expense: Expense) => {
  router.visit(route('admin.expenses.show', expense.id));
};

// Fungsi untuk mengedit pengeluaran
const editExpense = (expense: Expense) => {
  router.visit(route('admin.expenses.edit', expense.id));
};

// Fungsi navigasi ke halaman kategori pengeluaran
const navigateToCategories = () => {
  router.visit(route('admin.expense-categories.index'));
};
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