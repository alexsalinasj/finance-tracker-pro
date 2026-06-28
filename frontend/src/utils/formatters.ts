export function money(value: number | string | null | undefined): string {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(Number(value ?? 0))
}

export function today(): string {
  return new Date().toISOString().slice(0, 10)
}

export function currentMonth(): number {
  return new Date().getMonth() + 1
}

export function currentYear(): number {
  return new Date().getFullYear()
}

