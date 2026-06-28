<script setup lang="ts">
import {
  CategoryScale,
  Chart,
  Filler,
  Legend,
  LinearScale,
  LineController,
  LineElement,
  PointElement,
  Tooltip,
} from 'chart.js'
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'

Chart.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Tooltip, Legend, Filler)

const props = defineProps<{
  labels: string[]
  income: number[]
  expense: number[]
}>()

const canvas = ref<HTMLCanvasElement | null>(null)
let chart: Chart | null = null

function lastPositiveIndex(values: number[]): number {
  for (let index = values.length - 1; index >= 0; index -= 1) {
    if (Number(values[index]) > 0) {
      return index
    }
  }

  return -1
}

function render() {
  if (!canvas.value) return

  const lastDataIndex = Math.max(
    lastPositiveIndex(props.income),
    lastPositiveIndex(props.expense),
  )
  const normalizeSeries = (values: number[]) => values.map((value, index) => (index > lastDataIndex ? null : value))
  const incomeSeries = normalizeSeries(props.income)
  const expenseSeries = normalizeSeries(props.expense)
  const balanceSeries = props.income.map((value, index) => (index > lastDataIndex ? null : value - (props.expense[index] ?? 0)))

  chart?.destroy()
  chart = new Chart(canvas.value, {
    type: 'line',
    data: {
      labels: props.labels,
      datasets: [
        {
          label: 'Ingresos',
          data: incomeSeries,
          borderColor: '#10B981',
          backgroundColor: 'rgba(16, 185, 129, 0.10)',
          pointBackgroundColor: '#0B1120',
          pointBorderColor: '#10B981',
          pointHoverBackgroundColor: '#10B981',
          pointHoverBorderColor: '#F9FAFB',
          pointRadius: 3,
          pointHoverRadius: 6,
          borderWidth: 3,
          tension: 0.38,
          fill: true,
          spanGaps: false,
        },
        {
          label: 'Gastos',
          data: expenseSeries,
          borderColor: '#EF4444',
          backgroundColor: 'rgba(239, 68, 68, 0.08)',
          pointBackgroundColor: '#0B1120',
          pointBorderColor: '#EF4444',
          pointHoverBackgroundColor: '#EF4444',
          pointHoverBorderColor: '#F9FAFB',
          pointRadius: 3,
          pointHoverRadius: 6,
          borderWidth: 3,
          tension: 0.38,
          fill: true,
          spanGaps: false,
        },
        {
          label: 'Balance',
          data: balanceSeries,
          borderColor: '#8B5CF6',
          backgroundColor: 'rgba(139, 92, 246, 0.10)',
          pointBackgroundColor: '#0B1120',
          pointBorderColor: '#8B5CF6',
          pointHoverBackgroundColor: '#8B5CF6',
          pointHoverBorderColor: '#F9FAFB',
          pointRadius: 3,
          pointHoverRadius: 6,
          borderWidth: 3,
          tension: 0.38,
          fill: true,
          spanGaps: false,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: { duration: 260, easing: 'easeOutQuart' },
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#94A3B8',
            boxWidth: 10,
            boxHeight: 10,
            usePointStyle: true,
            padding: 18,
            font: { family: 'Geist', weight: 650 },
          },
        },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.96)',
          borderColor: 'rgba(255,255,255,0.10)',
          borderWidth: 1,
          padding: 12,
          cornerRadius: 8,
          titleColor: '#F9FAFB',
          bodyColor: '#CBD5E1',
          displayColors: true,
        },
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: '#94A3B8', font: { family: 'Geist', weight: 650 } },
          border: { display: false },
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(255,255,255,0.06)' },
          ticks: { color: '#94A3B8', font: { family: 'Geist' } },
          border: { display: false },
        },
      },
    },
  })
}

onMounted(render)
watch(() => [props.labels, props.income, props.expense], render, { deep: true })
onBeforeUnmount(() => chart?.destroy())
</script>

<template>
  <div class="chart-box">
    <canvas ref="canvas"></canvas>
  </div>
</template>
