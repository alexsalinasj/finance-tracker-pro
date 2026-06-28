<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { Calendar, CreditCard, Pencil, Plus, ReceiptText, Trash2, TrendingDown, TrendingUp } from '@lucide/vue'
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
import { transactionService } from '../services/transactions'
import type { Category, CategoryType, Expense, Income } from '../types/finance'
import { currentMonth, currentYear, money, today } from '../utils/formatters'

const props = defineProps<{ type: CategoryType }>()

const records = ref<Array<Income | Expense>>([])
const categories = ref<Category[]>([])
const error = ref('')
const loading = ref(false)
const editingId = ref<number | null>(null)
const filters = reactive({
  month: currentMonth(),
  year: currentYear(),
})

const form = reactive({
  name: '',
  amount: 0,
  date: today(),
  category_id: null as number | null,
  payment_method: '',
  description: '',
})

const isExpense = computed(() => props.type === 'expense')
const title = computed(() => (isExpense.value ? 'Gastos' : 'Ingresos'))
const subtitle = computed(() =>
  isExpense.value
    ? 'Detecta patrones de consumo y mantente dentro de tu plan.'
    : 'Registra nuevas entradas y observa como crece tu flujo disponible.',
)
const total = computed(() => records.value.reduce((sum, item) => sum + Number(item.amount), 0))
const icon = computed(() => (isExpense.value ? TrendingDown : TrendingUp))

function resetForm() {
  editingId.value = null
  form.name = ''
  form.amount = 0
  form.date = today()
  form.category_id = categories.value[0]?.id ?? null
  form.payment_method = ''
  form.description = ''
}

async function loadCategories() {
  categories.value = await categoryService.list(props.type)
  if (!form.category_id && categories.value.length) {
    form.category_id = categories.value[0].id
  }
}

async function loadRecords() {
  const response = await transactionService.list(props.type, filters)
  records.value = response.data
}

async function load() {
  error.value = ''
  loading.value = true
  try {
    await Promise.all([loadCategories(), loadRecords()])
  } catch (err) {
    error.value = apiErrorMessage(err)
  } finally {
    loading.value = false
  }
}

function edit(record: Income | Expense) {
  editingId.value = record.id
  form.name = record.name
  form.amount = Number(record.amount)
  form.date = record.date
  form.category_id = record.category_id
  form.description = record.description ?? ''
  form.payment_method = 'payment_method' in record ? record.payment_method : ''
}

