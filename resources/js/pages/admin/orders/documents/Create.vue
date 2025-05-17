<template>
  <Head title="Tambah Dokumen Order" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol kembali -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-col gap-2">
          <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Tambah Dokumen Order</h1>
        
        </div>
        <Link :href="getBackUrl()" class="cursor-pointer">
          <Button variant="outline" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
            <ArrowLeft class="h-4 w-4" />
            Kembali
          </Button>
        </Link>
      </div>
      
      <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <div>
            <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Form Dokumen Order Baru</h2>
            <p class="text-secondary-600 dark:text-secondary-400 mt-1">Tambahkan dokumen baru yang berkaitan dengan order</p>
          </div>
        </div>
        
        <div class="p-6">
          <form @submit.prevent="submit">
            <div class="grid gap-6">
              <!-- Order Terkait -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <div>
                  <Label for="order_id" class="text-sm font-medium">Order Terkait <span class="text-red-500">*</span></Label>
                </div>
                <div class="md:col-span-3">
                  <div id="combobox-container" class="relative w-full">
                    <Combobox
                      v-model="form.order_id"
                      :options="availableOrdersFormatted"
                      placeholder="Cari dan pilih order..."
                    />
                  </div>
                  <InputError :message="form.errors.order_id" class="mt-1" />
                </div>
              </div>
              
              <!-- Tipe Dokumen -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <div>
                  <Label for="type" class="text-sm font-medium">Tipe Dokumen <span class="text-red-500">*</span></Label>
                </div>
                <div class="md:col-span-3">
                  <div class="relative">
                    <div 
                      class="custom-select-container" 
                      :class="{ 'active': isDocumentTypeSelectOpen }"
                    >
                      <div 
                        @click="toggleDocumentTypeSelect" 
                        class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                      >
                        <span>
                          <Badge v-if="form.type" :variant="getDocumentTypeBadgeVariant(form.type)">
                            {{ selectedDocumentTypeLabel }}
                          </Badge>
                          <span v-else>{{ selectedDocumentTypeLabel }}</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isDocumentTypeSelectOpen }">
                          <path d="m6 9 6 6 6-6"></path>
                        </svg>
                      </div>
                      
                      <div 
                        v-if="isDocumentTypeSelectOpen" 
                        class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                      >
                        <div 
                          v-for="(label, type) in documentTypes" 
                          :key="type"
                          @click="selectDocumentType(type)"
                          class="custom-select-option py-2 px-3 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                          :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.type === type }"
                        >
                          <Badge :variant="getDocumentTypeBadgeVariant(type)">{{ label }}</Badge>
                        </div>
                      </div>
                    </div>
                  </div>
                  <InputError :message="form.errors.type" class="mt-1" />
                </div>
              </div>
              
              <!-- Judul Dokumen -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <div>
                  <Label for="title" class="text-sm font-medium">Judul Dokumen <span class="text-red-500">*</span></Label>
                </div>
                <div class="md:col-span-3">
                  <Input v-model="form.title" id="title" name="title" placeholder="Masukkan judul dokumen" required />
                  <InputError :message="form.errors.title" class="mt-1" />
                </div>
              </div>
              
              <!-- Isi Dokumen -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <div>
                  <Label for="content" class="text-sm font-medium">Isi Dokumen <span class="text-red-500">*</span></Label>
                </div>
                <div class="md:col-span-3">
                  <Textarea v-model="form.content" id="content" name="content" placeholder="Masukkan isi dokumen" rows="5" required />
                  <p class="text-xs text-slate-500 mt-1">
                    {{ getDocumentInstructions() }}
                  </p>
                  <InputError :message="form.errors.content" class="mt-1" />
                </div>
              </div>
              
              <!-- Tanggal Kedaluwarsa -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <div>
                  <Label for="expires_at" class="text-sm font-medium">Tanggal Kedaluwarsa</Label>
                </div>
                <div class="md:col-span-3">
                  <Input v-model="form.expires_at" id="expires_at" name="expires_at" type="date" />
                  <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika dokumen tidak memiliki tanggal kedaluwarsa</p>
                  <InputError :message="form.errors.expires_at" class="mt-1" />
                </div>
              </div>
              
              <!-- Unggah File -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <div>
                  <Label for="file" class="text-sm font-medium">Unggah File</Label>
                </div>
                <div class="md:col-span-3">
                  <Input type="file" id="file" name="file" @input="form.file = $event.target.files[0]" />
                  <p class="text-xs text-slate-500 mt-1">Format yang didukung: PDF, docx, xlsx, zip (Maks. 10MB)</p>
                  <InputError :message="form.errors.file" class="mt-1" />
                </div>
              </div>
              
              <!-- Kirim Notifikasi -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <div>
                  <Label for="should_notify" class="text-sm font-medium">Kirim Notifikasi</Label>
                </div>
                <div class="md:col-span-3 flex items-center space-x-2">
                  <Checkbox v-model="form.should_notify" id="should_notify" />
                  <Label for="should_notify">Kirim notifikasi ke pelanggan</Label>
                </div>
              </div>
              
              <!-- Tombol Submit dan Cancel -->
              <div class="flex justify-end space-x-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                <Button 
                  type="button" 
                  variant="outline" 
                  @click="router.visit(getBackUrl())"
                >
                  Batal
                </Button>
                <Button type="submit" :disabled="form.processing">
                  <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                  Simpan Dokumen
                </Button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import Combobox from '@/components/ui/combobox/Combobox.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Loader2 } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

// Definisi props dari controller
const props = defineProps<{
  availableOrders?: Array<{
    id: number;
    order_number: string;
    customer_name: string;
  }>;
  orderParam?: string;
  order?: {
    id: number;
    order_number: string;
    customer_name: string;
  };
}>();

