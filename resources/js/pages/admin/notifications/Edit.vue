<template>
    <Head title="Edit Template WhatsApp" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Edit Template WhatsApp</h1>
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
                            
                            <!-- Template Type (readonly karena tidak dapat diubah) -->
                            <div>
                                <Label for="type" class="mb-1.5 block">Tipe Template</Label>
                                <Input 
                                    id="type"
                                    v-model="templateType"
                                    readonly
                                    disabled
                                    class="bg-slate-50 dark:bg-slate-800/60"
                                />
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Tipe template tidak dapat diubah
                                </p>
                            </div>
                            
                            <!-- Trigger Status (readonly karena tidak dapat diubah) -->
                            <div>
                                <Label for="trigger_status" class="mb-1.5 block">Status Pemicu</Label>
                                <Input 
                                    id="trigger_status"
                                    v-model="triggerStatusName"
                                    readonly
                                    disabled
                                    class="bg-slate-50 dark:bg-slate-800/60"
                                />
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Status pemicu tidak dapat diubah
                                </p>
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
                                Simpan Perubahan
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeftIcon, LoaderIcon } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { 
    Select,
    SelectContent,
    SelectItem, 
    SelectTrigger, 
    SelectValue 
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    template: Object,
    availableVariables: Object,
    triggerStatusOptions: Object,
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
        title: 'Edit Template',
        href: route('admin.notifications.edit', props.template.id),
    },
];

// Map template type to readable label
const templateType = computed(() => {
    const types = {
        'customer': 'Template Pelanggan',
        'admin': 'Template Admin'
    };
    return types[props.template.type] || props.template.type;
});

// Get trigger status label
const triggerStatusName = computed(() => {
    return props.triggerStatusOptions[props.template.trigger_status] || props.template.trigger_status;
});

// Form data
const form = useForm({
    name: props.template.name,
    message_template: props.template.message_template,
    description: props.template.description || '',
    is_active: props.template.is_active,
    order: props.template.order || 0,
});

const processing = ref(false);

// Submit form
const submitForm = () => {
    processing.value = true;
    
    form.put(route('admin.notifications.whatsapp-templates.update', props.template.id), {
        onSuccess: () => {
            toast.success('Template berhasil diperbarui');
        },
        onError: () => {
            toast.error('Gagal memperbarui template');
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};
</script> 