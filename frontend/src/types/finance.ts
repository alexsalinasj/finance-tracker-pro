export type CategoryType = 'income' | 'expense'
export type GoalStatus = 'active' | 'paused' | 'completed'

export interface User {
  id: number
  name: string
  email: string
}

export interface Category {
  id: number
  name: string
  type: CategoryType
  color: string
  icon: string
}

export interface Income {
  id: number
  name: string
  amount: string | number
  date: string
  category_id: number
  description?: string | null
  category?: Category
}

export interface Expense {
  id: number
  name: string
  amount: string | number
  date: string
  category_id: number
  payment_method: string
  description?: string | null
  category?: Category
}

export interface Budget {
  id: number
  category_id: number
  monthly_limit: string | number
  month: number
  year: number
  category?: Category
  spent: number
  usage_percentage: number
  alert_level: 'ok' | 'warning' | 'danger'
}

export interface SavingsGoal {
  id: number
  name: string
  target_amount: string | number
  current_amount: string | number
  deadline: string
  status: GoalStatus
  progress_percentage: number
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  total: number
}

export interface DashboardData {
  summary: {
    current_balance: number
    monthly_income: number
    monthly_expense: number
    available_savings: number
    goals_completion_percentage: number
  }
  income_vs_expense: Array<{ label: string; income: number; expense: number }>
  expenses_by_category: Array<{ name: string; color: string; total: number }>
  budget_alerts: Budget[]
  recent_transactions: Array<{
    id: number
    type: CategoryType
    name: string
    amount: number
    date: string
    category: string
  }>
}

export interface ReportData {
  month: number
  year: number
  period_label: string
  income_summary: {
    total: number
    count: number
    by_category: Array<{ name: string; color: string; total: number }>
  }
  expense_summary: {
    total: number
    count: number
    by_category: Array<{ name: string; color: string; total: number }>
  }
  final_balance: number
  top_expense_categories: Array<{ name: string; color: string; total: number }>
}

