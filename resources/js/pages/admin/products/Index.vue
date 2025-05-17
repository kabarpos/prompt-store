<template>
    <Head title="Produk" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul dan tombol tambah -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Produk</h1>
                <div class="flex gap-2">
                    <Button 
                        variant="outline" 
                        @click="navigateToCategories"
                        class="flex items-center gap-1.5 h-10"
                    >
                        <TagIcon class="h-4 w-4 mr-1" />
                        Kategori
                    </Button>
                    <Link :href="route('admin.products.create')" class="cursor-pointer">
                        <Button 
                            variant="action" 
                            class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
                            <PlusIcon class="h-4 w-4" />
                            Tambah Produk
                        </Button>
                    </Link>
                </div>
            </div>
            
            <!-- Filters Card -->
            <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search Filter -->
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Cari</label>
                        <div class="relative">
                            <input 
                                id="search"
                                v-model="search" 
                                type="text" 
                                placeholder="Cari nama produk..." 
                                class="w-full h-9 pl-10 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
                                @keyup.enter="applyFilters"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <SearchIcon class="h-4 w-4 text-slate-400" />
                            </div>
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
                                <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isStatusSelectOpen }" />
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
                        <Button @click="resetFilters" variant="action-outline">Reset</Button>
                    </div>
                </div>
            </div>
            
            <!-- Tabel Produk dengan AdminTable -->
            <AdminTable 
                :columns="columns" 
                :data="products" 
                :loading="loading"
                emptyMessage="Tidak ada produk ditemukan"
            >
                <TableRow v-for="product in products.data" :key="product.id" class="hover:bg-secondary-100/50 dark:hover:bg-slate-900/90">
                    <TableCell class="py-3.5 px-6 align-middle">
                        <img
                            :src="'/storage/' + product.featured_image"
                            :alt="product.name"
                            class="w-16 h-16 object-cover rounded-md"
                        />
                    </TableCell>
                    <TableCell class="py-3.5 px-6 align-middle font-medium text-secondary-900 dark:text-white">{{ product.name }}</TableCell>
                    <TableCell class="py-3.5 px-6 align-middle">
                        <Badge v-if="product.category" variant="outline" class="px-2.5 py-0.5 text-xs font-medium">
                            {{ product.category.name }}
                        </Badge>
                        <Badge v-else variant="outline" class="px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                            Kategori tidak tersedia
                        </Badge>
                    </TableCell>
                    <TableCell class="py-3.5 px-6 align-middle">{{ formatPrice(product.price) }}</TableCell>
                    <TableCell class="py-3.5 px-6 align-middle">
                        <div v-if="product.url" class="flex items-center gap-2">
                            <a 
                                :href="product.url" 
                                target="_blank" 
                                class="text-blue-600 hover:text-blue-800 underline text-xs truncate max-w-[150px]"
                                :title="product.url"
                            >
                                {{ product.slug }}
                            </a>
                            <button 
                                @click="copyToClipboard(product.url)" 
                                class="text-gray-500 hover:text-gray-700 focus:outline-none"
                                title="Salin URL"
                            >
                                <ClipboardIcon class="w-4 h-4" />
                            </button>
                        </div>
                        <span v-else>-</span>
                    </TableCell>
                    <TableCell class="py-3.5 px-6 align-middle">
                        <StatusBadge 
                            :status="product.is_active ? '1' : '0'" 
                            :statusMap="statusMap"
                        />
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
                                    <DropdownMenuItem @click="viewProduct(product)" class="flex items-center gap-2 cursor-pointer py-1.5">
                                        <Eye class="h-4 w-4" />
                                        <span>Lihat</span>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem @click="editProduct(product)" class="flex items-center gap-2 cursor-pointer py-1.5">
                                        <Pencil class="h-4 w-4" />
                                        <span>Edit</span>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem 
                                        @click="showHapusDialog(product)" 
                                        variant="destructive" 
                                        class="flex items-center gap-2 cursor-pointer py-1.5">
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
            @confirm="hapusProduct()"
        >
            <p class="mb-2 text-secondary-900 dark:text-secondary-200">Apakah Anda yakin ingin menghapus {{ selectedProduct?.name }}?</p>
        </ConfirmationDialog>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch, nextTick, onUnmounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { PlusIcon, MoreHorizontal, Eye, Pencil, Trash, Trash2, ClipboardIcon, SearchIcon, ChevronDownIcon, TagIcon } from 'lucide-vue-next';
