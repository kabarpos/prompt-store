<template>
    <textarea
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="textarea"
        v-bind="$attrs"
    ></textarea>
</template>

<script setup>
defineProps({
    modelValue: {
        type: String,
        default: ''
    }
})

defineExpose({
    focus: () => {
        document.querySelector('textarea')?.focus()
    }
})
</script> 