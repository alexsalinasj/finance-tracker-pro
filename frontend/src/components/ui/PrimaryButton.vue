<script setup lang="ts">
withDefaults(defineProps<{ type?: 'button' | 'submit'; loading?: boolean }>(), {
  type: 'button',
  loading: false,
})
</script>

<template>
  <button class="ui-button ui-button-primary" :type="type" :disabled="loading">
    <span v-if="loading" class="ui-spinner" aria-hidden="true"></span>
    <slot />
  </button>
</template>

