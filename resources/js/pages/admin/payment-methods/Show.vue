<template>
    <Head :title="`Detail Metode Pembayaran - ${paymentMethod.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul dan tombol kembali -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold">Detail Metode Pembayaran</h1>
                <div class="flex gap-2">
                    <Link :href="route('admin.payment-methods.edit', paymentMethod.id)" class="cursor-pointer">
                        <Button variant="outline" class="flex items-center gap-1.5">
                            <Pencil class="h-4 w-4" />
                            Edit
                        </Button>
                    </Link>
                    <Link :href="route('admin.payment-methods.index')" class="cursor-pointer">
                        <Button variant="outline" class="flex items-center gap-1.5">
                            <ArrowLeft class="h-4 w-4" />
                            Kembali
                        </Button>
                    </Link>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Informasi Utama -->
                <div class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-50 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden md:col-span-2">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-4">
                            <div v-if="paymentMethod.logo" class="flex-shrink-0">
                                <img 
                                    :src="'/storage/' + paymentMethod.logo" 
                                    :alt="paymentMethod.name" 
                                    class="w-16 h-16 object-cover rounded-md"
                                />
                            </div>
                            <div v-else class="flex-shrink-0 w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-md flex items-center justify-center">
                                <CreditCard class="w-8 h-8 text-slate-400 dark:text-slate-300" />
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold">{{ paymentMethod.name }}</h2>
                                <div class="flex items-center gap-2 mt-1">
                                    <Badge variant="outline" :class="{
                                        'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700': paymentMethod.type === 'bank_transfer',
                                        'bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700': paymentMethod.type === 'payment_gateway'
                                    }">
                                        {{ paymentMethod.type === 'bank_transfer' ? 'Transfer Bank' : 'Payment Gateway' }}
                                    </Badge>
                                    <Badge v-if="paymentMethod.is_active" variant="outline" class="bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700">
                                        Aktif
                                    </Badge>
                                    <Badge v-else variant="outline" class="bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700">
                                        Nonaktif
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Detail Umum -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium">Informasi Umum</h3>
                                
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="text-sm text-slate-500 dark:text-slate-400">Kode</div>
                                    <div class="col-span-2 text-sm font-medium">{{ paymentMethod.code }}</div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="text-sm text-slate-500 dark:text-slate-400">Deskripsi</div>
                                    <div class="col-span-2 text-sm">{{ paymentMethod.description || 'Tidak ada deskripsi' }}</div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="text-sm text-slate-500 dark:text-slate-400">Urutan</div>
                                    <div class="col-span-2 text-sm">{{ paymentMethod.sort_order }}</div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="text-sm text-slate-500 dark:text-slate-400">Dibuat</div>
                                    <div class="col-span-2 text-sm">{{ formatDate(paymentMethod.created_at) }}</div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="text-sm text-slate-500 dark:text-slate-400">Diperbarui</div>
                                    <div class="col-span-2 text-sm">{{ formatDate(paymentMethod.updated_at) }}</div>
                                </div>
                            </div>
                            
                            <!-- Detail Bank/Payment Gateway -->
                            <div class="space-y-4" v-if="paymentMethod.type === 'bank_transfer'">
                                <h3 class="text-lg font-medium">Detail Rekening Bank</h3>
                                
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="text-sm text-slate-500 dark:text-slate-400">Nama Bank</div>
                                    <div class="col-span-2 text-sm font-medium">{{ paymentMethod.bank_name || '-' }}</div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="text-sm text-slate-500 dark:text-slate-400">Nomor Rekening</div>
                                    <div class="col-span-2 text-sm">{{ paymentMethod.account_number || '-' }}</div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-2 py-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="text-sm text-slate-500 dark:text-slate-400">Atas Nama</div>
                                    <div class="col-span-2 text-sm">{{ paymentMethod.account_name || '-' }}</div>
                                </div>
                            </div>
                            
                            <div class="space-y-4" v-else-if="paymentMethod.type === 'payment_gateway'">
                                <h3 class="text-lg font-medium">Detail Payment Gateway</h3>
                                
                                <div class="py-2">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        Konfigurasi payment gateway dapat diatur di pengaturan integrasi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistik Penggunaan -->
                <div class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-50 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden h-fit">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-lg font-medium">Statistik Penggunaan</h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Total Transaksi</div>
                            <div class="text-2xl font-semibold">{{ paymentMethod.payments_count || 0 }}</div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4">
                            <div class="text-sm text-slate-500 dark:text-slate-400 mb-3">Status</div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm">Dapat dihapus</div>
                                    <div>
                                        <span v-if="paymentMethod.payments_count === 0" class="inline-block w-5 h-5 bg-emerald-500 dark:bg-emerald-600 rounded-full"></span>
                                        <span v-else class="inline-block w-5 h-5 bg-red-500 dark:bg-red-600 rounded-full"></span>
                                    </div>
                                </div>
                                <div v-if="paymentMethod.payments_count > 0" class="text-xs text-red-500 dark:text-red-400">
                                    Metode pembayaran ini telah digunakan dalam transaksi dan tidak dapat dihapus.
                                </div>
                                <div v-else class="text-xs text-emerald-500 dark:text-emerald-400">
                                    Metode pembayaran ini belum digunakan dan dapat dihapus.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Pencil, CreditCard } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

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
    {
        title: 'Detail Metode Pembayaran',
        href: '#',
    }
];

const props = defineProps({
    paymentMethod: Object
});

// Format tanggal
const formatDate = (dateString) => {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};
</script> 