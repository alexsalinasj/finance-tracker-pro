<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { CheckCircle2, PartyPopper, Pencil, Plus, Target, Trash2 } from '@lucide/vue'
import ApiAlert from '../components/ApiAlert.vue'
import EmptyState from '../components/EmptyState.vue'
import ChartCard from '../components/ui/ChartCard.vue'
import IconButton from '../components/ui/IconButton.vue'
import Input from '../components/ui/Input.vue'
import PrimaryButton from '../components/ui/PrimaryButton.vue'
import ProgressCard from '../components/ui/ProgressCard.vue'
import SecondaryButton from '../components/ui/SecondaryButton.vue'
import Select from '../components/ui/Select.vue'
import { apiErrorMessage } from '../services/api'
import { savingsGoalService } from '../services/savingsGoals'
import type { GoalStatus, SavingsGoal } from '../types/finance'
import { money, today } from '../utils/formatters'

const goals = ref<SavingsGoal[]>([])
const error = ref('')
const editingId = ref<number | null>(null)
const form = reactive({
  name: '',
  target_amount: 0,
  current_amount: 0,
  deadline: today(),
  status: 'active' as GoalStatus,
})

const completedGoals = computed(() => goals.value.filter((goal) => goal.status === 'completed' || goal.progress_percentage >= 100))
const totalProgress = computed(() => {
  if (!goals.value.length) return 0
  return Math.round(goals.value.reduce((sum, goal) => sum + goal.progress_percentage, 0) / goals.value.length)
})

function resetForm() {
  editingId.value = null
  form.name = ''
  form.target_amount = 0
  form.current_amount = 0
  form.deadline = today()
  form.status = 'active'
}

async function load() {
  error.value = ''
  try {
    goals.value = await savingsGoalService.list()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

function edit(goal: SavingsGoal) {
  editingId.value = goal.id
  form.name = goal.name
  form.target_amount = Number(goal.target_amount)
  form.current_amount = Number(goal.current_amount)
  form.deadline = goal.deadline
  form.status = goal.status
}

async function save() {
  error.value = ''
  try {
    if (editingId.value) {
      await savingsGoalService.update(editingId.value, form)
    } else {
      await savingsGoalService.create(form)
    }
    resetForm()
    await load()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

async function remove(id: number) {
  if (!confirm('¿Eliminar esta meta?')) return

  try {
    await savingsGoalService.remove(id)
    await load()
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}

function statusClass(status: GoalStatus) {
  if (status === 'completed') return 'pill-success'
  if (status === 'paused') return 'pill-warning'
  return 'pill-primary'
}

onMounted(load)
</script>

<template>
  <div class="page-shell">
    <header class="page-hero">
      <div>
        <p class="eyebrow">Progreso de ahorro</p>
        <h1 class="page-title">Metas de ahorro</h1>
        <p class="page-subtitle">Convierte objetivos grandes en progreso visible, medible y motivador.</p>
      </div>
    </header>

    <ApiAlert :message="error" />

    <section class="metric-grid triple">
      <ProgressCard title="Progreso promedio" :value="totalProgress" :footer="`${goals.length} metas activas o históricas`" tone="success" />
      <ProgressCard title="Metas completadas" :value="goals.length ? (completedGoals.length / goals.length) * 100 : 0" :footer="`${completedGoals.length} logros desbloqueados`" tone="primary" />
      <article class="progress-card">
        <div class="progress-copy">
          <h3>Impulso financiero</h3>
          <p>Pequeños aportes consistentes crean libertad futura.</p>
        </div>
        <span class="progress-ring" style="--progress: 74%"><Target :size="24" /></span>
        <div class="progress-footer">Mantener el hábito es tan importante como el monto.</div>
      </article>
    </section>

    <ChartCard :title="editingId ? 'Editar meta' : 'Nueva meta'" subtitle="Objetivo, avance y fecha límite">
      <form class="form-row" @submit.prevent="save">
        <Input v-model="form.name" class="span-3" label="Nombre" required />
        <Input v-model="form.target_amount" class="span-2" label="Objetivo" type="number" min="0.01" step="0.01" required />
        <Input v-model="form.current_amount" class="span-2" label="Actual" type="number" min="0" step="0.01" required />
        <Input v-model="form.deadline" class="span-2" label="Fecha límite" type="date" required />
        <Select v-model="form.status" class="span-2" label="Estado" required>
          <option value="active">Activa</option>
          <option value="paused">Pausada</option>
          <option value="completed">Completada</option>
        </Select>
        <div class="form-actions span-1" style="align-content: end">
          <PrimaryButton type="submit">
            <Plus :size="17" />
            Guardar
          </PrimaryButton>
        </div>
        <div v-if="editingId" class="form-actions span-12">
          <SecondaryButton @click="resetForm">Cancelar edición</SecondaryButton>
        </div>
      </form>
    </ChartCard>

    <ChartCard title="Tus objetivos" subtitle="Progreso circular y estado">
      <div v-if="goals.length" class="goal-grid">
        <article v-for="goal in goals" :key="goal.id" class="progress-card">
          <div class="progress-copy">
            <h3>{{ goal.name }}</h3>
            <p>{{ money(goal.current_amount) }} ahorrados de {{ money(goal.target_amount) }} · límite {{ goal.deadline }}</p>
          </div>
          <div class="progress-ring" :style="{ '--progress': `${goal.progress_percentage}%` }">
            <span>{{ Math.round(goal.progress_percentage) }}%</span>
          </div>
          <div class="progress-footer">
            <div class="form-actions">
              <span class="status-pill" :class="statusClass(goal.status)">
                <CheckCircle2 v-if="goal.status === 'completed'" :size="14" />
                {{ goal.status }}
              </span>
              <span v-if="goal.progress_percentage >= 100" class="status-pill pill-success">
                <PartyPopper :size="14" />
                Meta lograda
              </span>
            </div>
            <div class="form-actions" style="margin-top: 14px">
              <IconButton :icon="Pencil" label="Editar" @click="edit(goal)" />
              <IconButton :icon="Trash2" label="Eliminar" tone="danger" @click="remove(goal.id)" />
            </div>
          </div>
        </article>
      </div>
      <EmptyState v-else title="Sin metas todavía" message="Define una meta de ahorro y observa cómo cada aporte se convierte en progreso." />
    </ChartCard>
  </div>
</template>
