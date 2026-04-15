import Link from 'next/link'

export default function NotFound() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-[#8aafdf] via-[#6b9fd4] to-[#003153] flex items-center justify-center p-4">
      <div className="text-center animate-slide-up">
        <h1 className="text-8xl font-bold text-white/20 mb-4">404</h1>
        <h2 className="text-2xl font-bold text-white mb-4">Page Not Found</h2>
        <p className="text-white/70 mb-8 max-w-md mx-auto">
          The page you&apos;re looking for doesn&apos;t exist or has been moved.
        </p>
        <Link
          href="/"
          className="inline-block px-8 py-3.5 bg-white text-[#003153] rounded-xl font-semibold hover:bg-gray-100 transition-colors"
        >
          Go to Home
        </Link>
      </div>
    </div>
  )
}
