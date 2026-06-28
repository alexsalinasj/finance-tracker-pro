<script setup lang="ts">
import { AlertCircle } from '@lucide/vue'

defineProps<{ message: string }>()
</script>

<template>
  <div v-if="message" class="alert-soft" role="alert">
    <AlertCircle :size="19" />
    <span>{{ message }}</span>
  </div>
</template>