async function save() {
  if (!form.category_id) {
    error.value = 'Selecciona una categoría.'
    return
  }

  error.value = ''
  const payload = {
    name: form.name,
    amount: form.amount,
    date: form.date,
    category_id: form.category_id,
    description: form.description || null,
    ...(isExpense.value ? { payment_method: form.payment_method } : {}),
  }

  try {
    if (editingId.value) {
      await transactionService.update(props.type, editingId.value, payload)
    } else {
      await transactionService.create(props.type, payload)
    }
    resetForm()
    await loadRecords()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

async function remove(id: number) {
  if (!confirm('¿Eliminar este registro?')) return

  error.value = ''
  try {
    await transactionService.remove(props.type, id)
    await loadRecords()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

onMounted(load)
watch(() => props.type, load)
</script>

<template>
  <div class="page-shell">
    <header class="page-hero">
      <div>
        <p class="eyebrow">{{ isExpense ? 'Lectura de gasto' : 'Flujo de ingresos' }}</p>
        <h1 class="page-title">{{ title }}</h1>
        <p class="page-subtitle">{{ subtitle }}</p>
      </div>
      <form class="filter-bar" @submit.prevent="loadRecords">
        <Input v-model="filters.month" label="Mes" type="number" min="1" max="12" required />
        <Input v-model="filters.year" label="Año" type="number" min="2000" max="2100" required />
        <PrimaryButton type="submit" :loading="loading">Filtrar</PrimaryButton>
      </form>
    </header>

    <ApiAlert :message="error" />

    <section class="metric-grid triple">
      <article class="stat-card">
        <p class="stat-label">Total del periodo</p>
        <div class="stat-value" :class="isExpense ? 'amount-negative' : 'amount-positive'">{{ money(total) }}</div>
        <span class="stat-trend"><component :is="icon" :size="14" /> {{ records.length }} movimientos</span>
      </article>
      <article class="stat-card">
        <p class="stat-label">Categoría principal</p>
        <div class="stat-value">{{ records[0]?.category?.name ?? 'Pendiente' }}</div>
        <span class="stat-trend"><ReceiptText :size="14" /> Clasificación activa</span>
      </article>
      <article class="stat-card">
        <p class="stat-label">Promedio</p>
        <div class="stat-value">{{ money(records.length ? total / records.length : 0) }}</div>
        <span class="stat-trend"><Calendar :size="14" /> {{ filters.month }}/{{ filters.year }}</span>
      </article>
    </section>

    <ChartCard :title="editingId ? 'Editar movimiento' : `Nuevo ${isExpense ? 'gasto' : 'ingreso'}`" subtitle="Formulario rápido">
      <form class="form-row" @submit.prevent="save">
        <Input v-model="form.name" class="span-3" label="Nombre" required />
        <Input v-model="form.amount" class="span-2" label="Monto" type="number" min="0.01" step="0.01" required />
        <Input v-model="form.date" class="span-2" label="Fecha" type="date" required />
        <Select v-model="form.category_id" class="span-3" label="Categoría" required number>
          <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
        </Select>
        <Input v-if="isExpense" v-model="form.payment_method" class="span-2" label="Pago" required />
        <label class="field span-12">
          <span>Descripción</span>
          <textarea v-model="form.description" class="ui-input" rows="3"></textarea>
        </label>
        <div class="form-actions span-12">
          <PrimaryButton type="submit">
            <Plus :size="17" />
            {{ editingId ? 'Guardar cambios' : 'Crear registro' }}
          </PrimaryButton>
          <SecondaryButton v-if="editingId" @click="resetForm">Cancelar</SecondaryButton>
        </div>
      </form>
    </ChartCard>

    <ChartCard title="Movimientos" subtitle="Lista interactiva">
      <div v-if="loading" class="loading-stack">
        <div class="skeleton"></div>
        <div class="skeleton"></div>
      </div>
      <div v-else-if="records.length" class="data-list">
        <article v-for="record in records" :key="record.id" class="data-row">
          <div class="item-title">
            <span class="item-icon"><component :is="icon" :size="18" /></span>
            <div class="item-copy">
              <strong>{{ record.name }}</strong>
              <span>{{ record.description || 'Movimiento registrado' }}</span>
            </div>
          </div>
          <CategoryBadge :category="record.category" />
          <span class="status-pill pill-primary">
            <Calendar :size="14" />
            {{ record.date }}
          </span>
          <span v-if="isExpense" class="status-pill pill-warning">
            <CreditCard :size="14" />
            {{ 'payment_method' in record ? record.payment_method : '' }}
          </span>
          <strong :class="isExpense ? 'amount-negative' : 'amount-positive'">{{ money(record.amount) }}</strong>
          <div class="row-actions">
            <IconButton :icon="Pencil" label="Editar" @click="edit(record)" />
            <IconButton :icon="Trash2" label="Eliminar" tone="danger" @click="remove(record.id)" />
          </div>
        </article>
      </div>
      <EmptyState
        v-else
        :title="`Sin ${title.toLowerCase()} todavía`"
        :message="isExpense ? 'No has registrado gastos todavía. Empieza con uno pequeño y deja que el patrón aparezca.' : 'Registra tu primer ingreso para visualizar tu capacidad de ahorro.'"
      />
    </ChartCard>
  </div>
</template>
