import { createClient } from '@/lib/supabase/server'
import { redirect } from 'next/navigation'
import Link from 'next/link'
import { Users, UserCheck, Crown, Star, CreditCard, Share2, Search, Trash2, ChevronRight } from 'lucide-react'
import AdminTutorTable from '@/components/admin/AdminTutorTable'

export const metadata = { title: 'Admin Dashboard' }

export default async function AdminDashboardPage() {
  const supabase = await createClient()

  const { data: { user } } = await supabase.auth.getUser()
  if (!user) redirect('/login')

  const { data: profile } = await supabase.from('profiles').select('role').eq('id', user.id).single()
  if (profile?.role !== 'admin') redirect('/')

  // Fetch stats
  const { count: total } = await supabase.from('tutors').select('*', { count: 'exact', head: true })
  const { count: active } = await supabase.from('tutors').select('*', { count: 'exact', head: true }).eq('status', 'active')
  const { count: premium } = await supabase.from('tutors').select('*', { count: 'exact', head: true }).eq('plan', 'premium')
  const { count: paid } = await supabase.from('tutors').select('*', { count: 'exact', head: true }).eq('payment_status', 'paid')

  const { data: ratingData } = await supabase.from('tutors').select('rating').gt('rating', 0)
  const avgRating = ratingData && ratingData.length > 0
    ? (ratingData.reduce((sum, t) => sum + (t.rating || 0), 0) / ratingData.length).toFixed(1)
    : '0.0'

  // Fetch all tutors
  const { data: tutors } = await supabase
    .from('tutors')
    .select('id, name, email, phone, city, experience, rating, rating_count, plan, status, type, profession, payment_status, created_at')
    .order('created_at', { ascending: false })
    .limit(100)

  const stats = [
    { icon: Users, label: 'Total Tutors', value: total || 0, color: 'from-blue-500 to-indigo-600' },
    { icon: UserCheck, label: 'Active Tutors', value: active || 0, color: 'from-green-500 to-emerald-600' },
    { icon: Crown, label: 'Premium Plans', value: premium || 0, color: 'from-yellow-500 to-orange-500' },
    { icon: Star, label: 'Avg Rating', value: avgRating, color: 'from-purple-500 to-violet-600' },
    { icon: CreditCard, label: 'Paid Tutors', value: paid || 0, color: 'from-pink-500 to-rose-600' },
  ]

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Admin Header */}
      <div className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <Link href="/" className="text-xl font-bold text-[#003153]">DooarsTutors</Link>
              <span className="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">ADMIN</span>
            </div>
            <form action="/api/auth/signout" method="POST">
              <button className="text-sm text-gray-500 hover:text-gray-700">Logout</button>
            </form>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 className="text-2xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
        <p className="text-gray-500 mb-8">Comprehensive tutor management and analytics</p>

        {/* Stats */}
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-10">
          {stats.map((stat) => (
            <div key={stat.label} className={`bg-gradient-to-br ${stat.color} rounded-xl p-5 text-white relative overflow-hidden`}>
              <stat.icon className="w-8 h-8 opacity-20 absolute top-3 right-3" />
              <div className="text-2xl font-bold">{stat.value}</div>
              <div className="text-sm text-white/70 mt-1">{stat.label}</div>
            </div>
          ))}
        </div>

        {/* Tutor Table */}
        <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="p-6 border-b border-gray-200">
            <h2 className="text-lg font-semibold text-gray-900">Tutor Management</h2>
            <p className="text-gray-500 text-sm mt-1">{total || 0} total tutors</p>
          </div>
          <AdminTutorTable tutors={tutors || []} />
        </div>
      </div>
    </div>
  )
}
