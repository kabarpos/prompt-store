<template>
    <Head title="Tambah Metode Pembayaran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul dan tombol kembali -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold">Tambah Metode Pembayaran</h1>
                <Link :href="route('admin.payment-methods.index')" class="cursor-pointer">
                    <Button variant="outline" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali
                    </Button>
                </Link>
            </div>
            
            <div class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-50 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div>
                        <h2 class="text-lg font-medium">Form Metode Pembayaran Baru</h2>
                        <p class="text-slate-600 dark:text-slate-400 mt-1">Tambahkan metode pembayaran baru untuk pelanggan Anda</p>
                    </div>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Informasi Umum -->
                            <div class="space-y-4 md:col-span-1">
                                <h3 class="font-medium text-lg mb-2">Informasi Umum</h3>
                                
                                <div>
                                    <Label for="name">Nama</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Bank BCA"
                                        required
                                    />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="code">Kode</Label>
                                    <Input
                                        id="code"
                                        v-model="form.code"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="bca"
                                        required
                                    />
                                    <p class="text-xs text-slate-500 mt-1">Kode unik untuk metode pembayaran (gunakan huruf kecil dan tanpa spasi)</p>
                                    <InputError :message="form.errors.code" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="type">Tipe</Label>
                                    <div class="relative custom-select-container type-select-container" :class="{ 'active': isTypeSelectOpen }">
                                        <div 
                                            @click="toggleTypeSelect" 
                                            class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                                            :class="{ 'border-red-500': form.errors.type }"
                                        >
                                            <span>{{ selectedTypeLabel }}</span>
                                            <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isTypeSelectOpen }" />
                                        </div>
                                        
                                        <div 
                                            v-if="isTypeSelectOpen" 
                                            class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                                        >
                                            <div
                                                class="custom-select-option py-2 px-3 text-slate-500 dark:text-slate-300 italic hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                                                @click="selectType('')"
                                                :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.type === '' }"
                                            >
                                                Pilih Tipe
                                            </div>
                                            <div
                                                class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                                                @click="selectType('bank_transfer')"
                                                :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.type === 'bank_transfer' }"
                                            >
                                                Transfer Bank
                                            </div>
                                            <div
                                                class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                                                @click="selectType('payment_gateway')"
                                                :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.type === 'payment_gateway' }"
                                            >
                                                Payment Gateway
                                            </div>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.type" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="logo">Logo</Label>
                                    <Input
                                        id="logo"
                                        type="file"
                                        @input="form.logo = $event.target.files[0]"
                                        accept="image/*"
                                        class="mt-1 block w-full"
                                    />
                                    <p class="text-xs text-slate-500 mt-1">Upload logo untuk metode pembayaran (opsional)</p>
                                    <InputError :message="form.errors.logo" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="description">Deskripsi</Label>
                                    <Textarea
                                        id="description"
                                        v-model="form.description"
                                        class="mt-1 block w-full"
                                        placeholder="Deskripsi metode pembayaran"
                                        rows="3"
                                    ></Textarea>
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>
                            </div>
                            
                            <!-- Bank Transfer / Payment Gateway Details -->
                            <div class="space-y-4 md:col-span-1">
                                <h3 class="font-medium text-lg mb-2">Pengaturan Spesifik</h3>
                                
                                <!-- Bank Transfer Details -->
                                <div v-if="form.type === 'bank_transfer'" class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 space-y-4">
                                    <h4 class="font-medium">Informasi Rekening Bank</h4>
                                    
                                    <div>
                                        <Label for="bank_name">Nama Bank</Label>
                                        <Input
                                            id="bank_name"
                                            v-model="form.bank_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="BCA"
                                        />
                                        <InputError :message="form.errors.bank_name" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <Label for="account_number">Nomor Rekening</Label>
                                        <Input
                                            id="account_number"
                                            v-model="form.account_number"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="1234567890"
                                        />
                                        <InputError :message="form.errors.account_number" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <Label for="account_name">Nama Pemilik Rekening</Label>
                                        <Input
                                            id="account_name"
                                            v-model="form.account_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="PT. Dilogif Indonesia"
                                        />
                                        <InputError :message="form.errors.account_name" class="mt-2" />
                                    </div>
                                </div>
                                
                                <!-- Payment Gateway Details -->
                                <div v-else-if="form.type === 'payment_gateway'" class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 space-y-4">
                                    <h4 class="font-medium">Informasi Payment Gateway</h4>
                                    <p class="text-sm text-slate-500">
                                        Konfigurasi payment gateway dapat ditambahkan di pengaturan integrasi.
                                    </p>
                                </div>
                                
                                <!-- Pengaturan Umum -->
                                <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 space-y-4 mt-4">
                                    <h4 class="font-medium">Pengaturan Umum</h4>
                                    
                                    <div>
                                        <Label for="sort_order">Urutan Tampilan</Label>
                                        <Input
                                            id="sort_order"
                                            v-model="form.sort_order"
                                            type="number"
                                            min="0"
                                            class="mt-1 block w-full"
                                            placeholder="0"
                                        />
                                        <p class="text-xs text-slate-500 mt-1">Urutan tampilan pada halaman checkout (angka kecil ditampilkan lebih dulu)</p>
                                        <InputError :message="form.errors.sort_order" class="mt-2" />
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <Toggle v-model="form.is_active" label="Status Aktif" />
                                        <Label for="is_active">Status Aktif</Label>
                                        <Badge variant="outline" :class="form.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'">
                                            {{ form.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <Button type="submit" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                Simpan Metode Pembayaran
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Loader2, ChevronDown as ChevronDownIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import Toggle from '@/components/ui/Toggle.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { ref } from 'vue';

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
        title: 'Tambah Metode Pembayaran',
        href: '#',
    }
];

const form = useForm({
    name: '',
    code: '',
    type: '',
    bank_name: '',
    account_number: '',
    account_name: '',
    description: '',
    logo: null,
    sort_order: 0,
    is_active: true
});

// Custom select logic untuk tipe
const isTypeSelectOpen = ref(false);
const selectedTypeLabel = ref('Pilih Tipe');

const toggleTypeSelect = () => {
    isTypeSelectOpen.value = !isTypeSelectOpen.value;
};

const selectType = (type) => {
    form.type = type;
    isTypeSelectOpen.value = false;
    
    if (type === '') {
        selectedTypeLabel.value = 'Pilih Tipe';
    } else if (type === 'bank_transfer') {
        selectedTypeLabel.value = 'Transfer Bank';
    } else if (type === 'payment_gateway') {
        selectedTypeLabel.value = 'Payment Gateway';
    }
};

const submit = () => {
    form.post(route('admin.payment-methods.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        }
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