<template>
    <Head :title="`Edit Kategori - ${category.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul dan tombol kembali -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold">Edit Kategori: {{ category.name }}</h1>
                <Link :href="route('admin.categories.index')" class="cursor-pointer">
                    <Button variant="outline" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali
                    </Button>
                </Link>
            </div>
            
            <div class="bg-card text-card-foreground rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div>
                        <h2 class="text-lg font-medium">Form Edit Kategori</h2>
                        <p class="text-muted-foreground mt-1">Perbarui informasi kategori produk Anda</p>
                    </div>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <!-- Hidden method field for Laravel method spoofing -->
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="space-y-4">
                            <div>
                                <Label for="name">Nama {{ form.name }}</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="form.name"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <Label for="icon">Ikon</Label>
                                <div v-if="category.icon" class="mt-2">
                                    <img 
                                        :src="'/storage/' + category.icon" 
                                        alt="Category Icon" 
                                        class="w-16 h-16 object-cover rounded-md mb-2"
                                    />
                                </div>
                                <Input
                                    id="icon"
                                    type="file"
                                    @input="form.icon = $event.target.files[0]"
                                    accept="image/*"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.icon" class="mt-2" />
                            </div>

                            <div class="flex items-center space-x-2">
                                <Toggle v-model="form.is_active" id="is_active" />
                                <Label for="is_active">Status Aktif</Label>
                                <Badge variant="outline" :class="form.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'">
                                    {{ form.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </Badge>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <Button type="submit" :disabled="loading || form.processing">
                                <Loader2 v-if="loading || form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                Perbarui Kategori
                            </Button>
                        </div>
                    </form>
                    
                    <!-- Debug info -->
                    <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-md">
                        <p class="text-xs">Debug: Nama kategori: {{ category.name }} ({{ typeof category.name }})</p>
                        <p class="text-xs">Debug: Form name: {{ form.name }} ({{ typeof form.name }})</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Loader2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import Toggle from '@/components/ui/Toggle.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { toast } from 'vue-sonner';
import axios from 'axios';

// Breadcrumbs untuk navigasi
const breadcrumbs = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Kategori',
        href: route('admin.categories.index'),
    },
    {
        title: 'Edit Kategori',
        href: '#',
    }
];

const props = defineProps({
    category: Object
});

// Debug nilai is_active dari database
console.log('Nilai is_active dari database:', props.category.is_active, typeof props.category.is_active);

// Pastikan nilai is_active dikonversi ke boolean
const categoryIsActive = 
    props.category.is_active === true || 
    props.category.is_active === 1 || 
    props.category.is_active === '1' || 
    props.category.is_active === 'true';

const form = useForm({
    // Pastikan nama kategori tidak undefined/null
    name: props.category.name || '',
    icon: null,
    is_active: categoryIsActive
});

// Inisialisasi lagi form name pada mounted untuk memastikan
onMounted(() => {
    console.log('Form is_active pada mount:', form.is_active, typeof form.is_active);
    console.log('Category name pada mount:', props.category.name, typeof props.category.name);
    
    // Jaga-jaga, atur ulang nama
    form.name = props.category.name || '';
});

const loading = ref(false);

// Metode submit alternatif menggunakan axios jika Inertia.js tetap bermasalah
const submitAlternative = () => {
    if (!form.name || form.name.trim() === '') {
        form.name = props.category.name || 'Kategori';
    }
    
    loading.value = true;
    
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('name', form.name);
    formData.append('is_active', form.is_active ? 1 : 0);
    
    if (form.icon) {
        formData.append('icon', form.icon);
    }
    
    axios.post(route('admin.categories.update', props.category.id), formData)
        .then(response => {
            toast.success('Berhasil', {
                description: 'Kategori berhasil diperbarui',
            });
            
            // Redirect ke halaman index setelah sukses
            setTimeout(() => {
                router.visit(route('admin.categories.index'));
            }, 1500);
        })
        .catch(error => {
            console.error('Error update kategori:', error.response?.data);
            
            if (error.response?.data?.errors) {
                // Hubungkan error ke form
                Object.keys(error.response.data.errors).forEach(key => {
                    form.setError(key, error.response.data.errors[key][0]);
                });
            } else {
                toast.error('Gagal', {
                    description: 'Terjadi kesalahan saat memperbarui kategori',
                });
            }
        })
        .finally(() => {
            loading.value = false;
        });
};

// Ganti fungsi submit agar menggunakan metode alternatif
const submit = submitAlternative;
</script> 