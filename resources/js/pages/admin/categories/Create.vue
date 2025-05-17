<template>
    <Head title="Tambah Kategori" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul dan tombol kembali -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold">Tambah Kategori</h1>
                <Link :href="route('admin.categories.index')" class="cursor-pointer">
                    <Button variant="outline" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali
                    </Button>
                </Link>
            </div>
            
            <div class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-50 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div>
                        <h2 class="text-lg font-medium">Form Kategori Baru</h2>
                        <p class="text-slate-600 dark:text-slate-400 mt-1">Tambahkan kategori baru untuk produk Anda</p>
                    </div>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-4">
                            <div>
                                <Label for="name">Nama</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <Label for="icon">Ikon</Label>
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
                                <Toggle v-model="form.is_active" label="Status Aktif" />
                                <Label for="is_active">Status Aktif</Label>
                                <Badge variant="outline" :class="form.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'">
                                    {{ form.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </Badge>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <Button type="submit" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                Simpan Kategori
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
import { ArrowLeft, Loader2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import Toggle from '@/components/ui/Toggle.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';

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
        title: 'Tambah Kategori',
        href: '#',
    }
];

const form = useForm({
    name: '',
    icon: null,
    is_active: true
});

const submit = () => {
    console.log('Mengirim form dengan is_active:', form.is_active, typeof form.is_active);
    
    form.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        }
    });
};
</script> 