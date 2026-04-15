import { LucideIcon, BookOpen, Music, Palette, Dumbbell, Drama, Trophy, Building2, GraduationCap } from 'lucide-react'

export interface Category {
  slug: string
  name: string
  description: string
  icon: LucideIcon
  professionKeys: string[]  // Keys in profession_details JSON
  gradient: string
}

export const CATEGORIES: Category[] = [
  {
    slug: 'tutors',
    name: 'Academic Tutors',
    description: 'Expert tutors for all subjects and boards',
    icon: GraduationCap,
    professionKeys: ['tutor'],
    gradient: 'from-blue-600 to-indigo-700',
  },
  {
    slug: 'arts',
    name: 'Arts & Visual Arts',
    description: 'Art teachers, painting, drawing, sculpture',
    icon: Palette,
    professionKeys: ['art_teacher', 'visual_arts'],
    gradient: 'from-pink-500 to-rose-600',
  },
  {
    slug: 'dance',
    name: 'Dance',
    description: 'Classical, contemporary, folk, and more',
    icon: Drama,
    professionKeys: ['dance_teacher', 'dance_school'],
    gradient: 'from-purple-500 to-violet-600',
  },
  {
    slug: 'music',
    name: 'Music & Singing',
    description: 'Instruments, vocal training, music theory',
    icon: Music,
    professionKeys: ['music_teacher', 'singing_teacher', 'music_school', 'singing_school'],
    gradient: 'from-amber-500 to-orange-600',
  },
  {
    slug: 'sports',
    name: 'Sports Coaching',
    description: 'Cricket, football, badminton, and more',
    icon: Trophy,
    professionKeys: ['sports_coach', 'sports_coaching_centre'],
    gradient: 'from-green-500 to-emerald-600',
  },
  {
    slug: 'gym-yoga',
    name: 'Gym & Yoga',
    description: 'Fitness trainers, yoga instructors',
    icon: Dumbbell,
    professionKeys: ['trainer', 'gym_yoga'],
    gradient: 'from-red-500 to-rose-600',
  },
  {
    slug: 'centres',
    name: 'Coaching Centres',
    description: 'Educational, computer, abacus centres',
    icon: Building2,
    professionKeys: ['educational_coaching_centre', 'computer_centre', 'abacus_centre'],
    gradient: 'from-cyan-500 to-teal-600',
  },
]

export function getCategoryBySlug(slug: string): Category | undefined {
  return CATEGORIES.find((c) => c.slug === slug)
}
