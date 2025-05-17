<script setup lang="ts">
import { defineComponent as _defineComponent } from "vue";
import Breadcrumbs from "@/components/ui/Breadcrumbs.vue";
import { SidebarTrigger } from "@/components/ui/sidebar";
import type { BreadcrumbItemType } from '@/types';
import AppearanceTabs from "@/components/features/appearance/AppearanceTabs.vue";

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-slate-200 dark:border-slate-600/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        
        <!-- Theme Toggle -->
        <div class="ml-auto">
            <AppearanceTabs />
        </div>
    </header>
</template>
