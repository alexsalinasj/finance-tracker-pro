<script setup lang="ts">
import type { Category } from '../../types/finance'
import { resolveIcon } from '../../utils/icons'

const props = defineProps<{ category?: Pick<Category, 'name' | 'color' | 'icon'> | null; compact?: boolean }>()
</script>

<template>
  <span class="category-badge" :class="{ compact }" :style="{ '--badge-color': category?.color ?? '#4F46E5' }">
    <span class="category-icon"><component :is="resolveIcon(category?.icon)" :size="compact ? 14 : 16" /></span>
    <span>{{ category?.name ?? 'Sin categoría' }}</span>
  </span>
</template>
