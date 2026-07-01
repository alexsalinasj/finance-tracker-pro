import {
  Banknote,
  BadgeDollarSign,
  BriefcaseBusiness,
  Bike,
  BookOpen,
  Building2,
  Bus,
  Car,
  ChartNoAxesColumnIncreasing,
  Clapperboard,
  Coffee,
  CreditCard,
  Dumbbell,
  Fuel,
  Gift,
  GraduationCap,
  Gamepad2,
  HandCoins,
  HeartPulse,
  Home,
  Landmark,
  Laptop,
  Lightbulb,
  Music,
  Motorbike,
  PiggyBank,
  Plane,
  ReceiptText,
  Shield,
  Shirt,
  ShoppingBag,
  Smartphone,
  TrendingUp,
  Utensils,
  Wallet,
  type LucideIcon,
} from '@lucide/vue'

export type IconOption = {
  value: string
  label: string
  icon: LucideIcon
}

export const iconOptions: IconOption[] = [
  { value: 'wallet', label: 'Billetera', icon: Wallet },
  { value: 'home', label: 'Hogar', icon: Home },
  { value: 'motorcycle', label: 'Moto', icon: Motorbike },
  { value: 'fuel', label: 'Gasolina', icon: Fuel },
  { value: 'food', label: 'Comida', icon: Utensils },
  { value: 'transport', label: 'Transporte', icon: Car },
  { value: 'shopping', label: 'Compras', icon: ShoppingBag },
  { value: 'services', label: 'Servicios', icon: Lightbulb },
  { value: 'subscriptions', label: 'Suscripciones', icon: Smartphone },
  { value: 'health', label: 'Salud', icon: HeartPulse },
  { value: 'entertainment', label: 'Entretenimiento', icon: Clapperboard },
  { value: 'salary', label: 'Salario', icon: BriefcaseBusiness },
  { value: 'savings', label: 'Ahorro', icon: PiggyBank },
  { value: 'bank', label: 'Banco', icon: Landmark },
  { value: 'credit', label: 'Tarjeta', icon: CreditCard },
]

const iconMap: Record<string, LucideIcon> = {
  'badge-dollar': BadgeDollarSign,
  bank: Landmark,
  banknote: Banknote,
  bike: Bike,
  book: BookOpen,
  briefcase: BriefcaseBusiness,
  bus: Bus,
  business: Building2,
  car: Car,
  cart: ShoppingBag,
  clothing: Shirt,
  coffee: Coffee,
  credit: CreditCard,
  education: GraduationCap,
  entertainment: Clapperboard,
  food: Utensils,
  fitness: Dumbbell,
  fuel: Fuel,
  game: Gamepad2,
  gift: Gift,
  handcoins: HandCoins,
  heart: HeartPulse,
  health: HeartPulse,
  home: Home,
  insurance: Shield,
  investment: TrendingUp,
  laptop: Laptop,
  lightbulb: Lightbulb,
  music: Music,
  motorcycle: Motorbike,
  moto: Motorbike,
  phone: Smartphone,
  receipt: ReceiptText,
  salary: BriefcaseBusiness,
  savings: PiggyBank,
  services: Lightbulb,
  shopping: ShoppingBag,
  subscriptions: Smartphone,
  transport: Car,
  'trending-up': TrendingUp,
  travel: Plane,
  wallet: Wallet,
}

const iconLabels = new Map([
  ...iconOptions.map((option) => [option.value, option.label] as const),
  ['banknote', 'Efectivo'],
  ['business', 'Negocio'],
  ['investment', 'Inversion'],
  ['coffee', 'Cafe'],
  ['bus', 'Bus'],
  ['travel', 'Viajes'],
  ['clothing', 'Ropa'],
  ['fitness', 'Fitness'],
  ['game', 'Gaming'],
  ['music', 'Musica'],
  ['education', 'Educacion'],
  ['book', 'Libros'],
  ['gift', 'Regalos'],
  ['insurance', 'Seguro'],
  ['receipt', 'Recibos'],
  ['laptop', 'Tecnologia'],
])

export function resolveIcon(icon?: string | null): LucideIcon {
  if (!icon) return Wallet

  const normalized = icon.toLowerCase().trim()

  return iconMap[normalized] ?? iconMap[normalized.replace(/\s+/g, '-')] ?? Wallet
}

export function resolveIconLabel(icon?: string | null): string {
  if (!icon) return 'Billetera'

  const normalized = icon.toLowerCase().trim()
  return iconLabels.get(normalized) ?? iconLabels.get(normalized.replace(/\s+/g, '-')) ?? 'Billetera'
}

export { PiggyBank }
