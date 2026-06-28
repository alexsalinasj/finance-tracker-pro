import { defineStore } from 'pinia'
import { dashboardService } from '../services/dashboard'
import type { DashboardData } from '../types/finance'

interface DashboardState {
  data: DashboardData | null
  loading: boolean
}

export const useDashboardStore = defineStore('dashboard', {
  state: (): DashboardState => ({
    data: null,
    loading: false,
  }),

  actions: {
    async load(params?: { month?: number; year?: number }) {
      this.loading = true
      try {
        this.data = await dashboardService.get(params)
      } finally {
        this.loading = false
      }
    },
  },
})

