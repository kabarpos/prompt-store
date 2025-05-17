<template>
    <div v-if="links.length > 3" class="flex flex-wrap items-center justify-center gap-1 mt-4">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" 
                class="px-4 py-2 text-sm text-muted-foreground bg-muted rounded-md cursor-not-allowed"
                v-html="link.label" 
            />
            <Link v-else
                :href="link.url"
                :class="[
                    'px-4 py-2 text-sm rounded-md transition-colors',
                    link.active 
                        ? 'bg-primary text-primary-foreground hover:bg-primary/90'
                        : 'text-foreground hover:bg-muted'
                ]"
                v-html="link.label"
            />
        </template>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true
    }
});
</script> 