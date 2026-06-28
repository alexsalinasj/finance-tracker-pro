<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  BarChart3,
  ChevronLeft,
  ChevronRight,
  CircleDollarSign,
  FolderKanban,
  LayoutDashboard,
  LogOut,
  Menu,
  ReceiptText,
  Target,
  WalletCards,
  X,
} from '@lucide/vue'
import Avatar from './ui/Avatar.vue'
import IconButton from './ui/IconButton.vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const collapsed = ref(false)
const mobileOpen = ref(false)

const navItems = [
  { to: '/dashboard', label: 'Inicio', icon: LayoutDashboard },
  { to: '/incomes', label: 'Ingresos', icon: CircleDollarSign },
  { to: '/expenses', label: 'Gastos', icon: ReceiptText },
  { to: '/categories', label: 'Categorías', icon: FolderKanban },
  { to: '/budgets', label: 'Presupuestos', icon: WalletCards },
  { to: '/savings-goals', label: 'Metas', icon: Target },
  { to: '/reports', label: 'Reportes', icon: BarChart3 },
]

async function logout() {
  await auth.logout()
  mobileOpen.value = false
  await router.push({ name: 'login' })
}
</script>

<template>
  <div class="mobile-bar">
    <div class="user-strip">
      <span class="brand-mark">
        <img class="brand-logo" src="/logo-sin-bg-app.png" alt="" aria-hidden="true" />
      </span>
      <strong>Finance Tracker Pro</strong>
    </div>
    <IconButton :icon="mobileOpen ? X : Menu" label="Menú" @click="mobileOpen = !mobileOpen" />
  </div>

  <aside class="sidebar" :class="{ collapsed, 'mobile-open': mobileOpen }">
    <div class="sidebar-brand">
      <RouterLink class="brand-mark" to="/dashboard" @click="mobileOpen = false">
        <img class="brand-logo" src="/logo-sin-bg-app.png" alt="Finance Tracker Pro" />
      </RouterLink>
      <div class="brand-copy">
        <strong>Finance Tracker Pro</strong>
        <span>Control financiero diario</span>
      </div>
    </div>

    <nav class="sidebar-nav" aria-label="Principal">
      <RouterLink
        v-for="item in navItems"
        :key="item.to"
        class="sidebar-link"
        :to="item.to"
        :title="item.label"
        @click="mobileOpen = false"
      >
        <span class="nav-icon"><component :is="item.icon" :size="19" /></span>
        <span class="nav-label">{{ item.label }}</span>
      </RouterLink>
    </nav>

    <div class="sidebar-footer">
      <div class="user-strip">
        <Avatar :name="auth.user?.name" />
        <div class="user-meta">
          <strong>{{ auth.user?.name }}</strong>
          <span>{{ auth.user?.email }}</span>
        </div>
      </div>
      <div class="sidebar-actions">
        <IconButton :icon="collapsed ? ChevronRight : ChevronLeft" label="Colapsar" @click="collapsed = !collapsed" />
        <IconButton :icon="LogOut" label="Cerrar sesión" tone="danger" @click="logout" />
      </div>
    </div>
  </aside>
</template>
