'use client'

import { useState } from 'react'
import { createClient } from '@/lib/supabase/client'
import { Star, Send } from 'lucide-react'
import { formatDate } from '@/lib/utils'
import toast from 'react-hot-toast'
import type { Review } from '@/types/tutor'

interface ReviewSectionProps {
  tutorId: string
  reviews: Review[]
}

export default function ReviewSection({ tutorId, reviews: initialReviews }: ReviewSectionProps) {
  const [reviews, setReviews] = useState(initialReviews)
  const [showForm, setShowForm] = useState(false)
  const [loading, setLoading] = useState(false)
  const [form, setForm] = useState({
    student_name: '',
    rating: 5,
    review_text: '',
  })

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    setLoading(true)

    const supabase = createClient()
    const { data, error } = await supabase
      .from('reviews')
      .insert({
        tutor_id: tutorId,
        student_name: form.student_name,
        rating: form.rating,
        review_text: form.review_text,
      })
      .select()
      .single()

    if (error) {
      toast.error('Failed to submit review')
    } else {
      toast.success('Review submitted!')
      setReviews([data, ...reviews])
      setShowForm(false)
      setForm({ student_name: '', rating: 5, review_text: '' })
    }
    setLoading(false)
  }

  return (
    <div className="bg-white rounded-xl border border-gray-200 p-6">
      <div className="flex items-center justify-between mb-6">
        <h3 className="font-semibold text-gray-900 text-lg">Student Reviews ({reviews.length})</h3>
        <button
          onClick={() => setShowForm(!showForm)}
          className="px-4 py-2 bg-[#003153] text-white rounded-lg text-sm font-medium hover:bg-[#002040] transition-colors"
        >
          {showForm ? 'Cancel' : '+ Add Review'}
        </button>
      </div>

      {/* Review Form */}
      {showForm && (
        <form onSubmit={handleSubmit} className="bg-gray-50 rounded-xl p-5 mb-6 border border-gray-200">
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
            <input
              type="text"
              value={form.student_name}
              onChange={(e) => setForm({ ...form, student_name: e.target.value })}
              required
              className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-[#003153] focus:outline-none text-gray-900"
            />
          </div>

          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-2">Rating</label>
            <div className="flex gap-1">
              {[1, 2, 3, 4, 5].map((star) => (
                <button
                  key={star}
                  type="button"
                  onClick={() => setForm({ ...form, rating: star })}
                  className={`text-2xl transition-colors ${
                    star <= form.rating ? 'text-yellow-400' : 'text-gray-300'
                  }`}
                >
                  ★
                </button>
              ))}
            </div>
          </div>

          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
            <textarea
              value={form.review_text}
              onChange={(e) => setForm({ ...form, review_text: e.target.value })}
              rows={3}
              required
              placeholder="Share your experience..."
              className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-[#003153] focus:outline-none text-gray-900 resize-none"
            />
          </div>

          <button
            type="submit"
            disabled={loading}
            className="flex items-center gap-2 px-5 py-2.5 bg-[#003153] text-white rounded-lg font-medium hover:bg-[#002040] disabled:opacity-50 transition-colors"
          >
            <Send className="w-4 h-4" />
            {loading ? 'Submitting...' : 'Submit Review'}
          </button>
        </form>
      )}

      {/* Reviews List */}
      <div className="space-y-4 max-h-[500px] overflow-y-auto">
        {reviews.length > 0 ? (
          reviews.map((review) => (
            <div key={review.id} className="border border-gray-100 rounded-xl p-4">
              <div className="flex items-start justify-between mb-2">
                <div>
                  <p className="font-semibold text-gray-900">{review.student_name}</p>
                  <p className="text-yellow-400 text-sm">{'★'.repeat(review.rating)}{'☆'.repeat(5 - review.rating)}</p>
                </div>
                <span className="text-gray-400 text-xs">{formatDate(review.created_at)}</span>
              </div>
              <p className="text-gray-600 text-sm leading-relaxed">{review.review_text}</p>
            </div>
          ))
        ) : (
          <p className="text-gray-400 text-center py-8">No reviews yet. Be the first to review!</p>
        )}
      </div>
    </div>
  )
}
