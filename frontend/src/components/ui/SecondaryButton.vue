<script setup lang="ts">
withDefaults(defineProps<{ type?: 'button' | 'submit'; tone?: 'neutral' | 'danger' }>(), {
  type: 'button',
  tone: 'neutral',
})
</script>

<template>
  <button class="ui-button ui-button-secondary" :class="`ui-button-${tone}`" :type="type">
    <slot />
  </button>
</template>

