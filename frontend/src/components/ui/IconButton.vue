<script setup lang="ts">
import type { LucideIcon } from '@lucide/vue'

defineProps<{
  icon: LucideIcon
  label: string
  tone?: 'neutral' | 'danger'
}>()
</script>

<template>
  <button class="icon-button" :class="tone === 'danger' ? 'icon-button-danger' : ''" type="button" :aria-label="label" :title="label">
    <component :is="icon" :size="17" />
  </button>
</template>

