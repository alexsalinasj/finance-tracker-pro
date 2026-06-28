import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import DashboardView from '../views/DashboardView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import TransactionsView from '../views/TransactionsView.vue'
import CategoriesView from '../views/CategoriesView.vue'
import BudgetsView from '../views/BudgetsView.vue'
import SavingsGoalsView from '../views/SavingsGoalsView.vue'
import ReportsView from '../views/ReportsView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/dashboard' },
    { path: '/login', name: 'login', component: LoginView, meta: { guest: true } },
    { path: '/register', name: 'register', component: RegisterView, meta: { guest: true } },
    { path: '/dashboard', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
    { path: '/incomes', name: 'incomes', component: TransactionsView, props: { type: 'income' }, meta: { requiresAuth: true } },
    { path: '/expenses', name: 'expenses', component: TransactionsView, props: { type: 'expense' }, meta: { requiresAuth: true } },
    { path: '/categories', name: 'categories', component: CategoriesView, meta: { requiresAuth: true } },
    { path: '/budgets', name: 'budgets', component: BudgetsView, meta: { requiresAuth: true } },
    { path: '/savings-goals', name: 'savings-goals', component: SavingsGoalsView, meta: { requiresAuth: true } },
    { path: '/reports', name: 'reports', component: ReportsView, meta: { requiresAuth: true } },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'dashboard' }
  }
})

export default router

