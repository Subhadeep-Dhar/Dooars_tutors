import { createClient } from '@/lib/supabase/server'
import { notFound } from 'next/navigation'
import Link from 'next/link'
import { Star, MapPin, Phone, Mail, Clock, BookOpen, GraduationCap, ArrowLeft, MessageCircle } from 'lucide-react'
import { getInitials, formatDate } from '@/lib/utils'
import ReviewSection from '@/components/tutors/ReviewSection'

export async function generateMetadata({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params
  const supabase = await createClient()
  const { data: tutor } = await supabase.from('tutors').select('name, city').eq('id', id).single()
  return {
    title: tutor ? `${tutor.name} - Tutor Profile` : 'Tutor Profile',
    description: tutor ? `View ${tutor.name}'s profile on DooarsTutors. Located in ${tutor.city || 'Dooars'}.` : '',
  }
}

export default async function TutorProfilePage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params
  const supabase = await createClient()

  const { data: tutor } = await supabase
    .from('tutors')
    .select('*')
    .eq('id', id)
    .single()

  if (!tutor) notFound()

  const { data: reviews } = await supabase
    .from('reviews')
    .select('*')
    .eq('tutor_id', id)
    .order('created_at', { ascending: false })
    .limit(20)

  // Increment view count
  await supabase.from('tutor_views').insert({ tutor_id: id })

  const initials = getInitials(tutor.name)
  const professions = tutor.profession?.split(',').map((p: string) => p.trim()) || []
  const professionDetails = tutor.profession_details || {}
  const fullStars = Math.floor(tutor.rating || 0)
  const starsHtml = '★'.repeat(fullStars) + '☆'.repeat(5 - fullStars)

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Header */}
      <div className="bg-[#003153] text-white">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
          <Link
            href="/tutors"
            className="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm mb-8 transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to Search
          </Link>

          <div className="flex flex-col md:flex-row items-center md:items-start gap-8">
            {/* Avatar */}
            <div className="w-32 h-32 rounded-full bg-white/10 border-3 border-white/20 flex items-center justify-center text-4xl font-bold flex-shrink-0 overflow-hidden">
              {tutor.profile_picture ? (
                <img src={tutor.profile_picture} alt={tutor.name} className="w-full h-full object-cover rounded-full" />
              ) : (
                initials
              )}
            </div>

            <div className="text-center md:text-left">
              <h1 className="text-3xl md:text-4xl font-bold mb-2">{tutor.name}</h1>
              <p className="text-white/70 text-lg mb-4">
                {tutor.type === 'organisation' ? '🏢 Organisation' : '👨‍🏫 Professional Educator'}
              </p>
              <div className="flex items-center justify-center md:justify-start gap-3 mb-4">
                <span className="text-yellow-400 text-2xl tracking-wider">{starsHtml}</span>
                <span className="text-xl font-semibold">{tutor.rating}/5</span>
                <span className="text-white/60">({tutor.rating_count} ratings)</span>
              </div>
              {/* Contact Buttons */}
              <div className="flex flex-wrap gap-3 justify-center md:justify-start">
                {tutor.phone && (
                  <a
                    href={`tel:${tutor.phone}`}
                    className="flex items-center gap-2 px-5 py-2.5 bg-sky-500 text-white rounded-lg font-medium hover:bg-sky-600 transition-colors"
                  >
                    <Phone className="w-4 h-4" /> Call Now
                  </a>
                )}
                {tutor.phone && (
                  <a
                    href={`https://wa.me/${tutor.phone.replace(/\D/g, '')}`}
                    target="_blank"
                    rel="noopener"
                    className="flex items-center gap-2 px-5 py-2.5 bg-white text-[#003153] rounded-lg font-medium hover:bg-gray-100 transition-colors"
                  >
                    <MessageCircle className="w-4 h-4" /> WhatsApp
                  </a>
                )}
                {tutor.email && (
                  <a
                    href={`mailto:${tutor.email}`}
                    className="flex items-center gap-2 px-5 py-2.5 bg-white/10 border border-white/20 text-white rounded-lg font-medium hover:bg-white/20 transition-colors"
                  >
                    <Mail className="w-4 h-4" /> Email
                  </a>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Content */}
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left Column - Info Cards */}
          <div className="lg:col-span-2 space-y-6">
            {/* Info Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {tutor.city && (
                <InfoCard icon={MapPin} title="City / Town" value={tutor.city} />
              )}
              {tutor.experience && (
                <InfoCard icon={Clock} title="Experience" value={`${tutor.experience} Years`} />
              )}
              {tutor.teaching_preferences && (
                <InfoCard icon={BookOpen} title="Teaching Mode" value={tutor.teaching_preferences.replace('|', ', ')} />
              )}
              {professionDetails?.tutor?.boards && (
                <InfoCard icon={GraduationCap} title="Boards" value={professionDetails.tutor.boards} />
              )}
            </div>

            {/* Subjects */}
            {professionDetails?.tutor?.subjects && (
              <div className="bg-white rounded-xl border border-gray-200 p-6">
                <h3 className="font-semibold text-gray-900 text-lg mb-4 flex items-center gap-2">
                  <BookOpen className="w-5 h-5 text-[#003153]" /> Subjects
                </h3>
                <div className="flex flex-wrap gap-2">
                  {professionDetails.tutor.subjects.split(',').map((s: string) => (
                    <span key={s} className="px-3 py-1.5 bg-[#003153] text-white rounded-full text-sm font-medium">
                      {s.trim()}
                    </span>
                  ))}
                </div>
              </div>
            )}

            {/* Classes */}
            {professionDetails?.tutor?.classes && (
              <div className="bg-white rounded-xl border border-gray-200 p-6">
                <h3 className="font-semibold text-gray-900 text-lg mb-4 flex items-center gap-2">
                  <GraduationCap className="w-5 h-5 text-[#003153]" /> Classes
                </h3>
                <p className="text-gray-600">{professionDetails.tutor.classes}</p>
              </div>
            )}

            {/* Profession Tags */}
            {professions.length > 0 && (
              <div className="bg-white rounded-xl border border-gray-200 p-6">
                <h3 className="font-semibold text-gray-900 text-lg mb-4">Professions</h3>
                <div className="flex flex-wrap gap-2">
                  {professions.map((p: string) => (
                    <span key={p} className="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-sm font-medium border border-indigo-100">
                      {p}
                    </span>
                  ))}
                </div>
              </div>
            )}

            {/* Reviews */}
            <ReviewSection tutorId={tutor.id} reviews={reviews || []} />
          </div>

          {/* Right Sidebar */}
          <div className="space-y-6">
            {/* Quick Contact */}
            <div className="bg-[#003153] text-white rounded-xl p-6 text-center">
              <h3 className="font-semibold text-lg mb-2">Ready to Start Learning?</h3>
              <p className="text-white/70 text-sm mb-6">
                Contact {tutor.name.split(' ')[0]} to schedule your first lesson
              </p>
              {tutor.phone && (
                <a
                  href={`tel:${tutor.phone}`}
                  className="block w-full py-3 bg-sky-500 text-white rounded-lg font-medium hover:bg-sky-600 transition-colors mb-3"
                >
                  📞 Call Now
                </a>
              )}
              {tutor.phone && (
                <a
                  href={`https://wa.me/${tutor.phone.replace(/\D/g, '')}`}
                  target="_blank"
                  rel="noopener"
                  className="block w-full py-3 bg-white/10 border border-white/20 text-white rounded-lg font-medium hover:bg-white/20 transition-colors"
                >
                  💬 WhatsApp
                </a>
              )}
            </div>

            {/* Location */}
            {tutor.address && (
              <div className="bg-white rounded-xl border border-gray-200 p-6">
                <h3 className="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                  <MapPin className="w-5 h-5 text-[#003153]" /> Location
                </h3>
                <p className="text-gray-600 text-sm">{tutor.address}</p>
              </div>
            )}

            {/* Joined */}
            <div className="bg-white rounded-xl border border-gray-200 p-6">
              <p className="text-gray-500 text-sm">
                Joined: <span className="font-medium text-gray-700">{formatDate(tutor.created_at)}</span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

function InfoCard({ icon: Icon, title, value }: { icon: any; title: string; value: string }) {
  return (
    <div className="bg-white rounded-xl border border-gray-200 p-5 hover:border-gray-300 hover:shadow-sm transition-all">
      <h3 className="text-gray-500 text-sm font-medium mb-2 flex items-center gap-2">
        <Icon className="w-4 h-4 text-[#003153]" /> {title}
      </h3>
      <p className="text-gray-900 font-medium">{value}</p>
    </div>
  )
}
