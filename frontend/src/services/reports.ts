import api from './api'
import type { ReportData } from '../types/finance'

export const reportService = {
  async summary(params: { month: number; year: number }) {
    const { data } = await api.get<{ data: ReportData }>('/reports', { params })
    return data.data
  },

  async exportPdf(params: { month: number; year: number }) {
    const { data } = await api.get<Blob>('/reports/export/pdf', {
      params,
      responseType: 'blob',
    })
    return data
  },
}

