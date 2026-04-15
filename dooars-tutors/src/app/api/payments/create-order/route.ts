import { NextRequest, NextResponse } from 'next/server'
import { razorpay, REGISTRATION_FEE, CURRENCY } from '@/lib/razorpay'
import { createClient } from '@/lib/supabase/server'

export async function POST(request: NextRequest) {
  try {
    const { tutor_id } = await request.json()

    if (!tutor_id) {
      return NextResponse.json({ error: 'tutor_id is required' }, { status: 400 })
    }

    const supabase = await createClient()

    // Verify tutor exists
    const { data: tutor, error } = await supabase
      .from('tutors')
      .select('id, name, email, phone')
      .eq('id', tutor_id)
      .single()

    if (error || !tutor) {
      return NextResponse.json({ error: 'Tutor not found' }, { status: 404 })
    }

    // Create Razorpay order
    const order = await razorpay.orders.create({
      amount: REGISTRATION_FEE * 100, // amount in paise
      currency: CURRENCY,
      receipt: `TUTOR_REG_${tutor_id}_${Date.now()}`,
      notes: {
        tutor_id,
        tutor_name: tutor.name,
        registration_fee: String(REGISTRATION_FEE),
      },
    })

    return NextResponse.json({
      order_id: order.id,
      amount: order.amount,
      currency: order.currency,
      key_id: process.env.NEXT_PUBLIC_RAZORPAY_KEY_ID,
    })
  } catch (err) {
    console.error('Create order error:', err)
    return NextResponse.json({ error: 'Failed to create order' }, { status: 500 })
  }
}
