import Link from 'next/link'
import { CheckCircle, ArrowRight } from 'lucide-react'

export const metadata = { title: 'Payment Successful' }

export default async function PaymentSuccessPage({
  searchParams,
}: {
  searchParams: Promise<{ tutor_id?: string; payment_id?: string }>
}) {
  const params = await searchParams

  return (
    <div className="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl shadow-xl p-10 max-w-md w-full text-center animate-slide-up">
        <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <CheckCircle className="w-10 h-10 text-green-600" />
        </div>

        <h1 className="text-2xl font-bold text-gray-900 mb-3">Registration Successful!</h1>
        <p className="text-gray-500 mb-6">
          Welcome to DooarsTutors! Your registration has been completed successfully.
        </p>

        {params.payment_id && (
          <div className="bg-gray-50 rounded-xl p-4 mb-6 text-left">
            <h3 className="text-sm font-semibold text-gray-700 mb-2">Payment Details</h3>
            <p className="text-sm text-gray-600">
              <span className="font-medium">Payment ID:</span> {params.payment_id}
            </p>
          </div>
        )}

        <p className="text-sm text-gray-500 mb-8">
          You will receive a confirmation email shortly with your login details.
        </p>

        <div className="flex flex-col gap-3">
          <Link
            href="/dashboard"
            className="flex items-center justify-center gap-2 px-6 py-3.5 bg-[#003153] text-white rounded-xl font-semibold hover:bg-[#002040] transition-colors"
          >
            Go to Dashboard <ArrowRight className="w-4 h-4" />
          </Link>
          <Link
            href="/"
            className="px-6 py-3 text-gray-600 font-medium hover:text-gray-900 transition-colors"
          >
            Back to Home
          </Link>
        </div>
      </div>
    </div>
  )
}
