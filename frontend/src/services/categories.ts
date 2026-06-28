import api from './api'
import type { Category, CategoryType } from '../types/finance'

export type CategoryPayload = Omit<Category, 'id'>

export const categoryService = {
  async list(type?: CategoryType) {
    const { data } = await api.get<{ data: Category[] }>('/categories', { params: { type } })
    return data.data
  },

  async create(payload: CategoryPayload) {
    const { data } = await api.post<{ data: Category }>('/categories', payload)
    return data.data
  },

  async update(id: number, payload: CategoryPayload) {
    const { data } = await api.put<{ data: Category }>(`/categories/${id}`, payload)
    return data.data
  },

  async remove(id: number) {
    await api.delete(`/categories/${id}`)
  },
}

