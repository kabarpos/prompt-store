<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { Moon, Sun } from 'lucide-vue-next';

interface Props {
    class?: string;
}

const { class: containerClass = '' } = defineProps<Props>();

const { appearance, updateAppearance } = useAppearance();

const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
] as const;
</script>

<template>
    <div :class="['inline-flex gap-1 rounded-lg bg-slate-100 p-1 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600/70', containerClass]">
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            @click="updateAppearance(value)"
            :class="[
                'flex items-center rounded-md px-3 py-1 transition-colors w-16 justify-center',
                appearance === value
                    ? 'bg-white shadow-sm dark:bg-slate-800 dark:text-slate-100 text-slate-900 font-medium'
                    : 'text-slate-600 hover:bg-slate-200/60 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-600/60 dark:hover:text-white',
            ]"
        >
            <component :is="Icon" class="h-5 w-5" />
            <span class="ml-1.5 text-xs">{{ label }}</span>
        </button>
    </div>
</template>
