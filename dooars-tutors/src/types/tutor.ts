export interface Tutor {
  id: string
  user_id: string
  name: string
  phone: string
  email: string | null
  experience: string | null
  boards: string | null
  classes: string | null
  subjects: string | null
  teaching_preferences: string | null
  city: string | null
  address: string | null
  latitude: number | null
  longitude: number | null
  plan: 'free' | 'basic' | 'premium'
  status: 'active' | 'inactive' | 'pending' | 'suspended'
  type: 'individual' | 'organisation'
  rating: number
  rating_count: number
  profession: string | null
  profession_details: ProfessionDetails
  profile_picture: string | null
  last_login: string | null
  last_logout: string | null
  referral_code: string | null
  referred_by: string | null
  wallet_balance: number
  referral_code_created_at: string | null
  payment_status: 'pending' | 'paid' | 'failed' | 'refunded'
  payment_id: string | null
  payment_date: string | null
  payment_amount: number | null
  order_id: string | null
  created_at: string
  updated_at: string
}

export interface ProfessionDetails {
  tutor?: {
    boards: string
    classes: string
    subjects: string
    class_subject_mapping?: Record<string, string[]>
  }
  sports_coach?: {
    sports_type: string
    gender: string
    days_per_week: string
  }
  trainer?: {
    training_type: string
    gender: string
    days_per_week: string
  }
  dance_teacher?: {
    dance_type: string
    gender: string
    days_per_week: string
  }
  music_teacher?: {
    music_type: string
    instruments: string
    days_per_week: string
  }
  singing_teacher?: {
    singing_type: string
    gender: string
    days_per_week: string
  }
  art_teacher?: {
    days_per_week: string
  }
  educational_coaching_centre?: {
    course_type: string
    days_per_week: string
  }
  computer_centre?: {
    course_type: string
    days_per_week: string
  }
  sports_coaching_centre?: {
    sports_type: string
    gender: string
    days_per_week: string
  }
  gym_yoga?: {
    training_type: string
    gender: string
    days_per_week: string
  }
  dance_school?: {
    dance_type: string
    gender: string
    days_per_week: string
  }
  music_school?: {
    music_type: string
    instrument: string
    days_per_week: string
  }
  singing_school?: {
    singing_type: string
    gender: string
    days_per_week: string
  }
  abriti_school?: {
    days_per_week: string
  }
  visual_arts?: {
    type: string
    gender: string
    days_per_week: string
  }
  abacus_centre?: {
    course_type: string
    gender: string
    days_per_week: string
  }
  others?: {
    profession_name: string
    gender: string
    days_per_week: string
  }
}

export interface Review {
  id: string
  tutor_id: string
  student_name: string | null
  rating: number
  review_text: string | null
  created_at: string
}

export interface Profile {
  id: string
  role: 'admin' | 'tutor'
  phone: string | null
  full_name: string | null
  avatar_url: string | null
  created_at: string
  updated_at: string
}

export interface TutorSearchParams {
  name?: string
  city?: string
  board?: string
  subject?: string
  classGrade?: string
  category?: string
  profession_type?: string
}

export interface AdminStats {
  total: number
  active: number
  premium: number
  avg_rating: number
  referrals: number
  paid: number
}
