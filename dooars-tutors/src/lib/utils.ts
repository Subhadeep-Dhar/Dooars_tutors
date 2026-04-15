import { type ClassValue, clsx } from 'clsx'

// Simple clsx implementation (no tailwind-merge needed for this project)
export function cn(...inputs: ClassValue[]) {
  return clsx(inputs)
}

export function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('en-IN', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

export function formatCurrency(amount: number) {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    minimumFractionDigits: 0,
  }).format(amount)
}

export function getInitials(name: string) {
  return name
    .split(' ')
    .map((word) => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

export function generateReferralCode(name: string, id: string) {
  const prefix = name.replace(/[^A-Za-z]/g, '').slice(0, 3).toUpperCase()
  const suffix = id.slice(-4).toUpperCase()
  return `${prefix}${suffix}`
}

export function getStarRating(rating: number): string {
  const fullStars = Math.floor(rating)
  const emptyStars = 5 - fullStars
  return '★'.repeat(fullStars) + '☆'.repeat(emptyStars)
}

export function slugify(text: string) {
  return text
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '')
}
