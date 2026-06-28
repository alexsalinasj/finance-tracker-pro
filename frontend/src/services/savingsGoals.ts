import api from './api'
import type { SavingsGoal } from '../types/finance'

export type SavingsGoalPayload = Omit<SavingsGoal, 'id' | 'progress_percentage'>

export const savingsGoalService = {
  async list() {
    const { data } = await api.get<{ data: SavingsGoal[] }>('/savings-goals')
    return data.data
  },

  async create(payload: SavingsGoalPayload) {
    const { data } = await api.post<{ data: SavingsGoal }>('/savings-goals', payload)
    return data.data
  },

  async update(id: number, payload: SavingsGoalPayload) {
    const { data } = await api.put<{ data: SavingsGoal }>(`/savings-goals/${id}`, payload)
    return data.data
  },

  async remove(id: number) {
    await api.delete(`/savings-goals/${id}`)
  },
}

