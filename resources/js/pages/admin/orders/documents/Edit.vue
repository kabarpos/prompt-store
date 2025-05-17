<template>
  <Head title="Edit Dokumen" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol kembali -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Edit Dokumen</h1>
        <div class="flex gap-2">
          <Link :href="route('admin.documents.all')">
            <Button variant="outline" size="sm" class="h-9">
              <ArrowLeft class="h-4 w-4 mr-2" />
              Kembali
            </Button>
          </Link>
        </div>
      </div>

      <!-- Form Edit -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-secondary-200 dark:border-slate-700">
        <div class="p-6 border-b border-secondary-200 dark:border-slate-700">
          <h2 class="text-lg font-semibold text-secondary-900 dark:text-white">Informasi Dokumen</h2>
          <p class="text-secondary-500 dark:text-secondary-400 mt-1">Edit informasi dokumen dan konten di bawah ini.</p>
        </div>
        <form @submit.prevent="updateDocument" class="p-6">
          <div class="space-y-6">
            <!-- Tipe Dokumen -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
              <Label for="type" class="text-right hidden md:block">Tipe Dokumen</Label>
              <div class="md:col-span-3 space-y-1">
                <Label for="type" class="md:hidden">Tipe Dokumen</Label>
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
                <p v-if="errors.type" class="text-destructive text-sm">{{ errors.type }}</p>
              </div>
            </div>

            <!-- Judul -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
              <Label for="title" class="text-right hidden md:block">Judul Dokumen</Label>
              <div class="md:col-span-3 space-y-1">
                <Label for="title" class="md:hidden">Judul Dokumen</Label>
                <Input id="title" v-model="form.title" />
                <p v-if="errors.title" class="text-destructive text-sm">{{ errors.title }}</p>
              </div>
            </div>

            <!-- Konten -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
              <Label for="content" class="text-right hidden md:block">Konten</Label>
              <div class="md:col-span-3 space-y-1">
                <Label for="content" class="md:hidden">Konten</Label>
                <Textarea id="content" v-model="form.content" rows="8" />
                <p v-if="errors.content" class="text-destructive text-sm">{{ errors.content }}</p>
              </div>
            </div>

            <!-- File Upload -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
              <Label for="file" class="text-right hidden md:block">File Lampiran</Label>
              <div class="md:col-span-3 space-y-1">
                <Label for="file" class="md:hidden">File Lampiran</Label>
                <Input id="file" type="file" @change="handleFileChange" />
                <p class="text-sm text-secondary-500 dark:text-secondary-400 mt-1">
                  File saat ini: 
                  <span v-if="document.file_path" class="font-medium">
                    {{ getFileName(document.file_path) }} ({{ formatFileSize(document.file_size) }})
                  </span>
                  <span v-else class="italic">Tidak ada file</span>
                </p>
                <p v-if="errors.file" class="text-destructive text-sm">{{ errors.file }}</p>
              </div>
            </div>

            <!-- Kirim Notifikasi -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
              <div class="md:col-span-1 text-right hidden md:block"></div>
              <div class="md:col-span-3 flex items-center gap-2">
                <Checkbox id="should_notify" v-model="form.should_notify" />
                <Label for="should_notify">Kirim notifikasi ke pelanggan setelah diperbarui</Label>
              </div>
            </div>

            <!-- Tombol Submit -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start pt-4 border-t border-secondary-200 dark:border-slate-700">
              <div class="md:col-span-1 text-right hidden md:block"></div>
              <div class="md:col-span-3 flex justify-start gap-2">
                <Button type="submit" disabled={isSubmitting}>
                  <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                  Simpan Perubahan
                </Button>
                <Link :href="route('admin.documents.all')">
                  <Button type="button" variant="outline">
                    Batal
                  </Button>
                </Link>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Loader2 } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';
import { Badge } from '@/components/ui/badge';

const props = defineProps({
  order: Object,
  document: Object,
  documentTypes: Object,
  errors: Object
});

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: route('admin.dashboard') },
  { title: 'Orders', href: route('admin.orders.index') },
  { title: 'Order ' + props.order.order_number, href: route('admin.orders.show', props.order.id) },
  { title: 'Dokumen', href: route('admin.orders.documents.index', props.order.id) },
  { title: 'Edit ' + props.document.title },
];

// Form state
const form = useForm({
  type: props.document.type,
  title: props.document.title,
  content: props.document.content,
  expires_at: props.document.expires_at,
  file: null,
  should_notify: false
});

const isSubmitting = ref(false);
const { toast } = useToast();

// Format file size
const formatFileSize = (bytes) => {
  if (!bytes) return '-';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Get file name from path
const getFileName = (path) => {
  if (!path) return '';
  return path.split('/').pop();
};

// Handle file change
const handleFileChange = (e) => {
  if (e.target.files.length > 0) {
    form.file = e.target.files[0];
  }
};

// Update document
const updateDocument = () => {
  isSubmitting.value = true;
  
  form.post(route('admin.orders.documents.update', [props.order.id, props.document.id]), {
    _method: 'PUT',
    forceFormData: true,
    onSuccess: () => {
      router.visit(route('admin.orders.documents.show', [props.order.id, props.document.id]));
      toast({
        title: 'Dokumen diperbarui',
        description: 'Dokumen berhasil diperbarui',
      });
    },
    onError: (errors) => {
      toast({
        title: 'Gagal memperbarui dokumen',
        description: Object.values(errors).flat().join(' '),
        variant: 'destructive',
      });
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

// Document type selection logic
const isDocumentTypeSelectOpen = ref(false);
const documentTypeSelectRef = ref(null);

// Computed property untuk label tipe dokumen terpilih
const selectedDocumentTypeLabel = computed(() => {
  if (!form.type) return 'Pilih Tipe Dokumen';
  return props.documentTypes[form.type] || 'Pilih Tipe Dokumen';
});

const toggleDocumentTypeSelect = () => {
  isDocumentTypeSelectOpen.value = !isDocumentTypeSelectOpen.value;
};

const selectDocumentType = (type) => {
  form.type = type;
  isDocumentTypeSelectOpen.value = false;
};

const getDocumentTypeBadgeVariant = (type) => {
  switch (type) {
    case 'credential': return 'credential';
    case 'domain': return 'domain';
    case 'update': return 'orange';
    case 'download': return 'download';
    case 'other': return 'other';
    default: return 'default';
  }
};

// Handle click outside untuk document type select
const handleClickOutside = (event) => {
  const selectContainer = document.querySelector('.custom-select-container');
  if (selectContainer && !selectContainer.contains(event.target)) {
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