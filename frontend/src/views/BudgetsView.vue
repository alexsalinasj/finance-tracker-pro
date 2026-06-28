<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { AlertTriangle, Pencil, Plus, Trash2, WalletCards } from '@lucide/vue'
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
import { budgetService } from '../services/budgets'
import { categoryService } from '../services/categories'
import type { Budget, Category } from '../types/finance'
import { currentMonth, currentYear, money } from '../utils/formatters'

const budgets = ref<Budget[]>([])
const categories = ref<Category[]>([])
const error = ref('')
const editingId = ref<number | null>(null)
const filters = reactive({
  month: currentMonth(),
  year: currentYear(),
})
const form = reactive({
  category_id: null as number | null,
  monthly_limit: 0,
  month: currentMonth(),
  year: currentYear(),
})

function resetForm() {
  editingId.value = null
  form.category_id = categories.value[0]?.id ?? null
  form.monthly_limit = 0
  form.month = filters.month
  form.year = filters.year
}

async function load() {
  error.value = ''
  try {
    const [categoryData, budgetData] = await Promise.all([
      categoryService.list('expense'),
      budgetService.list(filters),
    ])
    categories.value = categoryData
    budgets.value = budgetData
    if (!form.category_id) resetForm()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

function edit(budget: Budget) {
  editingId.value = budget.id
  form.category_id = budget.category_id
  form.monthly_limit = Number(budget.monthly_limit)
  form.month = budget.month
  form.year = budget.year
}

async function save() {
  if (!form.category_id) {
    error.value = 'Selecciona una categoría.'
    return
  }

  error.value = ''
  const payload = {
    category_id: form.category_id,
    monthly_limit: form.monthly_limit,
    month: form.month,
    year: form.year,
  }

  try {
    if (editingId.value) {
      await budgetService.update(editingId.value, payload)
    } else {
      await budgetService.create(payload)
    }
    resetForm()
    await load()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

async function remove(id: number) {
  if (!confirm('¿Eliminar este presupuesto?')) return

  try {
    await budgetService.remove(id)
    await load()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

function tone(budget: Budget) {
  if (budget.alert_level === 'danger') return 'danger'
  if (budget.alert_level === 'warning') return 'warning'
  return 'success'
}

onMounted(load)
</script>

<template>
  <div class="page-shell">
    <header class="page-hero">
      <div>
        <p class="eyebrow">Control de presupuesto</p>
        <h1 class="page-title">Presupuestos</h1>
        <p class="page-subtitle">Visualiza límites mensuales como señales suaves antes de que el gasto se salga del plan.</p>
      </div>
      <form class="filter-bar" @submit.prevent="load">
        <Input v-model="filters.month" label="Mes" type="number" min="1" max="12" required />
        <Input v-model="filters.year" label="Año" type="number" min="2000" max="2100" required />
        <PrimaryButton type="submit">Filtrar</PrimaryButton>
      </form>
    </header>

    <ApiAlert :message="error" />

    <ChartCard :title="editingId ? 'Editar presupuesto' : 'Nuevo presupuesto'" subtitle="Límite por categoría">
      <form class="form-row" @submit.prevent="save">
        <Select v-model="form.category_id" class="span-4" label="Categoría" required number>
          <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
        </Select>
        <Input v-model="form.monthly_limit" class="span-2" label="Límite" type="number" min="0.01" step="0.01" required />
        <Input v-model="form.month" class="span-2" label="Mes" type="number" min="1" max="12" required />
        <Input v-model="form.year" class="span-2" label="Año" type="number" min="2000" max="2100" required />
        <div class="form-actions span-2" style="align-content: end">
          <PrimaryButton type="submit">
            <Plus :size="17" />
            {{ editingId ? 'Guardar' : 'Crear' }}
          </PrimaryButton>
        </div>
        <div v-if="editingId" class="form-actions span-12">
          <SecondaryButton @click="resetForm">Cancelar edición</SecondaryButton>
        </div>
      </form>
    </ChartCard>

    <ChartCard title="Consumo mensual" subtitle="Alertas al 80% y 100%">
      <div v-if="budgets.length" class="budget-grid">
        <article v-for="budget in budgets" :key="budget.id" class="progress-card">
          <div class="progress-copy">
            <CategoryBadge :category="budget.category" />
            <p>{{ budget.month }}/{{ budget.year }} · {{ money(budget.spent) }} de {{ money(budget.monthly_limit) }}</p>
          </div>
          <span class="status-pill" :class="`pill-${tone(budget)}`">
            <AlertTriangle v-if="budget.alert_level !== 'ok'" :size="14" />
            {{ budget.usage_percentage }}%
          </span>
          <div class="progress-footer">
            <div class="progress-bar-shell">
              <div
                class="progress-bar-fill"
                :class="tone(budget)"
                :style="{ '--value': `${Math.min(budget.usage_percentage, 100)}%` }"
              ></div>
            </div>
            <div class="form-actions" style="margin-top: 14px">
              <IconButton :icon="Pencil" label="Editar" @click="edit(budget)" />
              <IconButton :icon="Trash2" label="Eliminar" tone="danger" @click="remove(budget.id)" />
            </div>
          </div>
        </article>
      </div>
      <EmptyState v-else title="Sin presupuestos" message="Define límites por categoría y deja que el sistema te avise antes de llegar al borde." />
    </ChartCard>

    <section class="premium-card" style="padding: 18px">
      <div class="item-title">
        <span class="item-icon"><WalletCards :size="18" /></span>
        <div class="item-copy">
          <strong>Lectura rápida</strong>
          <span>El amarillo aparece al superar el 80%; rojo al llegar o pasar el 100%.</span>
        </div>
      </div>
    </section>
  </div>
</template>
