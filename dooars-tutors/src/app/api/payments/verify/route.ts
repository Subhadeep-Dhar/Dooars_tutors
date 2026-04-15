import { NextRequest, NextResponse } from 'next/server'
import crypto from 'crypto'
import { createClient } from '@/lib/supabase/server'

export async function POST(request: NextRequest) {
  try {
    const { payment_id, order_id, signature, tutor_id, amount } = await request.json()

    if (!payment_id || !order_id || !signature || !tutor_id) {
      return NextResponse.json({ error: 'Missing required fields' }, { status: 400 })
    }

    // Verify Razorpay signature
    const body = order_id + '|' + payment_id
    const expectedSignature = crypto
      .createHmac('sha256', process.env.RAZORPAY_KEY_SECRET!)
      .update(body)
      .digest('hex')

    if (expectedSignature !== signature) {
      return NextResponse.json({ error: 'Invalid payment signature' }, { status: 400 })
    }

    // Update tutor payment status in DB
    const supabase = await createClient()
    const { error } = await supabase
      .from('tutors')
      .update({
        payment_status: 'paid',
        payment_id,
        payment_amount: amount,
        order_id,
        payment_date: new Date().toISOString(),
      })
      .eq('id', tutor_id)

    if (error) {
      console.error('DB update error:', error)
      return NextResponse.json({ error: 'Failed to update payment status' }, { status: 500 })
    }

    return NextResponse.json({ success: true, payment_id })
  } catch (err) {
    console.error('Payment verify error:', err)
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 })
  }
}
