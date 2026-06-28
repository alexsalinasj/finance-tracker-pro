<script setup lang="ts">
const props = defineProps<{ name?: string | null }>()

function initials(name?: string | null) {
  return (name ?? 'FT')
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0])
    .join('')
    .toUpperCase()
}
</script>

<template>
  <span class="avatar">{{ initials(props.name) }}</span>
</template>

