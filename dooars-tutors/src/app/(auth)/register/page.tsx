'use client'

import { useState } from 'react'
import { createClient } from '@/lib/supabase/client'
import { useRouter } from 'next/navigation'
import Link from 'next/link'
import { Eye, EyeOff, UserPlus } from 'lucide-react'
import toast from 'react-hot-toast'

export default function RegisterPage() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    password: '',
    confirmPassword: '',
    userType: 'individual' as 'individual' | 'organisation',
  })
  const [showPassword, setShowPassword] = useState(false)
  const [loading, setLoading] = useState(false)
  const router = useRouter()
  const supabase = createClient()

  function updateField(field: string, value: string) {
    setFormData((prev) => ({ ...prev, [field]: value }))
  }

  async function handleRegister(e: React.FormEvent) {
    e.preventDefault()

    if (formData.password !== formData.confirmPassword) {
      toast.error('Passwords do not match')
      return
    }

    if (formData.password.length < 6) {
      toast.error('Password must be at least 6 characters')
      return
    }

    setLoading(true)

    // 1. Create auth user
    const { data: authData, error: authError } = await supabase.auth.signUp({
      email: formData.email,
      password: formData.password,
      options: {
        data: {
          full_name: formData.name,
          phone: formData.phone,
        },
        emailRedirectTo: `${window.location.origin}/api/auth/callback`,
      },
    })

    if (authError) {
      toast.error(authError.message)
      setLoading(false)
      return
    }

    if (authData.user) {
      // 2. Create profile
      await supabase.from('profiles').upsert({
        id: authData.user.id,
        role: 'tutor',
        phone: formData.phone,
        full_name: formData.name,
      })

      // 3. Create tutor record
      await supabase.from('tutors').insert({
        user_id: authData.user.id,
        name: formData.name,
        phone: formData.phone,
        email: formData.email,
        type: formData.userType,
        status: 'active',
        plan: 'free',
      })

      toast.success('Registration successful! Please check your email to verify your account.')
      router.push('/login')
    }

    setLoading(false)
  }

  return (
    <div className="w-full max-w-lg animate-slide-up">
      {/* Header */}
      <div className="text-center mb-8">
        <Link href="/" className="inline-block mb-6">
          <span className="text-3xl font-bold text-white">DooarsTutors</span>
        </Link>
        <h1 className="text-2xl font-bold text-white">Become a Tutor</h1>
        <p className="text-white/70 mt-2">Join our community of expert educators</p>
      </div>

      {/* Card */}
      <div className="bg-white rounded-2xl shadow-2xl p-8">
        <form onSubmit={handleRegister} className="space-y-5">
          {/* User Type */}
          <div>
            <label className="block text-sm font-semibold text-gray-700 mb-3">I am a</label>
            <div className="grid grid-cols-2 gap-3">
              {(['individual', 'organisation'] as const).map((type) => (
                <button
                  key={type}
                  type="button"
                  onClick={() => updateField('userType', type)}
                  className={`py-3 px-4 rounded-xl border-2 text-sm font-medium transition-all ${
                    formData.userType === type
                      ? 'border-[#003153] bg-[#003153] text-white'
                      : 'border-gray-200 text-gray-600 hover:border-gray-300'
                  }`}
                >
                  {type === 'individual' ? '👨‍🏫 Individual' : '🏢 Organisation'}
                </button>
              ))}
            </div>
          </div>

          {/* Name */}
          <div>
            <label htmlFor="name" className="block text-sm font-semibold text-gray-700 mb-2">
              {formData.userType === 'individual' ? 'Full Name' : 'Organisation Name'}
            </label>
            <input
              id="name"
              type="text"
              value={formData.name}
              onChange={(e) => updateField('name', e.target.value)}
              placeholder={formData.userType === 'individual' ? 'John Doe' : 'Academy Name'}
              required
              className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#003153] focus:ring-0 outline-none transition-colors text-gray-900"
            />
          </div>

          {/* Phone & Email */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label htmlFor="phone" className="block text-sm font-semibold text-gray-700 mb-2">
                Phone Number
              </label>
              <input
                id="phone"
                type="tel"
                value={formData.phone}
                onChange={(e) => updateField('phone', e.target.value)}
                placeholder="9876543210"
                required
                className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#003153] focus:ring-0 outline-none transition-colors text-gray-900"
              />
            </div>
            <div>
              <label htmlFor="email" className="block text-sm font-semibold text-gray-700 mb-2">
                Email Address
              </label>
              <input
                id="email"
                type="email"
                value={formData.email}
                onChange={(e) => updateField('email', e.target.value)}
                placeholder="you@example.com"
                required
                className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#003153] focus:ring-0 outline-none transition-colors text-gray-900"
              />
            </div>
          </div>

          {/* Password */}
          <div>
            <label htmlFor="reg-password" className="block text-sm font-semibold text-gray-700 mb-2">
              Password
            </label>
            <div className="relative">
              <input
                id="reg-password"
                type={showPassword ? 'text' : 'password'}
                value={formData.password}
                onChange={(e) => updateField('password', e.target.value)}
                placeholder="Min. 6 characters"
                required
                className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#003153] focus:ring-0 outline-none transition-colors text-gray-900 pr-12"
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
              </button>
            </div>
          </div>

          {/* Confirm Password */}
          <div>
            <label htmlFor="confirm-password" className="block text-sm font-semibold text-gray-700 mb-2">
              Confirm Password
            </label>
            <input
              id="confirm-password"
              type="password"
              value={formData.confirmPassword}
              onChange={(e) => updateField('confirmPassword', e.target.value)}
              placeholder="••••••••"
              required
              className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#003153] focus:ring-0 outline-none transition-colors text-gray-900"
            />
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full flex items-center justify-center gap-2 bg-[#003153] text-white py-3.5 rounded-xl font-semibold hover:bg-[#002040] transition-all disabled:opacity-50 shadow-lg hover:shadow-xl"
          >
            {loading ? (
              <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
            ) : (
              <>
                <UserPlus className="w-5 h-5" />
                Create Account
              </>
            )}
          </button>
        </form>

        <div className="mt-6 pt-6 border-t border-gray-100 text-center">
          <p className="text-sm text-gray-500">
            Already have an account?{' '}
            <Link href="/login" className="text-[#003153] font-semibold hover:underline">
              Sign In
            </Link>
          </p>
        </div>
      </div>
    </div>
  )
}
