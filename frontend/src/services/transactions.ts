import api from './api'
import type { CategoryType, Expense, Income, PaginatedResponse } from '../types/finance'

export type IncomePayload = Omit<Income, 'id' | 'category'>
export type ExpensePayload = Omit<Expense, 'id' | 'category'>

const endpoints: Record<CategoryType, string> = {
  income: '/incomes',
  expense: '/expenses',
}

export const transactionService = {
  async list(type: CategoryType, params?: { month?: number; year?: number }) {
    const { data } = await api.get<PaginatedResponse<Income | Expense>>(endpoints[type], { params })
    return data
  },

  async create(type: CategoryType, payload: IncomePayload | ExpensePayload) {
    const { data } = await api.post<{ data: Income | Expense }>(endpoints[type], payload)
    return data.data
  },

  async update(type: CategoryType, id: number, payload: IncomePayload | ExpensePayload) {
    const { data } = await api.put<{ data: Income | Expense }>(`${endpoints[type]}/${id}`, payload)
    return data.data
  },

  async remove(type: CategoryType, id: number) {
    await api.delete(`${endpoints[type]}/${id}`)
  },
}

