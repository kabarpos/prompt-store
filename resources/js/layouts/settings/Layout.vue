<script setup lang="ts">
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { User, KeyRound, ChevronRight } from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';

const tabNavItems: NavItem[] = [
    {
        title: 'Profil',
        href: '/settings/profile',
        icon: User,
        description: 'Kelola informasi profil dan foto anda'
    },
    {
        title: 'Kata Sandi',
        href: '/settings/password',
        icon: KeyRound,
        description: 'Ubah kata sandi akun anda'
    }
];

// Gunakan ref untuk menyimpan path
const currentPath = ref('');

// Tentukan tab aktif berdasarkan URL saat ini
const activeTab = computed(() => {
    const pathParts = currentPath.value.split('/');
    return pathParts[pathParts.length - 1] || 'profile';
});

onMounted(() => {
    // Ambil path dari window.location saat component di-mount
    currentPath.value = window.location.pathname;
});
</script>

<template>
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <div class="w-full md:w-80 flex-shrink-0">
                <div class="sticky top-6">
                    <h1 class="text-2xl font-bold mb-6 text-slate-900 dark:text-white">Pengaturan</h1>
                    
                    <div class="space-y-2">
                        <Link 
                            v-for="item in tabNavItems" 
                            :key="item.href"
                            :href="item.href"
                            class="flex items-start gap-4 px-4 py-3 rounded-lg transition-colors hover:bg-slate-100 dark:hover:bg-slate-800 group"
                            :class="[
                                item.href.includes(activeTab) ? 
                                'bg-slate-100 dark:bg-slate-800 border-l-2 border-primary-500' : 
                                'border-l-2 border-transparent'
                            ]"
                        >
                            <div class="p-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700 group-hover:text-primary-500">
                                <component :is="item.icon" class="h-5 w-5" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-base text-slate-900 dark:text-white">{{ item.title }}</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ item.description }}</p>
                            </div>
                            <ChevronRight class="h-4 w-4 text-slate-400 mt-2 opacity-0 transition-opacity group-hover:opacity-100" />
                        </Link>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="flex-1 max-w-3xl">
                <Card>
                    <CardContent class="p-6 sm:p-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
