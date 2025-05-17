<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" :class="className" v-bind="$attrs">
        <!-- Latar belakang kotak berwarna oranye -->
        <rect width="24" height="24" rx="3" fill="#FF5722" />
        
        <!-- Simbol >_ sebagai kode/prompt -->
        <path
            d="M7,14l3-3L7,8v2l1.5,1L7,12V14z M12,14h5v-2h-5V14z"
            fill="white"
            stroke="white"
            stroke-width="0.5"
        />
    </svg>
</template>
