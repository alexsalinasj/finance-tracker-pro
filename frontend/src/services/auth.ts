import api from './api'
import type { User } from '../types/finance'

interface AuthResponse {
  user: User
  token: string
}

export const authService = {
  async login(payload: { email: string; password: string }) {
    const { data } = await api.post<AuthResponse>('/auth/login', payload)
    return data
  },

  async register(payload: { name: string; email: string; password: string; password_confirmation: string }) {
    const { data } = await api.post<AuthResponse>('/auth/register', payload)
    return data
  },

  async me() {
    const { data } = await api.get<{ user: User }>('/auth/me')
    return data.user
  },

  async logout() {
    await api.post('/auth/logout')
  },
}

