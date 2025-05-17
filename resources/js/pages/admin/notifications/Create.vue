<template>
    <Head title="Tambah Template WhatsApp" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Tambah Template WhatsApp</h1>
                <div class="flex gap-2">
                    <Link :href="route('admin.notifications.index')">
                        <Button variant="outline" class="flex items-center gap-1.5">
                            <ArrowLeftIcon class="h-4 w-4" />
                            Kembali
                        </Button>
                    </Link>
                </div>
            </div>
            
            <!-- Form Card -->
            <Card class="border border-slate-200 dark:border-slate-700">
                <CardContent class="pt-6">
                    <form @submit.prevent="submitForm">
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Template Name -->
                            <div>
                                <Label for="name" class="mb-1.5 block">Nama Template</Label>
                                <Input 
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Masukkan nama template"
                                    required
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            
                            <!-- Template Type -->
                            <div>
                                <Label for="type" class="mb-1.5 block">Tipe Template</Label>
                                <div class="relative">
                                    <div 
                                        class="custom-select-container" 
                                        :class="{ 'active': isTypeSelectOpen }"
                                    >
                                        <div 
                                            @click="toggleTypeSelect" 
                                            class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                                        >
                                            <span>{{ selectedTypeLabel }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isTypeSelectOpen }">
                                                <path d="m6 9 6 6 6-6"></path>
                                            </svg>
                                        </div>
                                        
                                        <div 
                                            v-if="isTypeSelectOpen" 
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
                                <InputError :message="form.errors.type" />
                            </div>
                            
                            <!-- Trigger Status -->
                            <div>
                                <Label for="trigger_status" class="mb-1.5 block">Status Pemicu</Label>
                                <div class="relative">
                                    <div 
                                        class="custom-select-container" 
                                        :class="{ 'active': isTriggerSelectOpen }"
                                    >
                                        <div 
                                            @click="toggleTriggerSelect" 
                                            class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                                        >
                                            <span>{{ selectedTriggerLabel }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isTriggerSelectOpen }">
                                                <path d="m6 9 6 6 6-6"></path>
                                            </svg>
                                        </div>
                                        
                                        <div 
                                            v-if="isTriggerSelectOpen" 
                                            class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                                        >
                                            <div 
                                                v-for="option in triggerOptions" 
                                                :key="option.value"
                                                @click="selectTrigger(option.value)"
                                                class="custom-select-option py-2 px-3 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                                                :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.trigger_status === option.value }"
                                            >
                                                {{ option.label }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.trigger_status" />
                            </div>
                            
                            <!-- Order -->
                            <div>
                                <Label for="order" class="mb-1.5 block">Urutan</Label>
                                <Input 
                                    id="order"
                                    v-model="form.order"
                                    type="number"
                                    min="0"
                                    placeholder="Urutan tampilan"
                                />
                                <InputError :message="form.errors.order" />
                            </div>
                        </div>
                        
                        <!-- Message Template -->
                        <div class="mb-6">
                            <Label for="message_template" class="mb-1.5 block">Template Pesan</Label>
                            <Textarea 
                                id="message_template"
                                v-model="form.message_template"
                                rows="10"
                                placeholder="Template pesan dengan variabel"
                                required
                            />
                            <InputError :message="form.errors.message_template" />
                            
                            <!-- Variabel yang tersedia -->
                            <div class="mt-3">
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Variabel yang tersedia:</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    <div 
                                        v-for="(description, variable) in availableVariables" 
                                        :key="variable" 
                                        class="text-xs bg-slate-100 dark:bg-slate-700 p-2 rounded flex items-center justify-between"
                                    >
                                        <code class="text-primary-500 dark:text-primary-400">{{ variable }}</code>
                                        <span class="text-slate-500 dark:text-slate-400">{{ description }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-6">
                            <Label for="description" class="mb-1.5 block">Deskripsi (Opsional)</Label>
                            <Textarea 
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Deskripsi penggunaan template"
                            />
                            <InputError :message="form.errors.description" />
                        </div>
                        
                        <!-- Active Status -->
                        <div class="mb-6">
                            <div class="flex items-center space-x-2">
                                <Checkbox id="is_active" v-model:checked="form.is_active" />
                                <Label for="is_active">Template Aktif</Label>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-2">
                            <Link :href="route('admin.notifications.index')">
                                <Button type="button" variant="outline">Batal</Button>
                            </Link>
                            <Button type="submit" variant="action" :disabled="processing">
                                <LoaderIcon v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
                                Simpan Template
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeftIcon, LoaderIcon } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    availableVariables: Object,
    triggerStatusOptions: Object,
});

// Format options for select input
const formatSelectOptions = (optionsObject) => {
    return Object.entries(optionsObject).map(([value, label]) => ({
        value, 
        label
    }));
};

const triggerOptions = computed(() => formatSelectOptions(props.triggerStatusOptions));

const typeOptions = [
    { value: 'customer', label: 'Template Pelanggan' },
    { value: 'admin', label: 'Template Admin' },
];

// State untuk custom select dropdowns
const isTypeSelectOpen = ref(false);
const isTriggerSelectOpen = ref(false);
const typeSelectRef = ref(null);
const triggerSelectRef = ref(null);

// Computed properties untuk label terpilih
const selectedTypeLabel = computed(() => {
    const option = typeOptions.find(opt => opt.value === form.type);
    return option ? option.label : 'Pilih tipe template...';
});

const selectedTriggerLabel = computed(() => {
    const option = triggerOptions.value.find(opt => opt.value === form.trigger_status);
    return option ? option.label : 'Pilih status pemicu...';
});

// Toggle dropdowns
const toggleTypeSelect = () => {
    isTypeSelectOpen.value = !isTypeSelectOpen.value;
    isTriggerSelectOpen.value = false;
};

const toggleTriggerSelect = () => {
    isTriggerSelectOpen.value = !isTriggerSelectOpen.value;
    isTypeSelectOpen.value = false;
};

// Select handlers
const selectType = (value) => {
    form.type = value;
    isTypeSelectOpen.value = false;
};

const selectTrigger = (value) => {
    form.trigger_status = value;
    isTriggerSelectOpen.value = false;
};

// Handle click outside
const handleClickOutside = (event) => {
    if (typeSelectRef.value && !typeSelectRef.value.contains(event.target)) {
        isTypeSelectOpen.value = false;
    }
    if (triggerSelectRef.value && !triggerSelectRef.value.contains(event.target)) {
        isTriggerSelectOpen.value = false;
    }
};

// Lifecycle hooks untuk select
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    typeSelectRef.value = document.querySelector('.custom-select-container');
    triggerSelectRef.value = document.querySelector('.custom-select-container:nth-child(2)');
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Breadcrumbs
const breadcrumbs = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Template WhatsApp',
        href: route('admin.notifications.index'),
    },
    {
        title: 'Tambah Template',
        href: route('admin.notifications.create'),
    },
];

// Form data
const form = useForm({
    name: '',
    type: 'customer',
    trigger_status: '',
    message_template: '',
    description: '',
    is_active: true,
    order: 0,
});

const processing = ref(false);

// Submit form
const submitForm = () => {
    processing.value = true;
    
    form.post(route('admin.notifications.whatsapp-templates.store'), {
        onSuccess: () => {
            toast.success('Template berhasil dibuat');
        },
        onError: () => {
            toast.error('Gagal membuat template');
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