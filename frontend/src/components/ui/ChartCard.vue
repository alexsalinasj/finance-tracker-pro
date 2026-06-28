<script setup lang="ts">
defineProps<{ title: string; subtitle?: string }>()
</script>

<template>
  <section class="chart-card premium-card">
    <header class="section-head">
      <div>
        <p v-if="subtitle" class="eyebrow">{{ subtitle }}</p>
        <h2>{{ title }}</h2>
      </div>
      <slot name="action" />
    </header>
    <slot />
  </section>
</template>

