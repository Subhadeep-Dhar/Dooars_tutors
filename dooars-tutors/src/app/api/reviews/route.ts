import { NextRequest, NextResponse } from 'next/server'
import { createClient } from '@/lib/supabase/server'

export async function POST(request: NextRequest) {
  try {
    const body = await request.json()
    const { tutor_id, student_name, rating, review_text } = body

    if (!tutor_id || !student_name || !rating || !review_text) {
      return NextResponse.json({ error: 'All fields are required' }, { status: 400 })
    }

    const supabase = await createClient()

    const { data, error } = await supabase
      .from('reviews')
      .insert({
        tutor_id,
        student_name,
        rating: Math.min(5, Math.max(1, parseInt(rating))),
        review_text,
      })
      .select()
      .single()

    if (error) {
      return NextResponse.json({ error: error.message }, { status: 500 })
    }

    return NextResponse.json({ success: true, review: data })
  } catch (err) {
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 })
  }
}
