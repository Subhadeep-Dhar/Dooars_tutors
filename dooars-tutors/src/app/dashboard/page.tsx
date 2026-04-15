import { createClient } from '@/lib/supabase/server'
import { redirect } from 'next/navigation'
import Link from 'next/link'
import { LayoutDashboard, Eye, Star, CreditCard, User, Settings, LogOut, MessageSquare, ChevronRight } from 'lucide-react'
import { formatDate } from '@/lib/utils'

export const metadata = { title: 'Dashboard' }

export default async function DashboardPage() {
  const supabase = await createClient()

  const { data: { user } } = await supabase.auth.getUser()
  if (!user) redirect('/login')

  // Get tutor data
  const { data: tutor } = await supabase
    .from('tutors')
    .select('*')
    .eq('user_id', user.id)
    .single()

  if (!tutor) redirect('/register')

  // Get visit count
  const { count: visitCount } = await supabase
    .from('tutor_views')
    .select('*', { count: 'exact', head: true })
    .eq('tutor_id', tutor.id)

  // Get review count
  const { count: reviewCount } = await supabase
    .from('reviews')
    .select('*', { count: 'exact', head: true })
    .eq('tutor_id', tutor.id)

  const initials = tutor.name?.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) || 'U'

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Header */}
      <div className="bg-[#003153] text-white">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-4">
              <Link href="/" className="text-xl font-bold">DooarsTutors</Link>
            </div>
            <form action="/api/auth/signout" method="POST">
              <button className="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-full text-sm hover:bg-white/20 transition-colors">
                <LogOut className="w-4 h-4" />
                Logout
              </button>
            </form>
          </div>
        </div>
      </div>

      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Welcome */}
        <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
          <div className="flex flex-col sm:flex-row items-center gap-5">
            <div className="w-20 h-20 rounded-full bg-[#003153] text-white flex items-center justify-center text-2xl font-bold flex-shrink-0 overflow-hidden">
              {tutor.profile_picture ? (
                <img src={tutor.profile_picture} alt={tutor.name} className="w-full h-full object-cover" />
              ) : (
                initials
              )}
            </div>
            <div className="text-center sm:text-left">
              <h1 className="text-2xl font-bold text-gray-900">Welcome, {tutor.name}!</h1>
              <p className="text-gray-500 mt-1">
                {tutor.type === 'organisation' ? '🏢 Organisation' : '👨‍🏫 Individual Tutor'} · 
                <span className={`ml-2 px-2 py-0.5 rounded-full text-xs font-medium ${
                  tutor.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                }`}>
                  {tutor.status}
                </span>
              </p>
            </div>
          </div>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <StatCard icon={Eye} label="Profile Views" value={visitCount || 0} color="from-blue-500 to-indigo-600" />
          <StatCard icon={Star} label="Rating" value={`${tutor.rating}/5`} color="from-yellow-500 to-orange-500" />
          <StatCard icon={MessageSquare} label="Reviews" value={reviewCount || 0} color="from-purple-500 to-violet-600" />
          <StatCard icon={CreditCard} label="Payment" value={tutor.payment_status === 'paid' ? 'Paid ✓' : 'Pending'} color="from-green-500 to-emerald-600" />
        </div>

        {/* Quick Actions */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
          <ActionCard
            href="/dashboard/profile"
            icon={User}
            title="Edit Profile"
            description="Update your information, subjects, and teaching preferences"
          />
          <ActionCard
            href={`/tutors/${tutor.id}`}
            icon={Eye}
            title="View Public Profile"
            description="See how students view your profile"
          />
        </div>

        {/* Profile Info */}
        <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Profile Details</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <ProfileField label="Phone" value={tutor.phone} />
            <ProfileField label="Email" value={tutor.email || 'Not set'} />
            <ProfileField label="City" value={tutor.city || 'Not set'} />
            <ProfileField label="Experience" value={tutor.experience ? `${tutor.experience} years` : 'Not set'} />
            <ProfileField label="Plan" value={tutor.plan} />
            <ProfileField label="Joined" value={formatDate(tutor.created_at)} />
            {tutor.referral_code && <ProfileField label="Referral Code" value={tutor.referral_code} />}
            {tutor.wallet_balance > 0 && <ProfileField label="Wallet" value={`₹${tutor.wallet_balance}`} />}
          </div>
        </div>
      </div>
    </div>
  )
}

function StatCard({ icon: Icon, label, value, color }: { icon: any; label: string; value: string | number; color: string }) {
  return (
    <div className={`bg-gradient-to-br ${color} rounded-xl p-5 text-white`}>
      <Icon className="w-6 h-6 mb-3 opacity-80" />
      <div className="text-2xl font-bold">{value}</div>
      <div className="text-sm text-white/70 mt-1">{label}</div>
    </div>
  )
}

function ActionCard({ href, icon: Icon, title, description }: { href: string; icon: any; title: string; description: string }) {
  return (
    <Link href={href} className="flex items-center gap-4 bg-white rounded-xl border border-gray-200 p-5 hover:border-[#003153] hover:shadow-md transition-all group">
      <div className="w-12 h-12 bg-[#003153]/10 rounded-xl flex items-center justify-center flex-shrink-0">
        <Icon className="w-6 h-6 text-[#003153]" />
      </div>
      <div className="flex-1 min-w-0">
        <h3 className="font-semibold text-gray-900">{title}</h3>
        <p className="text-gray-500 text-sm mt-0.5">{description}</p>
      </div>
      <ChevronRight className="w-5 h-5 text-gray-300 group-hover:text-[#003153] transition-colors" />
    </Link>
  )
}

function ProfileField({ label, value }: { label: string; value: string }) {
  return (
    <div className="bg-gray-50 rounded-lg p-3">
      <span className="text-xs font-medium text-gray-500 uppercase tracking-wide">{label}</span>
      <p className="text-gray-900 font-medium mt-0.5">{value}</p>
    </div>
  )
}
