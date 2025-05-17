<template>
  <Head title="Tambah Kategori Pengeluaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol kembali -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Tambah Kategori Pengeluaran</h1>
        <Link :href="route('admin.expense-categories.index')" class="cursor-pointer">
          <Button variant="outline" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
            <ArrowLeft class="h-4 w-4" />
            Kembali
          </Button>
        </Link>
      </div>
      
      <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <div>
            <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Form Kategori Pengeluaran Baru</h2>
            <p class="text-secondary-600 dark:text-secondary-400 mt-1">Tambahkan kategori baru untuk pengeluaran Anda</p>
          </div>
        </div>
        <div class="p-6">
          <form @submit.prevent="submit">
            <div class="space-y-4">
              <!-- Nama Kategori -->
              <div>
                <Label for="name">Nama <span class="text-red-500">*</span></Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full"
                  placeholder="Masukkan nama kategori"
                  required
                />
                <InputError :message="form.errors.name" class="mt-2" />
              </div>

              <!-- Deskripsi -->
              <div>
                <Label for="description">Deskripsi</Label>
                <Textarea
                  id="description"
                  v-model="form.description"
                  class="mt-1 block w-full"
                  rows="3"
                  placeholder="Deskripsi kategori (opsional)"
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>

              <!-- Warna Kategori -->
              <div>
                <Label for="color">Warna (opsional)</Label>
                <div class="flex items-center mt-1 w-[100px] border border-slate-200 dark:border-slate-700 rounded-md">
                  <Input
                    id="color"
                    v-model="form.color"
                    type="color"
                    class="w-12 h-9 p-1 cursor-pointer"
                  />
                 
                </div>
                <p class="text-xs text-secondary-600 dark:text-secondary-400 mt-1">
                  Pilih warna untuk kategori ini (format HEX, contoh: #3B82F6)
                </p>
                <InputError :message="form.errors.color" class="mt-2" />
              </div>

              <!-- Status Aktif -->
              <div class="flex items-center space-x-2">
                <Toggle v-model="form.is_active" />
                <Label for="is_active">Status Aktif</Label>
                <Badge variant="outline" :class="form.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'">
                  {{ form.is_active ? 'Aktif' : 'Tidak Aktif' }}
                </Badge>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <Button 
                type="submit" 
                variant="action"
                :disabled="form.processing"
              >
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

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Loader2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import Toggle from '@/components/ui/Toggle.vue';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem } from '@/types';

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Expense Categories',
    href: route('admin.expense-categories.index'),
  },
  {
    title: 'Tambah Kategori',
    href: route('admin.expense-categories.create'),
  }
];

// Form data
const form = useForm({
  name: '',
  description: '',
  color: '#3B82F6', // Default color (blue)
  is_active: true
});

// Submit form
const submit = () => {
  form.post(route('admin.expense-categories.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    }
  });
};
</script>

<style>
/* Custom style untuk color input */
input[type="color"] {
  -webkit-appearance: none;
  border: none;
}
input[type="color"]::-webkit-color-swatch-wrapper {
  padding: 0;
}
input[type="color"]::-webkit-color-swatch {
  border-radius: 4px;
  border: 1px solid rgba(0, 0, 0, 0.1);
}
</style> 