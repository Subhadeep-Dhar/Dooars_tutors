import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'
import { AuthProvider } from '@/components/auth/AuthProvider'
import { Toaster } from 'react-hot-toast'

const inter = Inter({
  subsets: ['latin'],
  display: 'swap',
  variable: '--font-inter',
})

export const metadata: Metadata = {
  title: {
    default: 'DooarsTutors - Find Your Perfect Tutor',
    template: '%s | DooarsTutors',
  },
  description:
    'Find the best tutors, coaches, and training centres in the Dooars region. Expert educators for academics, arts, music, dance, sports, and more.',
  keywords: ['tutor', 'Dooars', 'education', 'coaching', 'teacher', 'music', 'dance', 'sports'],
  openGraph: {
    title: 'DooarsTutors - Find Your Perfect Tutor',
    description: 'Connect with expert educators in the Dooars region.',
    type: 'website',
  },
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html lang="en" className={inter.variable}>
      <body className="font-sans antialiased">
        <AuthProvider>
          {children}
          <Toaster
            position="top-right"
            toastOptions={{
              duration: 4000,
              style: {
                background: '#1f2937',
                color: '#fff',
                borderRadius: '12px',
              },
            }}
          />
        </AuthProvider>
      </body>
    </html>
  )
}
