import {
  Banknote,
  BriefcaseBusiness,
  Car,
  ChartNoAxesColumnIncreasing,
  Clapperboard,
  CreditCard,
  Gamepad2,
  HeartPulse,
  Home,
  Landmark,
  Laptop,
  Lightbulb,
  Music,
  PiggyBank,
  ReceiptText,
  ShoppingBag,
  Smartphone,
  TrendingUp,
  Utensils,
  Wallet,
  type LucideIcon,
} from '@lucide/vue'

const iconMap: Record<string, LucideIcon> = {
  bank: Landmark,
  banknote: Banknote,
  briefcase: BriefcaseBusiness,
  car: Car,
  cart: ShoppingBag,
  credit: CreditCard,
  entertainment: Clapperboard,
  food: Utensils,
  game: Gamepad2,
  heart: HeartPulse,
  home: Home,
  laptop: Laptop,
  lightbulb: Lightbulb,
  music: Music,
  phone: Smartphone,
  receipt: ReceiptText,
  salary: BriefcaseBusiness,
  subscriptions: Smartphone,
  transport: Car,
  'trending-up': TrendingUp,
  wallet: Wallet,
}

export function resolveIcon(icon?: string | null): LucideIcon {
  if (!icon) return Wallet

  const normalized = icon.toLowerCase().trim()

  return iconMap[normalized] ?? iconMap[normalized.replace(/\s+/g, '-')] ?? Wallet
}

export { PiggyBank }

