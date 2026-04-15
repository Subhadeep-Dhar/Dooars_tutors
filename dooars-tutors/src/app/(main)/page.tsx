import Link from 'next/link'
import { createClient } from '@/lib/supabase/server'
import { CATEGORIES } from '@/constants/categories'
import { Search, ArrowRight, Star, MapPin, ChevronRight } from 'lucide-react'
import TutorCard from '@/components/tutors/TutorCard'

export default async function HomePage() {
  const supabase = await createClient()

  // Fetch featured tutors
  const { data: tutors } = await supabase
    .from('tutors')
    .select('*')
    .eq('status', 'active')
    .order('rating', { ascending: false })
    .limit(6)

  return (
    <div>
      {/* Hero Section */}
      <section className="relative bg-gradient-to-br from-[#8aafdf] via-[#6b9fd4] to-[#003153] overflow-hidden">
        {/* Decorative blobs */}
        <div className="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl" />
        <div className="absolute bottom-10 right-10 w-96 h-96 bg-[#003153]/20 rounded-full blur-3xl" />

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-36">
          <div className="text-center animate-slide-up">
            <h1 className="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
              Find Your Perfect
              <span className="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 to-yellow-400">
                Tutor in Dooars
              </span>
            </h1>
            <p className="text-lg md:text-xl text-white/80 max-w-2xl mx-auto mb-10">
              Connect with expert educators for academics, arts, music, dance, sports and more.
              Your learning journey starts here.
            </p>

            {/* Search Bar */}
            <div className="max-w-2xl mx-auto">
              <form action="/tutors" method="GET" className="flex flex-col sm:flex-row gap-3">
                <div className="flex-1 relative">
                  <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                  <input
                    name="q"
                    type="text"
                    placeholder="Search by name, subject, or city..."
                    className="w-full pl-12 pr-4 py-4 rounded-2xl bg-white/95 backdrop-blur-sm text-gray-900 placeholder:text-gray-400 outline-none focus:ring-4 focus:ring-white/30 transition-all text-base"
                  />
                </div>
                <button
                  type="submit"
                  className="px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-2xl font-semibold hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2"
                >
                  <Search className="w-5 h-5" />
                  Search
                </button>
              </form>
            </div>

            {/* Stats */}
            <div className="flex flex-wrap justify-center gap-8 mt-14">
              {[
                { label: 'Registered Tutors', value: '500+' },
                { label: 'Categories', value: '7+' },
                { label: 'Happy Students', value: '1000+' },
              ].map((stat) => (
                <div key={stat.label} className="text-center">
                  <div className="text-3xl font-bold text-white">{stat.value}</div>
                  <div className="text-white/60 text-sm mt-1">{stat.label}</div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Categories Section */}
      <section className="bg-[#003153] py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="text-3xl md:text-4xl font-bold text-white mb-4">
              Explore Categories
            </h2>
            <p className="text-white/60 text-lg max-w-2xl mx-auto">
              Find the right educator by browsing our diverse categories
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            {CATEGORIES.map((category, index) => (
              <Link
                key={category.slug}
                href={`/categories/${category.slug}`}
                className="group relative bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                style={{ animationDelay: `${index * 100}ms` }}
              >
                <div className={`w-14 h-14 rounded-xl bg-gradient-to-br ${category.gradient} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform`}>
                  <category.icon className="w-7 h-7 text-white" />
                </div>
                <h3 className="text-white font-semibold text-lg mb-2">{category.name}</h3>
                <p className="text-white/60 text-sm leading-relaxed">{category.description}</p>
                <ChevronRight className="absolute top-6 right-6 w-5 h-5 text-white/30 group-hover:text-white/70 group-hover:translate-x-1 transition-all" />
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Tutors */}
      <section className="bg-gradient-to-b from-[#003153] to-[#8aafdf] py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="text-3xl md:text-4xl font-bold text-white mb-4">
              Featured Tutors
            </h2>
            <p className="text-white/70 text-lg">
              Meet our top-rated educators
            </p>
          </div>

          {tutors && tutors.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {tutors.map((tutor) => (
                <TutorCard key={tutor.id} tutor={tutor} />
              ))}
            </div>
          ) : (
            <div className="text-center py-16">
              <p className="text-white/60 text-lg">No tutors available yet. Be the first to register!</p>
              <Link
                href="/register"
                className="inline-flex items-center gap-2 mt-6 px-8 py-3 bg-white text-[#003153] rounded-full font-semibold hover:bg-gray-100 transition-colors"
              >
                Register Now <ArrowRight className="w-5 h-5" />
              </Link>
            </div>
          )}

          <div className="text-center mt-10">
            <Link
              href="/tutors"
              className="inline-flex items-center gap-2 px-8 py-3.5 bg-white/10 border border-white/20 text-white rounded-full font-semibold hover:bg-white/20 transition-all"
            >
              View All Tutors <ArrowRight className="w-5 h-5" />
            </Link>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="bg-[#8aafdf] py-20">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h2 className="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Share Your Knowledge?
          </h2>
          <p className="text-white/80 text-lg mb-10 max-w-2xl mx-auto">
            Join DooarsTutors today and connect with students eager to learn. 
            Register as a tutor and grow your teaching career.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/register"
              className="px-8 py-4 bg-[#003153] text-white rounded-2xl font-semibold hover:bg-[#002040] transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5"
            >
              Register as Tutor
            </Link>
            <Link
              href="/about"
              className="px-8 py-4 bg-white/20 backdrop-blur-sm text-white border border-white/30 rounded-2xl font-semibold hover:bg-white/30 transition-all"
            >
              Learn More
            </Link>
          </div>
        </div>
      </section>
    </div>
  )
}
