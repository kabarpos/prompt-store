<template>
    <label class="block font-medium text-sm text-gray-700">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="required" class="text-red-500">*</span>
    </label>
</template>

<script setup>
defineProps({
    value: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    }
})
</script> 