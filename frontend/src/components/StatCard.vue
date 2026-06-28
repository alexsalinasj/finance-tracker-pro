<script setup lang="ts">
import { computed } from 'vue'
import { ArrowUpRight, type LucideIcon } from '@lucide/vue'

const props = withDefaults(
  defineProps<{
    label: string
    value: string
    icon?: LucideIcon
    tone?: 'primary' | 'success' | 'danger' | 'warning'
    trend?: string
    points?: number[]
  }>(),
  {
    trend: '+12.4%',
    points: () => [8, 15, 12, 21, 17, 28, 24],
  },
)

const polylinePoints = computed(() => {
  const values = props.points.length ? props.points : [0]
  const max = Math.max(...values)
  const min = Math.min(...values)
  const range = max - min || 1

  return values
    .map((value, index) => {
      const x = (index / Math.max(values.length - 1, 1)) * 100
      const y = 30 - ((value - min) / range) * 26 + 2
      return `${x},${y}`
    })
    .join(' ')
})
</script>

<template>
  <article class="stat-card" :class="tone ? `stat-${tone}` : ''">
    <div class="stat-top">
      <div>
        <p class="stat-label">{{ label }}</p>
        <div class="stat-value">{{ value }}</div>
        <span class="stat-trend">
          <ArrowUpRight :size="14" />
          {{ trend }}
        </span>
      </div>
      <span v-if="icon" class="stat-icon"><component :is="icon" :size="19" /></span>
    </div>
    <svg class="sparkline" viewBox="0 0 100 36" preserveAspectRatio="none" aria-hidden="true">
      <defs>
        <linearGradient id="spark" x1="0" x2="1" y1="0" y2="0">
          <stop offset="0%" stop-color="#4F46E5" />
          <stop offset="55%" stop-color="#8B5CF6" />
          <stop offset="100%" stop-color="#10B981" />
        </linearGradient>
      </defs>
      <polyline :points="polylinePoints" />
    </svg>
  </article>
</template>

