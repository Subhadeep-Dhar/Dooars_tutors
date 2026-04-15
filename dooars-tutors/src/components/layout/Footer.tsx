import Link from 'next/link'
import { CATEGORIES } from '@/constants/categories'

export default function Footer() {
  return (
    <footer className="bg-[#003153] text-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
          {/* Brand */}
          <div>
            <h3 className="text-xl font-bold mb-4">DooarsTutors</h3>
            <p className="text-white/70 text-sm leading-relaxed">
              Your trusted platform to find the best tutors, coaches, and training centres in the Dooars region. 
              Connecting students with quality educators.
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="font-semibold text-lg mb-4">Quick Links</h4>
            <ul className="space-y-2">
              <li>
                <Link href="/" className="text-white/70 hover:text-white text-sm transition-colors">
                  Home
                </Link>
              </li>
              <li>
                <Link href="/tutors" className="text-white/70 hover:text-white text-sm transition-colors">
                  Find Tutors
                </Link>
              </li>
              <li>
                <Link href="/register" className="text-white/70 hover:text-white text-sm transition-colors">
                  Become a Tutor
                </Link>
              </li>
              <li>
                <Link href="/about" className="text-white/70 hover:text-white text-sm transition-colors">
                  About Us
                </Link>
              </li>
              <li>
                <Link href="/login" className="text-white/70 hover:text-white text-sm transition-colors">
                  Login
                </Link>
              </li>
            </ul>
          </div>

          {/* Categories */}
          <div>
            <h4 className="font-semibold text-lg mb-4">Categories</h4>
            <ul className="space-y-2">
              {CATEGORIES.slice(0, 6).map((cat) => (
                <li key={cat.slug}>
                  <Link
                    href={`/categories/${cat.slug}`}
                    className="text-white/70 hover:text-white text-sm transition-colors"
                  >
                    {cat.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="font-semibold text-lg mb-4">Contact Us</h4>
            <ul className="space-y-3 text-sm text-white/70">
              <li className="flex items-start gap-2">
                <span>📧</span>
                <a href="mailto:subhadeepdhar563@gmail.com" className="hover:text-white transition-colors">
                  subhadeepdhar563@gmail.com
                </a>
              </li>
              <li className="flex items-start gap-2">
                <span>📱</span>
                <a href="tel:+919083009315" className="hover:text-white transition-colors">
                  +91 9083009315
                </a>
              </li>
              <li className="flex items-start gap-2">
                <span>📍</span>
                <span>Dooars Region, West Bengal, India</span>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="border-t border-white/10 mt-12 pt-8 text-center text-white/50 text-sm">
          <p>© {new Date().getFullYear()} DooarsTutors. All rights reserved.</p>
        </div>
      </div>
    </footer>
  )
}
