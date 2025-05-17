<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol kembali -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Tambah Kupon Baru</h1>
        <Link :href="route('admin.coupons.index')" class="cursor-pointer">
          <Button variant="outline" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
            <ArrowLeft class="h-4 w-4" />
            Kembali
          </Button>
        </Link>
      </div>
      
      <div class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-50 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <div>
            <h2 class="text-lg font-medium">Form Kupon Baru</h2>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Tambahkan kupon diskon baru untuk diterapkan pada checkout</p>
          </div>
        </div>

        <div class="p-6">
          <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <!-- Info Dasar -->
                <div>
                  <Label for="code">Kode Kupon <span class="text-red-500">*</span></Label>
                  <Input 
                    id="code" 
                    v-model="form.code" 
                    placeholder="DISKON20"
                    class="uppercase mt-1 block w-full"
                    @blur="form.code = form.code.toUpperCase()"
                  />
                  <InputError :message="errors.code" />
                </div>
                
                <div>
                  <Label for="description">Deskripsi</Label>
                  <Textarea 
                    id="description" 
                    v-model="form.description" 
                    placeholder="Deskripsi singkat tentang kupon ini"
                    rows="3"
                    class="mt-1 block w-full"
                  />
                  <InputError :message="errors.description" />
                </div>
              
                <!-- Nilai Diskon -->
                <div>
                  <Label for="type">Tipe Diskon <span class="text-red-500">*</span></Label>
                  <div class="relative mt-1">
                    <div 
                      class="custom-select-container" 
                      :class="{ 'active': isSelectOpen }"
                    >
                      <div 
                        @click="toggleSelect" 
                        class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                      >
                        <span>{{ selectedTypeLabel }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isSelectOpen }">
                          <path d="m6 9 6 6 6-6"></path>
                        </svg>
                      </div>
                      
                      <div 
                        v-if="isSelectOpen" 
                        class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                      >
                        <div 
                          v-for="option in typeOptions" 
                          :key="option.value"
                          @click="selectType(option.value)"
                          class="custom-select-option py-2 px-3 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                          :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.type === option.value }"
                        >
                          {{ option.label }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <InputError :message="errors.type" />
                </div>
                
                <div>
                  <Label for="value">Nilai Diskon <span class="text-red-500">*</span></Label>
                  <div class="relative mt-1">
                    <div v-if="form.type === 'fixed'" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <div v-else class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <span class="text-gray-500">%</span>
                    </div>
                    <Input 
                      id="value" 
                      v-model="form.value" 
                      type="number" 
                      :class="{ 'pl-12': form.type === 'fixed', 'pr-8': form.type === 'percentage' }"
                      :min="0"
                      :max="form.type === 'percentage' ? 100 : null"
                      step="0.01"
                      class="mt-1 block w-full"
                    />
                  </div>
                  <InputError :message="errors.value" />
                  <p v-if="form.type === 'percentage' && Number(form.value) > 100" class="text-xs text-red-500 mt-1">
                    Nilai persentase tidak boleh lebih dari 100%
                  </p>
                </div>
                
                <div>
                  <Label for="min_purchase">Minimal Pembelian <span class="text-red-500">*</span></Label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <Input 
                      id="min_purchase" 
                      v-model="form.min_purchase" 
                      type="number" 
                      class="pl-12 mt-1 block w-full"
                      min="0"
                      step="1000"
                    />
                  </div>
                  <InputError :message="errors.min_purchase" />
                </div>
                
                <div>
                  <Label for="max_discount">Maksimum Diskon</Label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <Input 
                      id="max_discount" 
                      v-model="form.max_discount"
                      type="number" 
                      class="pl-12 mt-1 block w-full"
                      min="0"
                      step="1000"
                    />
                  </div>
                  <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                    Kosongkan jika tidak ada batas maksimum diskon
                  </p>
                  <InputError :message="errors.max_discount" />
                </div>
              </div>

              <div class="space-y-4">
                <!-- Batasan Penggunaan -->
                <div>
                  <Label for="max_uses">Maksimum Penggunaan</Label>
                  <div class="flex items-center gap-2 mt-1">
                    <Input 
                      id="max_uses" 
                      v-model="form.max_uses" 
                      type="number" 
                      min="0"
                      step="1"
                      class="block w-full"
                    />
                    <p class="text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">(0 = tidak terbatas)</p>
                  </div>
                  <InputError :message="errors.max_uses" />
                </div>
                
                <div class="mt-4">
                  <div class="flex items-center space-x-2">
                    <Checkbox id="is_active" v-model:checked="form.is_active" />
                    <Label for="is_active">Aktifkan Kupon</Label>
                  </div>
                  <InputError :message="errors.is_active" />
                </div>
                
                <!-- Periode Kupon -->
                <div class="mt-4">
                  <Label for="starts_at">Berlaku Dari</Label>
                  <Input 
                    id="starts_at" 
                    v-model="form.starts_at" 
                    type="datetime-local"
                    class="mt-1 block w-full"
                  />
                  <InputError :message="errors.starts_at" />
                </div>
                
                <div>
                  <Label for="expires_at">Berlaku Sampai</Label>
                  <Input 
                    id="expires_at" 
                    v-model="form.expires_at" 
                    type="datetime-local"
                    :min="form.starts_at"
                    class="mt-1 block w-full"
                  />
                  <InputError :message="errors.expires_at" />
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <Button 
                type="submit" 
                :disabled="processing"
                class="cursor-pointer"
              >
                <Loader2Icon v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
                Simpan Kupon
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Loader2Icon } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { toast } from '@/lib/toast';
import { Link } from '@inertiajs/vue3';

