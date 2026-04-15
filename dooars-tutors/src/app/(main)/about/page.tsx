import Link from 'next/link'
import { GraduationCap, Users, Award, Heart, MapPin, Target } from 'lucide-react'

export const metadata = {
  title: 'About Us',
  description: 'Learn about DooarsTutors - connecting students with quality educators in the Dooars region.',
}

export default function AboutPage() {
  return (
    <div className="min-h-screen">
      {/* Hero */}
      <section className="bg-gradient-to-br from-[#003153] to-[#005a99] py-20">
        <div className="max-w-4xl mx-auto px-4 text-center text-white">
          <h1 className="text-4xl md:text-5xl font-bold mb-6">About DooarsTutors</h1>
          <p className="text-xl text-white/70 leading-relaxed">
            We are a platform dedicated to connecting students with quality educators 
            across the beautiful Dooars region of West Bengal.
          </p>
        </div>
      </section>

      {/* Mission */}
      <section className="py-20 bg-white">
        <div className="max-w-6xl mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {[
              { icon: Target, title: 'Our Mission', text: 'To make quality education accessible to every student in the Dooars region by connecting them with the best tutors and training centres.' },
              { icon: Heart, title: 'Our Vision', text: 'To build a thriving community of educators and learners, fostering growth and excellence in education across all disciplines.' },
              { icon: Users, title: 'Our Impact', text: 'Hundreds of tutors and thousands of students connected through our platform, building a stronger educational ecosystem.' },
            ].map((item) => (
              <div key={item.title} className="bg-gray-50 rounded-2xl p-8 text-center hover:shadow-lg transition-shadow">
                <div className="w-16 h-16 bg-[#003153] rounded-2xl flex items-center justify-center mx-auto mb-5">
                  <item.icon className="w-8 h-8 text-white" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-3">{item.title}</h3>
                <p className="text-gray-600 leading-relaxed">{item.text}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Features */}
      <section className="py-20 bg-gray-50">
        <div className="max-w-6xl mx-auto px-4">
          <h2 className="text-3xl font-bold text-gray-900 text-center mb-12">Why Choose Us</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {[
              { icon: GraduationCap, title: 'Verified Tutors', text: 'All educators are verified and rated by students' },
              { icon: MapPin, title: 'Local Focus', text: 'Specialized in the Dooars region for personalized service' },
              { icon: Award, title: 'All Categories', text: 'Academics, arts, music, dance, sports, and more' },
              { icon: Users, title: 'Easy Connect', text: 'Direct contact with tutors via call, WhatsApp, or email' },
            ].map((item) => (
              <div key={item.title} className="bg-white rounded-xl p-6 border border-gray-200 hover:border-[#003153] transition-colors">
                <item.icon className="w-8 h-8 text-[#003153] mb-4" />
                <h3 className="font-semibold text-gray-900 mb-2">{item.title}</h3>
                <p className="text-gray-500 text-sm">{item.text}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 bg-[#003153]">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h2 className="text-3xl font-bold text-white mb-6">Join Our Growing Community</h2>
          <p className="text-white/70 text-lg mb-8">
            Whether you&apos;re a tutor looking to reach more students or a student seeking the best education, we&apos;re here for you.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/register" className="px-8 py-4 bg-white text-[#003153] rounded-xl font-semibold hover:bg-gray-100 transition-colors">
              Register as Tutor
            </Link>
            <Link href="/tutors" className="px-8 py-4 bg-white/10 text-white border border-white/20 rounded-xl font-semibold hover:bg-white/20 transition-colors">
              Find a Tutor
            </Link>
          </div>
        </div>
      </section>
    </div>
  )
}
