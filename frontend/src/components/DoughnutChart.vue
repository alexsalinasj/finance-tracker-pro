<script setup lang="ts">
import { ArcElement, Chart, DoughnutController, Legend, Tooltip } from 'chart.js'
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'

Chart.register(DoughnutController, ArcElement, Tooltip, Legend)

const props = defineProps<{
  labels: string[]
  values: number[]
  colors: string[]
}>()

const canvas = ref<HTMLCanvasElement | null>(null)
let chart: Chart | null = null

function render() {
  if (!canvas.value) return

  chart?.destroy()
  chart = new Chart(canvas.value, {
    type: 'doughnut',
    data: {
      labels: props.labels,
      datasets: [
        {
          data: props.values,
          backgroundColor: props.colors.length ? props.colors : ['#4F46E5', '#8B5CF6', '#10B981'],
          borderColor: '#0B1120',
          borderWidth: 4,
          hoverOffset: 8,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: { duration: 260, easing: 'easeOutQuart' },
      cutout: '68%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#94A3B8',
            boxWidth: 10,
            boxHeight: 10,
            usePointStyle: true,
            padding: 16,
            font: { family: 'Geist', weight: 650 },
          },
        },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.96)',
          borderColor: 'rgba(255,255,255,0.10)',
          borderWidth: 1,
          cornerRadius: 8,
          padding: 12,
          titleColor: '#F9FAFB',
          bodyColor: '#CBD5E1',
        },
      },
    },
  })
}

onMounted(render)
watch(() => [props.labels, props.values, props.colors], render, { deep: true })
onBeforeUnmount(() => chart?.destroy())
</script>

<template>
  <div class="chart-box">
    <canvas ref="canvas"></canvas>
  </div>
</template>
