<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { Pencil, Plus, Trash2 } from '@lucide/vue'
import ApiAlert from '../components/ApiAlert.vue'
import EmptyState from '../components/EmptyState.vue'
import CategoryBadge from '../components/ui/CategoryBadge.vue'
import ChartCard from '../components/ui/ChartCard.vue'
import IconButton from '../components/ui/IconButton.vue'
import Input from '../components/ui/Input.vue'
import PrimaryButton from '../components/ui/PrimaryButton.vue'
import SecondaryButton from '../components/ui/SecondaryButton.vue'
import Select from '../components/ui/Select.vue'
import { apiErrorMessage } from '../services/api'
import { categoryService } from '../services/categories'
import type { Category, CategoryType } from '../types/finance'
import { resolveIcon } from '../utils/icons'

const categories = ref<Category[]>([])
const error = ref('')
const editingId = ref<number | null>(null)
const form = reactive({
  name: '',
  type: 'expense' as CategoryType,
  color: '#4F46E5',
  icon: 'wallet',
})

async function load() {
  error.value = ''
  try {
    categories.value = await categoryService.list()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

function resetForm() {
  editingId.value = null
  form.name = ''
  form.type = 'expense'
  form.color = '#4F46E5'
  form.icon = 'wallet'
}

function edit(category: Category) {
  editingId.value = category.id
  form.name = category.name
  form.type = category.type
  form.color = category.color
  form.icon = category.icon
}

async function save() {
  error.value = ''
  try {
    if (editingId.value) {
      await categoryService.update(editingId.value, form)
    } else {
      await categoryService.create(form)
    }
    resetForm()
    await load()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

async function remove(id: number) {
  if (!confirm('¿Eliminar esta categoría?')) return

  try {
    await categoryService.remove(id)
    await load()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

onMounted(load)
</script>

<template>
  <div class="page-shell">
    <header class="page-hero">
      <div>
        <p class="eyebrow">Sistema de categorías</p>
        <h1 class="page-title">Categorías</h1>
        <p class="page-subtitle">Dale identidad visual a tus movimientos para reconocer patrones en segundos.</p>
      </div>
    </header>

    <ApiAlert :message="error" />

    <ChartCard :title="editingId ? 'Editar categoría' : 'Nueva categoría'" subtitle="Color, icono y tipo">
      <form class="form-row" @submit.prevent="save">
        <Input v-model="form.name" class="span-3" label="Nombre" required />
        <Select v-model="form.type" class="span-3" label="Tipo" required>
          <option value="income">Ingreso</option>
          <option value="expense">Gasto</option>
        </Select>
        <label class="field span-2">
          <span>Color</span>
          <input v-model="form.color" class="ui-input" type="color" />
        </label>
        <Input v-model="form.icon" class="span-2" label="Icono" required />
        <div class="span-2" style="display: grid; align-content: end">
          <CategoryBadge :category="{ name: form.name || 'Vista previa', color: form.color, icon: form.icon }" />
        </div>
        <div class="form-actions span-12">
          <PrimaryButton type="submit">
            <Plus :size="17" />
            {{ editingId ? 'Guardar categoría' : 'Crear categoría' }}
          </PrimaryButton>
          <SecondaryButton v-if="editingId" @click="resetForm">Cancelar</SecondaryButton>
        </div>
      </form>
    </ChartCard>

    <ChartCard title="Mapa de categorías" subtitle="Identidad visual por tipo">
      <div v-if="categories.length" class="category-grid">
        <article v-for="category in categories" :key="category.id" class="progress-card">
          <div class="item-title">
            <span class="item-icon" :style="{ color: category.color, background: `${category.color}22` }">
              <component :is="resolveIcon(category.icon)" :size="20" />
            </span>
            <div class="item-copy">
              <strong>{{ category.name }}</strong>
              <span>{{ category.icon }}</span>
            </div>
          </div>
          <span class="status-pill" :class="category.type === 'income' ? 'pill-success' : 'pill-danger'">
            {{ category.type === 'income' ? 'Ingreso' : 'Gasto' }}
          </span>
          <div class="progress-footer row-actions">
            <IconButton :icon="Pencil" label="Editar" @click="edit(category)" />
            <IconButton :icon="Trash2" label="Eliminar" tone="danger" @click="remove(category.id)" />
          </div>
        </article>
      </div>
      <EmptyState v-else title="Sin categorías" message="Crea categorías memorables para que tus reportes tengan contexto." />
    </ChartCard>
  </div>
</template>
