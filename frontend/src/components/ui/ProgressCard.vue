<script setup lang="ts">
defineProps<{
  title: string
  subtitle?: string
  value: number
  footer?: string
  tone?: 'success' | 'warning' | 'danger' | 'primary'
}>()
</script>

<template>
  <article class="progress-card" :class="`progress-card-${tone ?? 'primary'}`">
    <div class="progress-copy">
      <h3>{{ title }}</h3>
      <p v-if="subtitle">{{ subtitle }}</p>
    </div>
    <div class="progress-ring" :style="{ '--progress': `${Math.min(value, 100)}%` }">
      <span>{{ Math.round(value) }}%</span>
    </div>
    <div v-if="footer" class="progress-footer">{{ footer }}</div>
  </article>
</template>

