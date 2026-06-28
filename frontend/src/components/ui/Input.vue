<script setup lang="ts">
defineProps<{
  label: string
  modelValue: string | number | null
  type?: string
  min?: string | number
  max?: string | number
  step?: string | number
  required?: boolean
  placeholder?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
}>()

function update(event: Event) {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.type === 'number' ? target.valueAsNumber : target.value)
}
</script>

<template>
  <label class="field">
    <span>{{ label }}</span>
    <input
      class="ui-input"
      :value="modelValue ?? ''"
      :type="type ?? 'text'"
      :min="min"
      :max="max"
      :step="step"
      :required="required"
      :placeholder="placeholder"
      @input="update"
    />
  </label>
</template>

