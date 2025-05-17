<script lang="ts" setup>
import { Toaster as Sonner, type ToasterProps } from 'vue-sonner'

const props = defineProps<ToasterProps>()
</script>

<template>
  <Sonner
    class="toaster group bg-white text-gray-900 border-gray-200 dark:bg-gray-900 dark:text-white dark:border-gray-700"
    v-bind="props"
  />
</template>
