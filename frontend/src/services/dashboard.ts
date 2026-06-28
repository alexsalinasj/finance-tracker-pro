import api from './api'
import type { DashboardData } from '../types/finance'

export const dashboardService = {
  async get(params?: { month?: number; year?: number }) {
    const { data } = await api.get<{ data: DashboardData }>('/dashboard', { params })
    return data.data
  },
}

