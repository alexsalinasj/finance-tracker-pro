import api from './api'
import type { Budget } from '../types/finance'

export type BudgetPayload = Pick<Budget, 'category_id' | 'monthly_limit' | 'month' | 'year'>

export const budgetService = {
  async list(params?: { month?: number; year?: number }) {
    const { data } = await api.get<{ data: Budget[] }>('/budgets', { params })
    return data.data
  },

  async create(payload: BudgetPayload) {
    const { data } = await api.post<{ data: Budget }>('/budgets', payload)
    return data.data
  },

  async update(id: number, payload: BudgetPayload) {
    const { data } = await api.put<{ data: Budget }>(`/budgets/${id}`, payload)
    return data.data
  },

  async remove(id: number) {
    await api.delete(`/budgets/${id}`)
  },
}

