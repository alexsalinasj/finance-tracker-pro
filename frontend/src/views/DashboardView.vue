<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { CircleDollarSign, PiggyBank, Target, TrendingDown, TrendingUp, Wallet } from '@lucide/vue'
import ApiAlert from '../components/ApiAlert.vue'
import DoughnutChart from '../components/DoughnutChart.vue'
import EmptyState from '../components/EmptyState.vue'
import LineChart from '../components/LineChart.vue'
import StatCard from '../components/StatCard.vue'
import ChartCard from '../components/ui/ChartCard.vue'
import Input from '../components/ui/Input.vue'
import PrimaryButton from '../components/ui/PrimaryButton.vue'
import CategoryBadge from '../components/ui/CategoryBadge.vue'
import { apiErrorMessage } from '../services/api'
import { useDashboardStore } from '../stores/dashboard'
import { currentMonth, currentYear, money } from '../utils/formatters'

const dashboard = useDashboardStore()
const error = ref('')
const filters = reactive({
  month: currentMonth(),
  year: currentYear(),
})

const data = computed(() => dashboard.data)
const trendLabels = computed(() => data.value?.income_vs_expense.map((item) => item.label) ?? [])
const trendIncome = computed(() => data.value?.income_vs_expense.map((item) => item.income) ?? [])
const trendExpense = computed(() => data.value?.income_vs_expense.map((item) => item.expense) ?? [])
const balancePoints = computed(() => data.value?.income_vs_expense.map((item) => item.income - item.expense) ?? [])
const expenseLabels = computed(() => data.value?.expenses_by_category.map((item) => item.name) ?? [])
const expenseValues = computed(() => data.value?.expenses_by_category.map((item) => Number(item.total)) ?? [])
const expenseColors = computed(() => data.value?.expenses_by_category.map((item) => item.color) ?? [])

async function load() {
  error.value = ''
  try {
    await dashboard.load(filters)
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
        <p class="eyebrow">Resumen mensual</p>
        <h1 class="page-title">Panel financiero</h1>
        <p class="page-subtitle">Tus ingresos, gastos y metas en una lectura clara para decidir mejor cada mes.</p>
      </div>
      <form class="filter-bar" @submit.prevent="load">
        <Input v-model="filters.month" label="Mes" type="number" min="1" max="12" required />
        <Input v-model="filters.year" label="Año" type="number" min="2000" max="2100" required />
        <PrimaryButton type="submit" :loading="dashboard.loading">Actualizar</PrimaryButton>
      </form>
    </header>

    <ApiAlert :message="error" />

    <div v-if="dashboard.loading && !data" class="loading-stack">
      <div class="skeleton"></div>
      <div class="skeleton"></div>
      <div class="skeleton"></div>
    </div>

    <template v-if="data">
      <section class="metric-grid">
        <StatCard label="Saldo actual" :value="money(data.summary.current_balance)" :icon="Wallet" trend="+8.2%" :points="balancePoints" />
        <StatCard label="Ingresos" :value="money(data.summary.monthly_income)" :icon="TrendingUp" tone="success" trend="+12.4%" :points="trendIncome" />
        <StatCard label="Gastos" :value="money(data.summary.monthly_expense)" :icon="TrendingDown" tone="danger" trend="-3.1%" :points="trendExpense" />
        <StatCard label="Ahorro" :value="money(data.summary.available_savings)" :icon="PiggyBank" tone="primary" trend="+6.8%" :points="balancePoints" />
        <StatCard label="Meta de ahorro" :value="`${data.summary.goals_completion_percentage}%`" :icon="Target" tone="warning" trend="+4.6%" :points="[12, 20, 28, 34, 43, 49, data.summary.goals_completion_percentage]" />
      </section>

      <section class="report-grid dashboard-main-grid">
        <ChartCard title="Ingresos, gastos y balance" subtitle="Tendencia anual">
          <LineChart :labels="trendLabels" :income="trendIncome" :expense="trendExpense" />
        </ChartCard>

        <ChartCard title="Gasto por categoría" subtitle="Distribución mensual">
          <DoughnutChart v-if="expenseValues.length" :labels="expenseLabels" :values="expenseValues" :colors="expenseColors" />
          <EmptyState v-else title="Sin gastos todavía" message="Registra tu primer gasto y empieza a leer tus hábitos con contexto." />
        </ChartCard>
      </section>

      <section class="report-grid split-grid">
        <ChartCard title="Alertas inteligentes" subtitle="Presupuestos bajo observacion">
          <div v-if="data.budget_alerts.length" class="data-list">
            <article v-for="budget in data.budget_alerts" :key="budget.id" class="data-row" style="grid-template-columns: 1fr 120px 1fr">
              <div class="item-title">
                <span class="item-icon"><CircleDollarSign :size="18" /></span>
                <div class="item-copy">
                  <strong>{{ budget.category?.name ?? 'Categoría' }}</strong>
                  <span>{{ money(budget.spent) }} usados de {{ money(budget.monthly_limit) }}</span>
                </div>
              </div>
              <span class="status-pill" :class="budget.alert_level === 'danger' ? 'pill-danger' : 'pill-warning'">
                {{ budget.usage_percentage }}%
              </span>
              <div class="progress-bar-shell">
                <div
                  class="progress-bar-fill"
                  :class="budget.alert_level === 'danger' ? 'danger' : 'warning'"
                  :style="{ '--value': `${Math.min(budget.usage_percentage, 100)}%` }"
                ></div>
              </div>
            </article>
          </div>
          <EmptyState v-else title="Todo bajo control" message="Tus presupuestos están dentro de los límites definidos." />
        </ChartCard>

        <ChartCard title="Actividad reciente" subtitle="Últimos movimientos">
          <div v-if="data.recent_transactions.length" class="data-list">
            <article v-for="item in data.recent_transactions" :key="`${item.type}-${item.id}`" class="data-row" style="grid-template-columns: 1fr auto auto">
              <div class="item-title">
                <span class="item-icon"><component :is="item.type === 'income' ? TrendingUp : TrendingDown" :size="18" /></span>
                <div class="item-copy">
                  <strong>{{ item.name }}</strong>
                  <span>{{ item.date }}</span>
                </div>
              </div>
              <CategoryBadge :category="{ name: item.category, color: item.type === 'income' ? '#10B981' : '#EF4444', icon: item.type === 'income' ? 'trending-up' : 'receipt' }" compact />
              <strong :class="item.type === 'income' ? 'amount-positive' : 'amount-negative'">{{ money(item.amount) }}</strong>
            </article>
          </div>
          <EmptyState v-else title="Sin actividad" message="Tus movimientos recientes aparecerán aquí." />
        </ChartCard>
      </section>
    </template>
  </div>
</template>