// Tipe dokumen
const documentTypes = {
  credential: 'Kredensial Login',
  domain: 'Informasi Domain',
  update: 'Pembaruan',
  download: 'File Unduhan',
  other: 'Lainnya'
};

// Form untuk menambah dokumen baru
const form = useForm({
  order_id: props.order?.id ? String(props.order.id) : (props.orderParam !== 'new' && props.orderParam ? props.orderParam : ''),
  type: 'credential',
  title: '',
  content: '',
  expires_at: '',
  file: null as File | null,
  should_notify: true,
});

// Available orders untuk dropdown
const availableOrders = ref(props.availableOrders || []);

// Available orders untuk dropdown - diformat untuk Combobox
const availableOrdersFormatted = computed(() => {
  return availableOrders.value.map(order => ({
    label: `${order.order_number} - ${order.customer_name}`,
    value: order.id.toString()
  }));
});

// Breadcrumbs untuk navigasi
const breadcrumbs = computed(() => {
  if (props.orderParam === 'new') {
    return [
      {
        title: 'Admin',
        href: route('admin.dashboard'),
      },
      {
        title: 'Dokumen Order',
        href: route('admin.documents.all'),
      },
      {
        title: 'Tambah Dokumen',
        href: route('admin.orders.documents.create', 'new'),
      },
    ];
  } else if (props.order) {
    return [
      {
        title: 'Admin',
        href: route('admin.dashboard'),
      },
      {
        title: 'Pesanan',
        href: route('admin.orders.index'),
      },
      {
        title: `#${props.order.order_number}`,
        href: route('admin.orders.show', props.order.id),
      },
      {
        title: 'Dokumen',
        href: route('admin.orders.documents.index', props.order.id),
      },
      {
        title: 'Tambah',
        href: route('admin.orders.documents.create', props.order.id),
      },
    ];
  } else {
    return [
      {
        title: 'Admin',
        href: route('admin.dashboard'),
      },
      {
        title: 'Dokumen Order',
        href: route('admin.documents.all'),
      },
      {
        title: 'Tambah Dokumen',
        href: route('admin.orders.documents.create', 'new'),
      },
    ];
  }
});

// Dapatkan instruksi berdasarkan tipe dokumen
const getDocumentInstructions = () => {
  if (!form.type) return 'Silakan pilih jenis dokumen terlebih dahulu';

  switch (form.type) {
    case 'credential':
      return 'Contoh format: Username: user123\nPassword: pass123\nURL: https://example.com/admin';
    case 'domain':
      return 'Contoh format: Domain: example.com\nServer: ns1.example.com, ns2.example.com\nTanggal Registrasi: 25 April 2023';
    case 'update':
      return 'Masukkan informasi pembaruan atau perubahan yang ingin disampaikan ke pelanggan';
    case 'download':
      return 'Masukkan instruksi unduhan atau link unduhan yang dapat diakses oleh pelanggan';
    default:
      return 'Masukkan isi dokumen dengan format yang sesuai';
  }
};

// Fungsi untuk mendapatkan URL kembali yang tepat
const getBackUrl = () => {
  if (props.orderParam === 'new') {
    return route('admin.documents.all');
  } else if (props.order) {
    return route('admin.orders.documents.index', props.order.id);
  } else {
    return route('admin.documents.all');
  }
};

// Submit form untuk membuat dokumen baru
const submit = () => {
  form.post(route('admin.orders.documents.store', form.order_id), {
    onSuccess: () => {
      const { toast } = useToast();
      toast({
        title: 'Dokumen berhasil dibuat',
        description: 'Dokumen baru telah berhasil ditambahkan',
      });
      router.visit(getBackUrl());
    },
  });
};

// Ambil daftar order yang tersedia untuk dropdown jika belum ada
onMounted(() => {
  if (!props.availableOrders || props.availableOrders.length === 0) {
    router.get(route('admin.orders.index'), {
      only: ['available_orders']
    }, {
      preserveState: true,
      onSuccess: (page: any) => {
        if (page.props.available_orders) {
          availableOrders.value = page.props.available_orders;
        }
      }
    });
  }
});

// State untuk custom select dropdown - Tipe Dokumen
const isDocumentTypeSelectOpen = ref(false);
const documentTypeSelectRef = ref<HTMLElement | null>(null);

// Fix linter errors with unique keys for document types
const documentTypeKeys = {
  credential: 'credential',
  domain: 'domain',
  update: 'update',
  download: 'download',
  other: 'other'
};

// Computed property untuk label tipe dokumen terpilih
const selectedDocumentTypeLabel = computed(() => {
  if (!form.type) return 'Pilih Tipe Dokumen';
  return documentTypes[form.type as keyof typeof documentTypes] || 'Pilih Tipe Dokumen';
});

// Dapatkan variant badge berdasarkan tipe dokumen
const getDocumentTypeBadgeVariant = (type: string) => {
  // Langsung menggunakan tipe dokumen sebagai variant badge
  return type || 'default';
};

// Toggle dropdown tipe dokumen
const toggleDocumentTypeSelect = () => {
  isDocumentTypeSelectOpen.value = !isDocumentTypeSelectOpen.value;
};

// Pilih tipe dokumen
const selectDocumentType = (type: string) => {
  form.type = type;
  isDocumentTypeSelectOpen.value = false;
};

// Handle click outside untuk document type select
const handleClickOutside = (event: MouseEvent) => {
  const selectContainer = document.querySelector('.custom-select-container');
  if (selectContainer && !selectContainer.contains(event.target as Node)) {
    isDocumentTypeSelectOpen.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  documentTypeSelectRef.value = document.querySelector('.custom-select-container');
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
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