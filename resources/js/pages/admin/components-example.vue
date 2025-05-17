<template>
  <Head title="Contoh Komponen" />

  <AppLayout>
    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
      <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Contoh Komponen Button</h1>
      
      <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
        <h2 class="text-xl font-semibold mb-6">Variant Action Buttons</h2>
        
        <div class="flex flex-wrap gap-4 mb-8">
          <Button variant="action">
            Action Primary
          </Button>
          
          <Button variant="action-secondary">
            Action Secondary
          </Button>
          
          <Button variant="action-danger">
            Action Danger
          </Button>
          
          <Button variant="action-success">
            Action Success
          </Button>
          
          <Button variant="action-warning">
            Action Warning
          </Button>
          
          <Button variant="action-outline">
            Action Outline
          </Button>
        </div>
        
        <h2 class="text-xl font-semibold mb-6 mt-12">Ukuran Button</h2>
        
        <div class="flex flex-wrap items-center gap-4 mb-8">
          <Button variant="action" size="sm">
            Small
          </Button>
          
          <Button variant="action">
            Default
          </Button>
          
          <Button variant="action" size="lg">
            Large
          </Button>
          
          <Button variant="action" size="icon">
            <PlusIcon class="h-4 w-4" />
          </Button>
        </div>
        
        <h2 class="text-xl font-semibold mb-6 mt-12">Variant Legacy Buttons</h2>
        
        <div class="flex flex-wrap gap-4 mb-8">
          <Button variant="default">
            Default
          </Button>
          
          <Button variant="destructive">
            Destructive
          </Button>
          
          <Button variant="outline">
            Outline
          </Button>
          
          <Button variant="secondary">
            Secondary
          </Button>
          
          <Button variant="ghost">
            Ghost
          </Button>
          
          <Button variant="link">
            Link
          </Button>
        </div>
        
        <h2 class="text-xl font-semibold mb-6 mt-12">Variant Color</h2>
        
        <div class="flex flex-wrap gap-4 mb-8">
          <Button variant="success">
            Success
          </Button>
          
          <Button variant="warning">
            Warning
          </Button>
        </div>
        
        <h2 class="text-xl font-semibold mb-6 mt-12">Status Button</h2>
        
        <div class="flex flex-wrap gap-4 mb-8">
          <Button variant="action" disabled>
            Disabled Button
          </Button>
          
          <Button variant="action" loading>
            Loading Button
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { PlusIcon } from 'lucide-vue-next';
</script> 