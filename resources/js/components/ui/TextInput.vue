<template>
    <input
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
        v-bind="$attrs"
    >
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    }
})

defineExpose({
    focus: () => {
        document.querySelector('input')?.focus()
    }
})
</script> 