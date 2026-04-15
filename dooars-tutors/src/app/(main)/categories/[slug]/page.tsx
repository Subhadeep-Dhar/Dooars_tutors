import { createClient } from '@/lib/supabase/server'
import { notFound } from 'next/navigation'
import { getCategoryBySlug, CATEGORIES } from '@/constants/categories'
import TutorCard from '@/components/tutors/TutorCard'
import Link from 'next/link'
import { ArrowLeft } from 'lucide-react'

export async function generateStaticParams() {
  return CATEGORIES.map((cat) => ({ slug: cat.slug }))
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params
  const category = getCategoryBySlug(slug)
  return {
    title: category ? category.name : 'Category',
    description: category?.description,
  }
}

export default async function CategoryPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params
  const category = getCategoryBySlug(slug)
  if (!category) notFound()

  const supabase = await createClient()

  // Build OR conditions for profession_details JSONB keys
  const orConditions = category.professionKeys
    .map((key) => `profession_details->>${key}.neq.null`)
    .join(',')

  // Use a simpler approach: filter by profession column
  const professionKeywords = category.professionKeys.map(k => 
    k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
  )

  let query = supabase
    .from('tutors')
    .select('*')
    .eq('status', 'active')

  // Filter by profession field matching any of the category's profession keywords
  if (professionKeywords.length > 0) {
    const orFilter = professionKeywords.map(k => `profession.ilike.%${k}%`).join(',')
    query = query.or(orFilter)
  }

  const { data: tutors } = await query
    .order('rating', { ascending: false })
    .limit(50)

  const Icon = category.icon

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className={`bg-gradient-to-r ${category.gradient} py-16`}>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <Link href="/" className="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm mb-6">
            <ArrowLeft className="w-4 h-4" /> Back to Home
          </Link>
          <div className="flex items-center gap-4">
            <div className="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
              <Icon className="w-8 h-8 text-white" />
            </div>
            <div>
              <h1 className="text-3xl md:text-4xl font-bold text-white">{category.name}</h1>
              <p className="text-white/70 mt-1">{category.description}</p>
            </div>
          </div>
        </div>
      </div>

      {/* Results */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <p className="text-gray-600 mb-6">{tutors?.length || 0} tutors found</p>

        {tutors && tutors.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {tutors.map((tutor) => (
              <TutorCard key={tutor.id} tutor={tutor} />
            ))}
          </div>
        ) : (
          <div className="text-center py-20">
            <p className="text-gray-500 text-lg">No tutors in this category yet.</p>
            <Link href="/register" className="inline-block mt-4 text-[#003153] font-semibold hover:underline">
              Be the first to register →
            </Link>
          </div>
        )}
      </div>
    </div>
  )
}