import { TableRow, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { toast } from 'vue-sonner';
import AdminTable from '@/components/AdminTable.vue';
import StatusBadge from '@/components/StatusBadge.vue';

const props = defineProps({
    products: Object,
    filters: {
        type: Object,
        default: () => ({
            search: '',
            status: ''
        })
    },
    categories: Array
});

// Fungsi navigasi ke halaman categories
const navigateToCategories = () => {
  router.visit(route('admin.categories.index'));
};

const breadcrumbs = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Produk',
        href: route('admin.products.index'),
    },
];

// State untuk dialog hapus
const selectedProduct = ref(null);
const showDeleteDialog = ref(false);
const loading = ref(false);

// State untuk filter
const search = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');

// State untuk dropdown
const isStatusSelectOpen = ref(false);
const statusSelectRef = ref(null);

// Filter options
const statusOptions = [
    { value: '', label: 'Semua Status' },
    { value: '1', label: 'Aktif' },
    { value: '0', label: 'Nonaktif' },
];

// Kolom tabel untuk AdminTable
const columns = [
    { label: 'Gambar' },
    { label: 'Nama' },
    { label: 'Kategori' },
    { label: 'Harga' },
    { label: 'URL' },
    { label: 'Status' },
    { label: 'Tindakan', headerClass: 'text-right' }
];

// Status map untuk StatusBadge
const statusMap = {
    '1': 'Aktif',
    '0': 'Nonaktif'
};

const selectedStatusLabel = computed(() => {
    const option = statusOptions.find(option => option.value === selectedStatus.value);
    return option ? option.label : 'Semua Status';
});

// Initialize filters and event listeners
onMounted(() => {
    // Set up click outside listeners
    document.addEventListener('click', handleStatusClickOutside);
    
    nextTick(() => {
        statusSelectRef.value = document.querySelector('.status-select-container');
    });
});

onUnmounted(() => {
    document.removeEventListener('click', handleStatusClickOutside);
});

// Toggle dropdown status
const toggleStatusSelect = () => {
    isStatusSelectOpen.value = !isStatusSelectOpen.value;
};

// Handle status selection
const selectStatus = (value) => {
    selectedStatus.value = value;
    isStatusSelectOpen.value = false;
    applyFilters(); // Langsung terapkan filter ketika status dipilih
};

// Handle click outside for status dropdown
const handleStatusClickOutside = (evt) => {
    if (statusSelectRef.value && !statusSelectRef.value.contains(evt.target)) {
        isStatusSelectOpen.value = false;
    }
};

// Filter methods
const applyFilters = () => {
    router.get(route('admin.products.index'), {
        search: search.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        replace: true
    });
};

const resetFilters = () => {
    // Reset semua filter ke nilai default
    search.value = '';
    selectedStatus.value = '';
    
    // Pastikan UI diupdate sebelum mengirim request
    nextTick(() => {
        // Gunakan router.visit untuk memuat ulang halaman tanpa filter
        router.visit(route('admin.products.index'), {
            preserveScroll: false
        });
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const viewProduct = (product) => {
    router.visit(route('admin.products.show', product.id));
};

const editProduct = (product) => {
    router.visit(route('admin.products.edit', product.id));
};

const showHapusDialog = (product) => {
    selectedProduct.value = product;
    showDeleteDialog.value = true;
};

const hapusProduct = () => {
    if (!selectedProduct.value) return;
    
    loading.value = true;
    
    router.delete(route('admin.products.destroy', selectedProduct.value.id), {
        onSuccess: () => {
            toast.success('Berhasil', {
                description: `Produk ${selectedProduct.value.name} berhasil dihapus`,
            });
            showDeleteDialog.value = false;
        },
        onError: (errors) => {
            toast.error('Gagal', {
                description: `Terjadi kesalahan saat menghapus produk: ${errors.message || 'Unknown error'}`,
            });
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};

// Fungsi untuk menyalin URL ke clipboard
const copyToClipboard = (url) => {
    navigator.clipboard.writeText(url).then(() => {
        toast.success('URL berhasil disalin!');
    });
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