const breadcrumbs = [
  { label: 'Dashboard', href: route('admin.dashboard') },
  { label: 'Manajemen Kupon', href: route('admin.coupons.index') },
  { label: 'Tambah Kupon', href: route('admin.coupons.create') },
];

// State untuk custom select dropdown
const isSelectOpen = ref(false);
const selectRef = ref(null);

// Opsi tipe diskon
const typeOptions = [
  { value: 'fixed', label: 'Nominal Tetap (Rp)' },
  { value: 'percentage', label: 'Persentase (%)' }
];

// Form data
const form = useForm({
  code: '',
  description: '',
  type: 'fixed',
  value: 0,
  min_purchase: 0,
  max_discount: null,
  max_uses: 0,
  is_active: true,
  starts_at: '',
  expires_at: '',
});

// Computed property untuk label tipe diskon terpilih
const selectedTypeLabel = computed(() => {
  const selected = typeOptions.find(option => option.value === form.type);
  return selected ? selected.label : 'Pilih tipe diskon...';
});

// Toggle dropdown
const toggleSelect = () => {
  isSelectOpen.value = !isSelectOpen.value;
};

// Pilih tipe diskon
const selectType = (value: string) => {
  form.type = value;
  isSelectOpen.value = false;
};

// Handle click outside untuk menutup dropdown
const handleClickOutside = (event: MouseEvent) => {
  if (selectRef.value && !selectRef.value.contains(event.target)) {
    isSelectOpen.value = false;
  }
};

// Lifecycle hooks untuk select
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  selectRef.value = document.querySelector('.custom-select-container');
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const processing = ref(false);
const errors = ref<Record<string, string>>({});

const submitForm = () => {
  // Validasi tambahan untuk persentase
  if (form.type === 'percentage' && Number(form.value) > 100) {
    errors.value.value = 'Persentase diskon tidak boleh lebih dari 100%';
    return;
  }
  
  processing.value = true;
  errors.value = {};
  
  form.post(route('admin.coupons.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Kupon berhasil dibuat');
      router.visit(route('admin.coupons.index'));
    },
    onError: (err) => {
      errors.value = err;
      processing.value = false;
    },
    onFinish: () => {
      processing.value = false;
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