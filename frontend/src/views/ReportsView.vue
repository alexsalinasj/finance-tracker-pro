<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { Download, TrendingDown, TrendingUp, Wallet } from '@lucide/vue'
import ApiAlert from '../components/ApiAlert.vue'
import DoughnutChart from '../components/DoughnutChart.vue'
import EmptyState from '../components/EmptyState.vue'
import ChartCard from '../components/ui/ChartCard.vue'
import Input from '../components/ui/Input.vue'
import PrimaryButton from '../components/ui/PrimaryButton.vue'
import CategoryBadge from '../components/ui/CategoryBadge.vue'
import { apiErrorMessage } from '../services/api'
import { reportService } from '../services/reports'
import type { ReportData } from '../types/finance'
import { currentMonth, currentYear, money } from '../utils/formatters'

const report = ref<ReportData | null>(null)
const error = ref('')
const loading = ref(false)
const filters = reactive({
  month: currentMonth(),
  year: currentYear(),
})

async function load() {
  error.value = ''
  loading.value = true
  try {
    report.value = await reportService.summary(filters)
  } catch (err) {
    error.value = apiErrorMessage(err)
  } finally {
    loading.value = false
  }
}

async function exportPdf() {
  error.value = ''
  try {
    const blob = await reportService.exportPdf(filters)
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `finance-report-${filters.year}-${filters.month}.pdf`
    link.click()
    window.URL.revokeObjectURL(url)
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
        <p class="eyebrow">Lectura mensual</p>
        <h1 class="page-title">Reportes</h1>
        <p class="page-subtitle">Convierte tus movimientos en una lectura clara de salud financiera mensual.</p>
      </div>
      <form class="filter-bar" @submit.prevent="load">
        <Input v-model="filters.month" label="Mes" type="number" min="1" max="12" required />
        <Input v-model="filters.year" label="Año" type="number" min="2000" max="2100" required />
        <PrimaryButton type="submit" :loading="loading">Filtrar</PrimaryButton>
        <PrimaryButton v-if="report" @click="exportPdf">
          <Download :size="17" />
          PDF
        </PrimaryButton>
      </form>
    </header>

    <ApiAlert :message="error" />

    <template v-if="report">
      <section class="metric-grid triple">
        <article class="stat-card">
          <span class="stat-icon"><TrendingUp :size="19" /></span>
          <p class="stat-label">Ingresos</p>
          <div class="stat-value amount-positive">{{ money(report.income_summary.total) }}</div>
          <span class="stat-trend">{{ report.income_summary.count }} registros</span>
        </article>
        <article class="stat-card">
          <span class="stat-icon"><TrendingDown :size="19" /></span>
          <p class="stat-label">Gastos</p>
          <div class="stat-value amount-negative">{{ money(report.expense_summary.total) }}</div>
          <span class="stat-trend">{{ report.expense_summary.count }} registros</span>
        </article>
        <article class="stat-card">
          <span class="stat-icon"><Wallet :size="19" /></span>
          <p class="stat-label">Balance final</p>
          <div class="stat-value" :class="report.final_balance >= 0 ? 'amount-positive' : 'amount-negative'">{{ money(report.final_balance) }}</div>
          <span class="stat-trend">{{ report.period_label }}</span>
        </article>
      </section>

      <section class="report-grid report-main-grid">
        <ChartCard title="Top 5 categorías" subtitle="Mayor concentración de gasto">
          <DoughnutChart
            v-if="report.top_expense_categories.length"
            :labels="report.top_expense_categories.map((item) => item.name)"
            :values="report.top_expense_categories.map((item) => Number(item.total))"
            :colors="report.top_expense_categories.map((item) => item.color)"
          />
          <EmptyState v-else title="Sin gastos" message="No hay gastos en este periodo para graficar." />
        </ChartCard>

        <ChartCard title="Ranking de gasto" subtitle="Categorías que merecen atención">
          <div v-if="report.top_expense_categories.length" class="data-list">
            <article v-for="(category, index) in report.top_expense_categories" :key="category.name" class="data-row" style="grid-template-columns: 52px 1fr auto">
              <span class="status-pill pill-primary">#{{ index + 1 }}</span>
              <CategoryBadge :category="{ name: category.name, color: category.color, icon: 'receipt' }" />
              <strong class="amount-negative">{{ money(category.total) }}</strong>
            </article>
          </div>
          <EmptyState v-else title="Sin ranking" message="Cuando registres gastos, tus categorías principales aparecerán aquí." />
        </ChartCard>
      </section>
    </template>

    <EmptyState v-else title="Sin reporte" message="Selecciona un periodo para generar un resumen financiero exportable." />
  </div>
</template>
