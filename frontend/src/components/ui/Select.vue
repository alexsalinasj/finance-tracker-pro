<script setup lang="ts">
defineProps<{
  label: string
  modelValue: string | number | null
  required?: boolean
  number?: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
}>()

function update(event: Event) {
  const target = event.target as HTMLSelectElement
  emit('update:modelValue', target.dataset.number === 'true' ? Number(target.value) : target.value)
}
</script>

<template>
  <label class="field">
    <span>{{ label }}</span>
    <select class="ui-input ui-select" :value="modelValue ?? ''" :required="required" :data-number="number ? 'true' : 'false'" @change="update">
      <slot />
    </select>
  </label>
</template>
