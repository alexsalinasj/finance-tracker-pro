<script setup lang="ts">
import { Sparkles } from '@lucide/vue'

defineProps<{ title: string; message: string }>()
</script>

<template>
  <div class="empty-state">
    <div>
      <span class="empty-icon"><Sparkles :size="22" /></span>
      <h2>{{ title }}</h2>
      <p>{{ message }}</p>
    </div>
  </div>
</template>

