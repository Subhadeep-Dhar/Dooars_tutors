'use client'

import { useState, useEffect } from 'react'
import { createClient } from '@/lib/supabase/client'
import { useRouter } from 'next/navigation'
import { Save, Loader2 } from 'lucide-react'
import toast from 'react-hot-toast'

export default function ProfileEditPage() {
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [form, setForm] = useState({
    name: '', email: '', experience: '', city: '', address: '',
    latitude: '', longitude: '',
  })
  const router = useRouter()
  const supabase = createClient()

  useEffect(() => {
    async function loadProfile() {
      const { data: { user } } = await supabase.auth.getUser()
      if (!user) { router.push('/login'); return }

      const { data: tutor } = await supabase
        .from('tutors')
        .select('*')
        .eq('user_id', user.id)
        .single()

      if (tutor) {
        setForm({
          name: tutor.name || '',
          email: tutor.email || '',
          experience: tutor.experience || '',
          city: tutor.city || '',
          address: tutor.address || '',
          latitude: tutor.latitude?.toString() || '',
          longitude: tutor.longitude?.toString() || '',
        })
      }
      setLoading(false)
    }
    loadProfile()
  }, []) // eslint-disable-line react-hooks/exhaustive-deps

  async function handleSave(e: React.FormEvent) {
    e.preventDefault()
    setSaving(true)

    const { data: { user } } = await supabase.auth.getUser()
    if (!user) return

    const { error } = await supabase
      .from('tutors')
      .update({
        name: form.name,
        email: form.email,
        experience: form.experience,
        city: form.city,
        address: form.address,
        latitude: form.latitude ? parseFloat(form.latitude) : null,
        longitude: form.longitude ? parseFloat(form.longitude) : null,
      })
      .eq('user_id', user.id)

    if (error) {
      toast.error('Failed to update profile')
    } else {
      toast.success('Profile updated successfully!')
      router.refresh()
    }
    setSaving(false)
  }

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Loader2 className="w-8 h-8 animate-spin text-[#003153]" />
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gray-100">
      <div className="bg-[#003153] text-white py-8">
        <div className="max-w-3xl mx-auto px-4">
          <h1 className="text-2xl font-bold">Edit Profile</h1>
          <p className="text-white/70 mt-1">Update your information</p>
        </div>
      </div>

      <div className="max-w-3xl mx-auto px-4 py-8">
        <form onSubmit={handleSave} className="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-5">
          <FormField label="Full Name" value={form.name} onChange={(v) => setForm({ ...form, name: v })} required />
          <FormField label="Email" type="email" value={form.email} onChange={(v) => setForm({ ...form, email: v })} />
          <FormField label="Experience (years)" value={form.experience} onChange={(v) => setForm({ ...form, experience: v })} />
          <FormField label="City" value={form.city} onChange={(v) => setForm({ ...form, city: v })} />

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea
              value={form.address}
              onChange={(e) => setForm({ ...form, address: e.target.value })}
              rows={3}
              className="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:border-[#003153] focus:outline-none text-gray-900 resize-none"
            />
          </div>

          <div className="grid grid-cols-2 gap-4">
            <FormField label="Latitude" value={form.latitude} onChange={(v) => setForm({ ...form, latitude: v })} />
            <FormField label="Longitude" value={form.longitude} onChange={(v) => setForm({ ...form, longitude: v })} />
          </div>

          <div className="flex gap-3 pt-3">
            <button
              type="submit"
              disabled={saving}
              className="flex items-center gap-2 px-6 py-3 bg-[#003153] text-white rounded-xl font-semibold hover:bg-[#002040] disabled:opacity-50 transition-all"
            >
              {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
              {saving ? 'Saving...' : 'Save Changes'}
            </button>
            <button
              type="button"
              onClick={() => router.push('/dashboard')}
              className="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}

function FormField({ label, value, onChange, type = 'text', required = false }: {
  label: string; value: string; onChange: (v: string) => void; type?: string; required?: boolean
}) {
  return (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
      <input
        type={type}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        required={required}
        className="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:border-[#003153] focus:outline-none text-gray-900"
      />
    </div>
  )
}
