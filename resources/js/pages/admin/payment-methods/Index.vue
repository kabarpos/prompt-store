<template>
    <Head title="Metode Pembayaran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul dan tombol tambah -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Metode Pembayaran</h1>
                <Link :href="route('admin.payment-methods.create')" class="cursor-pointer">
                    <Button variant="action" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
                        <PlusIcon class="w-4 h-4" />
                        Tambah Pembayaran
                    </Button>
                </Link>
            </div>
            
            <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-secondary-200 dark:border-slate-700">
                    <div>
                        <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Daftar Metode Pembayaran</h2>
                        <p class="text-secondary-600 dark:text-secondary-400 mt-1">Kelola metode pembayaran yang tersedia untuk pelanggan Anda</p>
                    </div>
                </div>
                
                <AdminTable 
                    :columns="columns" 
                    :data="paymentMethods" 
                    :loading="loading"
                    emptyMessage="Belum ada metode pembayaran"
                >
                    <TableRow v-for="method in paymentMethods.data" :key="method.id" class="border-b border-secondary-200/60 dark:border-slate-700/60 hover:bg-secondary-100/50 dark:hover:bg-secondary-800/50">
                        <TableCell class="py-3.5 px-6 align-middle">
                            <img
                                v-if="method.logo"
                                :src="'/storage/' + method.logo"
                                :alt="method.name"
                                class="w-10 h-10 object-cover rounded-md"
                            />
                            <div v-else class="w-10 h-10 bg-secondary-100 dark:bg-secondary-800 rounded-md flex items-center justify-center">
                                <CreditCard class="w-6 h-6 text-secondary-400 dark:text-secondary-500" />
                            </div>
                        </TableCell>
                        <TableCell class="py-3.5 px-6 align-middle">
                            <div class="font-medium">{{ method.name }}</div>
                            <div class="text-xs text-secondary-500 dark:text-secondary-400">
                                {{ method.code }}
                            </div>
                        </TableCell>
                        <TableCell class="py-3.5 px-6 align-middle">
                            <Badge variant="outline" :class="{
                                'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700': method.type === 'bank_transfer',
                                'bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700': method.type === 'payment_gateway'
                            }">
                                {{ method.type === 'bank_transfer' ? 'Transfer Bank' : 'Payment Gateway' }}
                            </Badge>
                        </TableCell>
                        <TableCell class="py-3.5 px-6 align-middle">
                            <div v-if="method.is_active" 
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
                        <TableCell class="py-3.5 px-6 align-middle">{{ method.sort_order }}</TableCell>
                        <TableCell class="py-3.5 px-6 align-middle text-right">
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="action" size="icon" class="h-8 w-8 rounded-md">
                                        <MoreHorizontal class="h-4 w-4" />
                                        <span class="sr-only">Menu</span>
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" class="w-[160px]">
                                    <DropdownMenuItem @click="viewMethod(method)" class="flex items-center gap-2 cursor-pointer py-1.5">
                                        <Eye class="h-4 w-4" />
                                        <span>Detail</span>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem @click="editMethod(method)" class="flex items-center gap-2 cursor-pointer py-1.5">
                                        <Pencil class="h-4 w-4" />
                                        <span>Edit</span>
                                    </DropdownMenuItem>
                                    
                                    <DropdownMenuItem 
                                        @click="showHapusDialog(method)" 
                                        variant="destructive" 
                                        class="flex items-center gap-2 cursor-pointer py-1.5">
                                        <Trash class="h-4 w-4" />
                                        <span>Hapus</span>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </AdminTable>
            </div>
            
            <div v-if="paymentMethods.links && paymentMethods.links.length > 0" class="py-4 px-6 flex items-center justify-between border-t border-secondary-200 dark:border-slate-700">
                <Pagination :links="paymentMethods.links" />
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
            @confirm="hapusMethod()"
        >
            <p class="mb-2">Apakah Anda yakin ingin menghapus {{ selectedMethod?.name }}?</p>
            <p v-if="selectedMethod?.payments_count > 0" class="text-orange-600 dark:text-orange-400">
                Metode pembayaran ini sudah digunakan dalam {{ selectedMethod.payments_count }} transaksi pembayaran!
            </p>
        </ConfirmationDialog>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { PlusIcon, MoreHorizontal, Pencil, Trash, CreditCard, Eye, Trash2 } from 'lucide-vue-next';
import { TableRow, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import StatusBadge from '@/components/ui/StatusBadge.vue';
import AdminTable from '@/components/AdminTable.vue';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Breadcrumbs untuk navigasi
const breadcrumbs = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Metode Pembayaran',
        href: route('admin.payment-methods.index'),
    },
];

// State untuk dialog hapus
const selectedMethod = ref(null);
const showDeleteDialog = ref(false);
const loading = ref(false);

const props = defineProps({
    paymentMethods: Object
});

const viewMethod = (method) => {
    router.visit(route('admin.payment-methods.show', method.id));
};

const editMethod = (method) => {
    router.visit(route('admin.payment-methods.edit', method.id));
};

const showHapusDialog = (method) => {
    selectedMethod.value = method;
    showDeleteDialog.value = true;
};

const hapusMethod = () => {
    if (!selectedMethod.value) return;
    
    loading.value = true;
    
    router.delete(route('admin.payment-methods.destroy', selectedMethod.value.id), {
        onSuccess: () => {
            toast.success('Berhasil', {
                description: `Metode pembayaran ${selectedMethod.value.name} berhasil dihapus`,
            });
            showDeleteDialog.value = false;
        },
        onError: (errors) => {
            toast.error('Gagal', {
                description: errors.message || 'Gagal menghapus metode pembayaran',
            });
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};

// Columns definition for AdminTable
const columns = [
    { label: 'Logo' },
    { label: 'Nama' },
    { label: 'Tipe' },
    { label: 'Status' },
    { label: 'Urutan' },
    { label: 'Tindakan', headerClass: 'text-right' }
];
</script> 