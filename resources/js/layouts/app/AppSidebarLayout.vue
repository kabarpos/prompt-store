<script setup lang="ts">
import { defineComponent as _defineComponent } from "vue";
import AppContent from "@/components/layout/AppContent.vue";
import AppShell from "@/components/layout/AppShell.vue";
import AppSidebar from "@/components/layout/AppSidebar.vue";
import AppSidebarHeader from '@/components/layout/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
