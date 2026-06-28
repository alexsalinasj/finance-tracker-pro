import { defineStore } from 'pinia'
import { authService } from '../services/auth'
import type { User } from '../types/finance'

interface AuthState {
  user: User | null
  token: string | null
  loading: boolean
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: localStorage.getItem('auth_user') ? JSON.parse(localStorage.getItem('auth_user') as string) : null,
    token: localStorage.getItem('auth_token'),
    loading: false,
  }),

  getters: {
    isAuthenticated: (state) => Boolean(state.token),
  },

  actions: {
    persist(user: User, token: string) {
      this.user = user
      this.token = token
      localStorage.setItem('auth_user', JSON.stringify(user))
      localStorage.setItem('auth_token', token)
    },

    async login(payload: { email: string; password: string }) {
      this.loading = true
      try {
        const response = await authService.login(payload)
        this.persist(response.user, response.token)
      } finally {
        this.loading = false
      }
    },

    async register(payload: { name: string; email: string; password: string; password_confirmation: string }) {
      this.loading = true
      try {
        const response = await authService.register(payload)
        this.persist(response.user, response.token)
      } finally {
        this.loading = false
      }
    },

    async fetchMe() {
      if (!this.token) return
      this.user = await authService.me()
      localStorage.setItem('auth_user', JSON.stringify(this.user))
    },

    async logout() {
      try {
        if (this.token) {
          await authService.logout()
        }
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('auth_user')
        localStorage.removeItem('auth_token')
      }
    },
  },
})

