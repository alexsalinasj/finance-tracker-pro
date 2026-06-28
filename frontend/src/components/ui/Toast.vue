<script setup lang="ts">
defineProps<{ message: string; tone?: 'success' | 'error' | 'info' }>()
</script>

<template>
  <div v-if="message" class="toast-card" :class="`toast-${tone ?? 'info'}`">{{ message }}</div>
</template>

