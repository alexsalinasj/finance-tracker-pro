<script setup lang="ts">
import { X } from '@lucide/vue'
import IconButton from './IconButton.vue'

defineProps<{ open: boolean; title: string }>()
defineEmits<{ close: [] }>()
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="modal-backdrop-soft" @click.self="$emit('close')">
      <section class="modal-panel">
        <header class="modal-head">
          <h2>{{ title }}</h2>
          <IconButton :icon="X" label="Cerrar" @click="$emit('close')" />
        </header>
        <slot />
      </section>
    </div>
  </Teleport>
</template>

