import { z } from 'zod'

export const loginSchema = z.object({
  email: z.string().email('Invalid email address'),
  password: z.string().min(6, 'Password must be at least 6 characters'),
})

export const registerSchema = z.object({
  email: z.string().email('Invalid email address'),
  password: z.string().min(6, 'Password must be at least 6 characters'),
  name: z.string().min(2, 'Name must be at least 2 characters'),
  phone: z.string().min(10, 'Phone number must be at least 10 digits'),
})

export const tutorProfileSchema = z.object({
  name: z.string().min(2, 'Name is required'),
  phone: z.string().min(10, 'Valid phone number required'),
  email: z.string().email('Invalid email').optional().or(z.literal('')),
  experience: z.string().optional(),
  city: z.string().optional(),
  address: z.string().optional(),
  latitude: z.number().optional(),
  longitude: z.number().optional(),
})

export const reviewSchema = z.object({
  student_name: z.string().min(2, 'Name is required'),
  rating: z.number().min(1).max(5),
  review_text: z.string().min(10, 'Review must be at least 10 characters'),
})

export const searchSchema = z.object({
  name: z.string().optional(),
  city: z.string().optional(),
  board: z.string().optional(),
  subject: z.string().optional(),
  classGrade: z.string().optional(),
  category: z.string().optional(),
})

export type LoginFormData = z.infer<typeof loginSchema>
export type RegisterFormData = z.infer<typeof registerSchema>
export type TutorProfileFormData = z.infer<typeof tutorProfileSchema>
export type ReviewFormData = z.infer<typeof reviewSchema>
export type SearchFormData = z.infer<typeof searchSchema>
