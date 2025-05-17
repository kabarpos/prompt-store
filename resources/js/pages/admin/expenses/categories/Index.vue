<template>
  <Head title="Kategori Pengeluaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol tambah -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Kategori Pengeluaran</h1>
        <Link :href="route('admin.expense-categories.create')">
          <Button variant="action" class="flex items-center gap-1.5 w-full sm:w-auto">
            <PlusIcon class="w-4 h-4" />
            Tambah Kategori
          </Button>
        </Link>
      </div>
      
      <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-secondary-200 dark:border-slate-700">
          <div class="flex justify-between items-center">
            <div>
              <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Daftar Kategori Pengeluaran</h2>
              <p class="text-secondary-600 dark:text-secondary-400 mt-1">Kelola kategori-kategori untuk pengeluaran bisnis Anda</p>
            </div>
            <Button @click="openCreateModal" class="inline-flex items-center gap-1">
              <PlusIcon class="h-4 w-4" />
              <span>Tambah Kategori</span>
            </Button>
          </div>
        </div>
        
        <AdminTable 
          :columns="columns" 
          :data="categories" 
          :loading="loading"
          emptyMessage="Tidak ada kategori pengeluaran yang tersedia"
        >
          <TableRow 
            v-for="category in categories.data" 
            :key="category.id" 
            class="border-b border-secondary-200/60 dark:border-slate-700/60 hover:bg-secondary-100/50 dark:hover:bg-secondary-800/50"
          >
            <TableCell class="py-3.5 px-6 align-middle font-medium text-secondary-900 dark:text-white">
              {{ category.name }}
            </TableCell>
            <TableCell class="py-3.5 px-6 align-middle">{{ category.description || '-' }}</TableCell>
            <TableCell class="py-3.5 px-6 align-middle">
              <div v-if="category.color" class="w-6 h-6 rounded" :style="{ backgroundColor: category.color }"></div>
              <span v-else>-</span>
            </TableCell>
            <TableCell class="py-3.5 px-6 align-middle">{{ category.expenses_count }}</TableCell>
            <TableCell class="py-3.5 px-6 align-middle">
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
            </TableCell>
            <TableCell class="py-3.5 px-6 align-middle text-right">
              <div class="flex justify-end gap-2">
                <Button 
                  variant="outline" 
                  size="sm" 
                  @click="viewCategory(category)"
                  class="h-8 px-2"
                >
                  <Eye class="h-4 w-4" />
                </Button>
                <Button 
                  variant="outline" 
                  size="sm" 
                  @click="editCategory(category)"
                  class="h-8 px-2"
                >
                  <Pencil class="h-4 w-4" />
                </Button>
                <Button 
                  variant="outline" 
                  size="sm"
                  @click="confirmDelete(category)" 
                  class="h-8 px-2 text-red-600 border-red-200 hover:bg-red-50 hover:text-red-700 hover:border-red-300 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-950 dark:hover:text-red-300"
                >
                  <Trash class="h-4 w-4" />
                </Button>
              </div>
            </TableCell>
          </TableRow>
        </AdminTable>
      </div>
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
      @confirm="deleteCategory()"
    >
      <p class="mb-2 text-secondary-900 dark:text-secondary-200">Apakah Anda yakin ingin menghapus {{ categoryToDelete?.name }}?</p>
    </ConfirmationDialog>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { SearchIcon, Eye, Edit, PlusIcon, Trash, Trash2, MoreHorizontal, Pencil } from 'lucide-vue-next';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import { ref, computed } from 'vue';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import AdminTable from '@/components/AdminTable.vue';

// Definisikan tipe
interface ExpenseCategory {
  id: number;
  name: string;
  description: string | null;
  color: string | null;
  is_active: boolean;
  expenses_count: number;
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

interface ExpenseCategoriesProps {
  categories: PaginatedData<ExpenseCategory>;
  filters: {
    search?: string;
    status?: string;
  };
}

// Definisikan props dengan tipe
const props = defineProps<ExpenseCategoriesProps>();

// Form untuk filter
const form = useForm({
  search: props.filters.search || '',
  status: props.filters.status || '',
});

// State untuk select status
const statusOptions = [
  { value: '', label: 'Semua Status' },
  { value: '1', label: 'Aktif' },
  { value: '0', label: 'Tidak Aktif' },
];

const selectedStatusLabel = computed(() => {
  const option = statusOptions.find(option => option.value === form.status);
  return option ? option.label : 'Semua Status';
});

// Submit form filter
const submit = () => {
  form.get(route('admin.expense-categories.index'), {
    preserveState: true,
    preserveScroll: true,
  });
};

// Reset filter
const resetFilters = () => {
  form.search = '';
  form.status = '';
  submit();
};

// State untuk dialog hapus
const categoryToDelete = ref<ExpenseCategory | null>(null);
const showDeleteDialog = ref(false);
const loading = ref(false);

// Fungsi untuk actions
const viewCategory = (category: ExpenseCategory) => {
  router.visit(route('admin.expense-categories.show', category.id));
};

const editCategory = (category: ExpenseCategory) => {
  router.visit(route('admin.expense-categories.edit', category.id));
};

const openDeleteDialog = (category: ExpenseCategory) => {
  categoryToDelete.value = category;
  showDeleteDialog.value = true;
};

const deleteCategory = () => {
  if (!categoryToDelete.value) return;
  
  loading.value = true;
  
  router.delete(route('admin.expense-categories.destroy', categoryToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Notifikasi sukses akan ditampilkan oleh sistem
      showDeleteDialog.value = false;
      categoryToDelete.value = null;
    },
    onError: () => {
      // Notifikasi error akan ditampilkan oleh sistem
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
    title: 'Expense Categories',
    href: route('admin.expense-categories.index'),
  },
];

// Add columns definition for AdminTable
const columns = [
  { label: 'Nama' },
  { label: 'Deskripsi' },
  { label: 'Warna' },
  { label: 'Jumlah Pengeluaran' },
  { label: 'Status' },
  { label: 'Aksi', headerClass: 'text-right' }
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
</style> 