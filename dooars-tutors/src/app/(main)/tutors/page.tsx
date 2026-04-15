import { createClient } from '@/lib/supabase/server'
import TutorCard from '@/components/tutors/TutorCard'
import { Search, SlidersHorizontal } from 'lucide-react'

export const metadata = {
  title: 'Find Tutors',
  description: 'Browse and search for tutors, coaches, and training centres in the Dooars region.',
}

export default async function TutorsPage({
  searchParams,
}: {
  searchParams: Promise<{ q?: string; city?: string; category?: string }>
}) {
  const params = await searchParams
  const supabase = await createClient()

  let query = supabase
    .from('tutors')
    .select('*')
    .eq('status', 'active')
    .order('rating', { ascending: false })

  if (params.q) {
    query = query.or(
      `name.ilike.%${params.q}%,subjects.ilike.%${params.q}%,profession.ilike.%${params.q}%,city.ilike.%${params.q}%`
    )
  }

  if (params.city) {
    query = query.ilike('city', `%${params.city}%`)
  }

  const { data: tutors, error } = await query.limit(50)

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Search Header */}
      <div className="bg-gradient-to-r from-[#003153] to-[#005a99] py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-white mb-6">Find Tutors</h1>

          <form className="flex flex-col sm:flex-row gap-3 max-w-3xl">
            <div className="flex-1 relative">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input
                name="q"
                type="text"
                defaultValue={params.q}
                placeholder="Search by name, subject, city..."
                className="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white text-gray-900 placeholder:text-gray-400 outline-none focus:ring-4 focus:ring-white/30"
              />
            </div>
            <input
              name="city"
              type="text"
              defaultValue={params.city}
              placeholder="City"
              className="px-4 py-3.5 rounded-xl bg-white text-gray-900 placeholder:text-gray-400 outline-none w-full sm:w-40"
            />
            <button
              type="submit"
              className="px-6 py-3.5 bg-emerald-500 text-white rounded-xl font-semibold hover:bg-emerald-600 transition-colors flex items-center justify-center gap-2"
            >
              <Search className="w-5 h-5" />
              Search
            </button>
          </form>
        </div>
      </div>

      {/* Results */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {params.q && (
          <p className="text-gray-600 mb-6">
            Showing results for &ldquo;<span className="font-semibold text-gray-900">{params.q}</span>&rdquo;
            {tutors && <span className="text-gray-400"> · {tutors.length} found</span>}
          </p>
        )}

        {tutors && tutors.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {tutors.map((tutor) => (
              <TutorCard key={tutor.id} tutor={tutor} />
            ))}
          </div>
        ) : (
          <div className="text-center py-20">
            <div className="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
              <Search className="w-8 h-8 text-gray-400" />
            </div>
            <h3 className="text-xl font-semibold text-gray-700 mb-2">No tutors found</h3>
            <p className="text-gray-500">Try adjusting your search or browse our categories</p>
          </div>
        )}
      </div>
    </div>
  )
}
