<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <PageHeader
        title="Edit Kupon"
        description="Perbarui informasi kupon diskon"
      />

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="lg:col-span-2 space-y-6">
          <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 space-y-6">
              <form @submit.prevent="submitForm">
                <!-- Info Dasar -->
                <div class="space-y-4">
                  <h3 class="text-lg font-medium">Informasi Kupon</h3>
                  
                  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                      <Label for="code">Kode Kupon <span class="text-red-500">*</span></Label>
                      <Input 
                        id="code" 
                        v-model="form.code" 
                        placeholder="DISKON20"
                        class="uppercase"
                        @blur="form.code = form.code.toUpperCase()"
                      />
                      <InputError :message="errors.code" />
                    </div>
                    
                    <div class="md:col-span-2">
                      <Label for="description">Deskripsi</Label>
                      <Textarea 
                        id="description" 
                        v-model="form.description" 
                        placeholder="Deskripsi singkat tentang kupon ini"
                        rows="3"
                      />
                      <InputError :message="errors.description" />
                    </div>
                  </div>
                </div>
                
                <Separator class="my-6" />
                
                <!-- Nilai Diskon -->
                <div class="space-y-4">
                  <h3 class="text-lg font-medium">Nilai Diskon</h3>
                  
                  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                      <div class="relative">
                        <div v-if="form.type === 'fixed'" class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                          <span class="text-gray-500">Rp</span>
                        </div>
                        <div v-else class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                          <span class="text-gray-500">%</span>
                        </div>
                        <Input 
                          id="value" 
                          v-model="form.value" 
                          type="number" 
                          :class="{ 'pl-10': form.type === 'fixed', 'pr-8': form.type === 'percentage' }"
                          :min="0"
                          :max="form.type === 'percentage' ? 100 : null"
                          step="0.01"
                        />
                      </div>
                      <InputError :message="errors.value" />
                      <p v-if="form.type === 'percentage' && Number(form.value) > 100" class="text-xs text-red-500 mt-1">
                        Persentase diskon tidak boleh lebih dari 100%
                      </p>
                    </div>
                    
                    <div>
                      <Label for="min_purchase">Minimal Pembelian <span class="text-red-500">*</span></Label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                          <span class="text-gray-500">Rp</span>
                        </div>
                        <Input 
                          id="min_purchase" 
                          v-model="form.min_purchase" 
                          type="number" 
                          class="pl-10"
                          min="0"
                          step="1000"
                        />
                      </div>
                      <InputError :message="errors.min_purchase" />
                    </div>
                    
                    <div>
                      <Label for="max_discount">Maksimum Diskon</Label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                          <span class="text-gray-500">Rp</span>
                        </div>
                        <Input 
                          id="max_discount" 
                          v-model="form.max_discount" 
                          type="number" 
                          class="pl-10"
                          min="0"
                          step="1000"
                        />
                      </div>
                      <InputError :message="errors.max_discount" />
                      <p class="text-xs text-muted-foreground mt-1">
                        Kosongkan jika tidak ada batas maksimum diskon
                      </p>
                    </div>
                  </div>
                </div>
                
                <Separator class="my-6" />
                
                <!-- Batasan Penggunaan -->
                <div class="space-y-4">
                  <h3 class="text-lg font-medium">Batasan Penggunaan</h3>
                  
                  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                      <Label for="max_uses">Maksimum Penggunaan</Label>
                      <div class="flex items-center gap-2">
                        <Input 
                          id="max_uses" 
                          v-model="form.max_uses" 
                          type="number" 
                          min="0"
                          step="1"
                          class="flex-1"
                        />
                        <p class="text-sm text-muted-foreground whitespace-nowrap">(0 = tidak terbatas)</p>
                      </div>
                      <InputError :message="errors.max_uses" />
                    </div>
                    
                    <div>
                      <div class="flex flex-col">
                        <div class="flex items-center space-x-2">
                          <Checkbox 
                            id="is_active" 
                            :checked="form.is_active" 
                            @update:checked="form.is_active = $event"
                          />
                          <Label for="is_active" class="cursor-pointer">
                            Aktifkan Kupon
                          </Label>
                        </div>
                        <p class="text-xs text-blue-500 mt-1">
                          Status kupon: {{ form.is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </p>
                        <InputError :message="errors.is_active" />
                      </div>
                      
                      <div class="mt-2 text-sm text-muted-foreground">
                        <p>Digunakan: {{ coupon.used_count }} kali</p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <Separator class="my-6" />
                
                <!-- Periode Kupon -->
                <div class="space-y-4">
                  <h3 class="text-lg font-medium">Periode Kupon</h3>
                  
                  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                      <Label for="starts_at">Berlaku Dari</Label>
                      <Input 
                        id="starts_at" 
                        v-model="form.starts_at" 
                        type="datetime-local"
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
                      />
                      <InputError :message="errors.expires_at" />
                    </div>
                  </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-3">
                  <Button variant="outline" type="button" @click="cancel">
                    Batal
                  </Button>
                  <Button type="submit" :disabled="processing">
                    <Loader2Icon v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
                    Simpan Perubahan
                  </Button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/ui/page-header/PageHeader.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Separator } from '@/components/ui/separator';
import { Loader2Icon } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { toast } from '@/lib/toast';

interface Coupon {
  id: number;
  code: string;
  description: string | null;
  type: 'fixed' | 'percentage';
  value: number;
  min_purchase: number;
  max_discount: number | null;
  max_uses: number;
  used_count: number;
  is_active: boolean;
  starts_at: string | null;
  expires_at: string | null;
}

interface Props {
  coupon: Coupon;
}

const props = defineProps<Props>();

const breadcrumbs = [
  { label: 'Dashboard', href: route('admin.dashboard') },
  { label: 'Manajemen Kupon', href: route('admin.coupons.index') },
  { label: 'Edit Kupon', href: route('admin.coupons.edit', props.coupon.id) },
];

// State untuk custom select dropdown
const isSelectOpen = ref(false);
const selectRef = ref(null);

// Opsi tipe diskon
const typeOptions = [
  { value: 'fixed', label: 'Nominal Tetap (Rp)' },
  { value: 'percentage', label: 'Persentase (%)' }
];

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
const selectType = (value: 'fixed' | 'percentage') => {
  form.type = value;
  isSelectOpen.value = false;
};

// Handle click outside untuk menutup dropdown
const handleClickOutside = (event: MouseEvent) => {
  if (selectRef.value && selectRef.value instanceof Element && !selectRef.value.contains(event.target as Node)) {
    isSelectOpen.value = false;
  }
};

// Lifecycle hooks untuk select
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  selectRef.value = document.querySelector('.custom-select-container');
  
  // Log initial values for debugging
  console.log('Initial coupon data:', props.coupon);
  console.log('Initial is_active value:', form.is_active);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Format tanggal untuk input datetime-local
const formatDatetimeLocal = (date: string | null) => {
  if (!date) return '';
  
  // Format: YYYY-MM-DDThh:mm
  return new Date(date).toISOString().slice(0, 16);
};

const form = useForm({
  code: props.coupon.code,
  description: props.coupon.description || '',
  type: props.coupon.type,
  value: props.coupon.value,
  min_purchase: props.coupon.min_purchase,
  max_discount: props.coupon.max_discount || undefined,
  max_uses: props.coupon.max_uses,
  is_active: props.coupon.is_active,
  starts_at: formatDatetimeLocal(props.coupon.starts_at),
  expires_at: formatDatetimeLocal(props.coupon.expires_at),
});

const processing = ref(false);
const errors = ref<Record<string, string>>({});

const submitForm = () => {
  // Validasi tambahan untuk persentase
  if (form.type === 'percentage' && Number(form.value) > 100) {
    errors.value.value = 'Persentase diskon tidak boleh lebih dari 100%';
    return;
  }
  
  // Log form data sebelum submit untuk debugging
  console.log('Data kupon yang akan disimpan:', {
    ...form.data(),
    is_active: form.is_active
  });
  
  // Memastikan is_active adalah boolean
  form.is_active = Boolean(form.is_active);
  
  processing.value = true;
  errors.value = {};
  
  form.put(route('admin.coupons.update', props.coupon.id), {
    preserveScroll: true,
    onSuccess: (page) => {
      toast.success('Kupon berhasil diperbarui');
      console.log('Respons sukses:', page);
      router.visit(route('admin.coupons.index'));
    },
    onError: (err) => {
      errors.value = err;
      processing.value = false;
      console.error('Error saat update kupon:', err);
    },
    onFinish: () => {
      processing.value = false;
    }
  });
};

const cancel = () => {
  router.visit(route('admin.coupons.index'));
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