<script setup lang="ts">
interface Props {
  title: string;
  description?: string;
}

defineProps<Props>();
</script>

<template>
  <div class="space-y-1.5">
    <h3 class="text-2xl font-semibold tracking-tight">{{ title }}</h3>
    <p v-if="description" class="text-sm text-muted-foreground">
      {{ description }}
    </p>
  </div>
</template> 