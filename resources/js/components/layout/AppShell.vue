<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import { SharedData } from '@/types';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage<SharedData>().props.sidebarOpen;
</script>

<template>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col bg-white dark:bg-slate-800">
        <slot />
    </div>
    <SidebarProvider v-else :default-open="isOpen" class="bg-white dark:bg-slate-800" >
        <slot />
    </SidebarProvider>
</template>
