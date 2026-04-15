import Link from 'next/link'
import { Star, MapPin, Phone } from 'lucide-react'
import { getInitials } from '@/lib/utils'
import type { Tutor } from '@/types/tutor'

interface TutorCardProps {
  tutor: Tutor
}

export default function TutorCard({ tutor }: TutorCardProps) {
  const initials = getInitials(tutor.name || 'U')
  const professions = tutor.profession?.split(',').map((p) => p.trim()) || []

  return (
    <Link
      href={`/tutors/${tutor.id}`}
      className="group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden border border-gray-100"
    >
      {/* Header with gradient */}
      <div className="bg-gradient-to-r from-[#003153] to-[#005a99] p-6 relative">
        <div className="flex items-center gap-4">
          {/* Avatar */}
          <div className="w-16 h-16 rounded-full bg-white/20 border-2 border-white/30 flex items-center justify-center text-white font-bold text-xl flex-shrink-0 overflow-hidden">
            {tutor.profile_picture ? (
              <img
                src={tutor.profile_picture}
                alt={tutor.name}
                className="w-full h-full object-cover rounded-full"
              />
            ) : (
              initials
            )}
          </div>
          <div className="min-w-0">
            <h3 className="text-white font-semibold text-lg truncate">{tutor.name}</h3>
            <div className="flex items-center gap-1.5 mt-1">
              <div className="flex items-center gap-1">
                <Star className="w-4 h-4 text-yellow-400 fill-yellow-400" />
                <span className="text-white/90 text-sm font-medium">{tutor.rating || '0.0'}</span>
              </div>
              <span className="text-white/50 text-sm">({tutor.rating_count || 0} ratings)</span>
            </div>
          </div>
        </div>
      </div>

      {/* Body */}
      <div className="p-5">
        {/* Location */}
        {tutor.city && (
          <div className="flex items-center gap-2 text-gray-500 text-sm mb-3">
            <MapPin className="w-4 h-4 flex-shrink-0" />
            <span className="truncate">{tutor.city}</span>
          </div>
        )}

        {/* Experience */}
        {tutor.experience && (
          <div className="text-sm text-gray-600 mb-3">
            <span className="font-medium text-gray-700">Experience:</span> {tutor.experience} years
          </div>
        )}

        {/* Professions as tags */}
        {professions.length > 0 && (
          <div className="flex flex-wrap gap-1.5 mb-3">
            {professions.slice(0, 3).map((prof) => (
              <span
                key={prof}
                className="px-2.5 py-1 bg-[#003153]/10 text-[#003153] rounded-full text-xs font-medium"
              >
                {prof}
              </span>
            ))}
            {professions.length > 3 && (
              <span className="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">
                +{professions.length - 3} more
              </span>
            )}
          </div>
        )}

        {/* Type badge */}
        <div className="flex items-center justify-between pt-3 border-t border-gray-100">
          <span
            className={`px-3 py-1 rounded-full text-xs font-semibold ${
              tutor.type === 'organisation'
                ? 'bg-purple-100 text-purple-700'
                : 'bg-blue-100 text-blue-700'
            }`}
          >
            {tutor.type === 'organisation' ? '🏢 Organisation' : '👨‍🏫 Individual'}
          </span>
          <span className="text-sm text-[#003153] font-semibold group-hover:underline">
            View Profile →
          </span>
        </div>
      </div>
    </Link>
  )
}
