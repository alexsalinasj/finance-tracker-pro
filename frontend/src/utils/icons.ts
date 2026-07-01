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
  { value: 'banknote', label: 'Efectivo', icon: Banknote },
  { value: 'credit', label: 'Tarjeta', icon: CreditCard },
  { value: 'bank', label: 'Banco', icon: Landmark },
  { value: 'salary', label: 'Salario', icon: BriefcaseBusiness },
  { value: 'business', label: 'Negocio', icon: Building2 },
  { value: 'investment', label: 'Inversión', icon: TrendingUp },
  { value: 'savings', label: 'Ahorro', icon: PiggyBank },
  { value: 'home', label: 'Hogar', icon: Home },
  { value: 'food', label: 'Comida', icon: Utensils },
  { value: 'coffee', label: 'Café', icon: Coffee },
  { value: 'transport', label: 'Transporte', icon: Car },
  { value: 'motorcycle', label: 'Moto', icon: Motorbike },
  { value: 'bus', label: 'Bus', icon: Bus },
  { value: 'fuel', label: 'Gasolina', icon: Fuel },
  { value: 'travel', label: 'Viajes', icon: Plane },
  { value: 'shopping', label: 'Compras', icon: ShoppingBag },
  { value: 'clothing', label: 'Ropa', icon: Shirt },
  { value: 'subscriptions', label: 'Suscripciones', icon: Smartphone },
  { value: 'services', label: 'Servicios', icon: Lightbulb },
  { value: 'health', label: 'Salud', icon: HeartPulse },
  { value: 'fitness', label: 'Fitness', icon: Dumbbell },
  { value: 'entertainment', label: 'Entretenimiento', icon: Clapperboard },
  { value: 'game', label: 'Gaming', icon: Gamepad2 },
  { value: 'music', label: 'Música', icon: Music },
  { value: 'education', label: 'Educación', icon: GraduationCap },
  { value: 'book', label: 'Libros', icon: BookOpen },
  { value: 'gift', label: 'Regalos', icon: Gift },
  { value: 'insurance', label: 'Seguro', icon: Shield },
  { value: 'receipt', label: 'Recibos', icon: ReceiptText },
  { value: 'laptop', label: 'Tecnología', icon: Laptop },
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

const iconLabels = new Map(iconOptions.map((option) => [option.value, option.label]))

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
