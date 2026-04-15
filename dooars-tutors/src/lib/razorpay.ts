import Razorpay from 'razorpay'

export const razorpay = new Razorpay({
  key_id: process.env.RAZORPAY_KEY_ID!,
  key_secret: process.env.RAZORPAY_KEY_SECRET!,
})

export const REGISTRATION_FEE = parseInt(process.env.REGISTRATION_FEE || '500', 10)
export const CURRENCY = 'INR'
export const COMPANY_NAME = 'DooarsTutors'
