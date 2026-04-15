import { NextRequest, NextResponse } from 'next/server'
import { createClient } from '@/lib/supabase/server'

export async function POST(request: NextRequest) {
  try {
    const body = await request.json()
    const { name, city, board, subject, classGrade, category } = body

    const supabase = await createClient()

    let query = supabase
      .from('tutors')
      .select('id, name, phone, email, experience, profession_details, city, address, rating, rating_count, profile_picture, profession, type')
      .eq('status', 'active')
      .not('profession_details', 'is', null)

    // Apply filters
    if (name) query = query.ilike('name', `%${name}%`)
    if (city) query = query.ilike('city', `%${city}%`)

    // JSONB filtering for tutor profession details
    if (board) {
      query = query.filter('profession_details->tutor->>boards', 'ilike', `%${board}%`)
    }
    if (subject) {
      query = query.filter('profession_details->tutor->>subjects', 'ilike', `%${subject}%`)
    }
    if (classGrade) {
      query = query.filter('profession_details->tutor->>classes', 'ilike', `%${classGrade}%`)
    }

    // Category-based filtering
    if (category) {
      query = query.ilike('profession', `%${category}%`)
    }

    const { data, error } = await query
      .order('rating', { ascending: false })
      .limit(50)

    if (error) {
      return NextResponse.json({ error: error.message }, { status: 500 })
    }

    return NextResponse.json({
      tutors: data || [],
      count: data?.length || 0,
    })
  } catch (err) {
    console.error('Search error:', err)
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 })
  }
}
