'use client'

import Link from 'next/link'
import { useState } from 'react'
import { useAuth } from '@/hooks/useAuth'
import { Menu, X, ChevronDown, LogOut, LayoutDashboard, User } from 'lucide-react'
import { CATEGORIES } from '@/constants/categories'

export default function Navbar() {
  const [mobileOpen, setMobileOpen] = useState(false)
  const [categoriesOpen, setCategoriesOpen] = useState(false)
  const { user, profile, isAdmin, signOut } = useAuth()

  return (
    <>
      <nav className="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg shadow-sm border-b border-gray-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            {/* Logo */}
            <Link href="/" className="flex items-center gap-2">
              <span className="text-xl font-bold bg-gradient-to-r from-[#003153] to-[#0066aa] bg-clip-text text-transparent">
                DooarsTutors
              </span>
            </Link>

            {/* Desktop Nav */}
            <div className="hidden md:flex items-center gap-1">
              <Link
                href="/"
                className="px-4 py-2 rounded-full text-sm font-medium text-gray-700 hover:bg-[#003153] hover:text-white transition-all duration-200"
              >
                Home
              </Link>

              {/* Categories Dropdown */}
              <div className="relative group">
                <button className="flex items-center gap-1 px-4 py-2 rounded-full text-sm font-medium text-gray-700 hover:bg-[#003153] hover:text-white transition-all duration-200">
                  Categories
                  <ChevronDown className="w-3.5 h-3.5" />
                </button>
                <div className="absolute top-full left-0 mt-1 w-56 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0">
                  {CATEGORIES.map((cat) => (
                    <Link
                      key={cat.slug}
                      href={`/categories/${cat.slug}`}
                      className="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-[#003153] hover:text-white transition-colors first:rounded-t-xl last:rounded-b-xl"
                    >
                      <cat.icon className="w-4 h-4" />
                      {cat.name}
                    </Link>
                  ))}
                </div>
              </div>

              <Link
                href="/tutors"
                className="px-4 py-2 rounded-full text-sm font-medium text-gray-700 hover:bg-[#003153] hover:text-white transition-all duration-200"
              >
                Find Tutors
              </Link>

              <Link
                href="/about"
                className="px-4 py-2 rounded-full text-sm font-medium text-gray-700 hover:bg-[#003153] hover:text-white transition-all duration-200"
              >
                About
              </Link>

              {/* Auth Buttons */}
              {user ? (
                <div className="flex items-center gap-2 ml-4">
                  <Link
                    href={isAdmin ? '/admin' : '/dashboard'}
                    className="flex items-center gap-2 px-4 py-2 bg-[#003153] text-white rounded-full text-sm font-medium hover:bg-[#002040] transition-all"
                  >
                    <LayoutDashboard className="w-4 h-4" />
                    Dashboard
                  </Link>
                  <button
                    onClick={signOut}
                    className="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-200 transition-all"
                  >
                    <LogOut className="w-4 h-4" />
                  </button>
                </div>
              ) : (
                <div className="flex items-center gap-2 ml-4">
                  <Link
                    href="/login"
                    className="px-4 py-2 text-sm font-medium text-[#003153] hover:text-[#002040] transition-colors"
                  >
                    Login
                  </Link>
                  <Link
                    href="/register"
                    className="px-5 py-2.5 bg-[#003153] text-white rounded-full text-sm font-semibold hover:bg-[#002040] transition-all shadow-md hover:shadow-lg"
                  >
                    Register as Tutor
                  </Link>
                </div>
              )}
            </div>

            {/* Mobile Menu Button */}
            <button
              onClick={() => setMobileOpen(!mobileOpen)}
              className="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100"
            >
              {mobileOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>
        </div>
      </nav>

      {/* Mobile Overlay */}
      {mobileOpen && (
        <div className="fixed inset-0 z-40 bg-black/50 md:hidden" onClick={() => setMobileOpen(false)} />
      )}

      {/* Mobile Nav */}
      <div
        className={`fixed top-0 right-0 w-72 h-full bg-white z-50 shadow-xl transform transition-transform duration-300 md:hidden ${
          mobileOpen ? 'translate-x-0' : 'translate-x-full'
        }`}
      >
        <div className="bg-[#003153] text-white p-5">
          <div className="flex justify-between items-center">
            <span className="font-bold text-lg">DooarsTutors</span>
            <button onClick={() => setMobileOpen(false)}>
              <X className="w-5 h-5" />
            </button>
          </div>
          {user && profile && (
            <p className="text-sm text-white/70 mt-2">Welcome, {profile.full_name}</p>
          )}
        </div>

        <div className="py-4 overflow-y-auto h-[calc(100%-80px)]">
          <Link
            href="/"
            onClick={() => setMobileOpen(false)}
            className="block px-5 py-3 text-gray-700 hover:bg-gray-50 font-medium"
          >
            Home
          </Link>
          <Link
            href="/tutors"
            onClick={() => setMobileOpen(false)}
            className="block px-5 py-3 text-gray-700 hover:bg-gray-50 font-medium"
          >
            Find Tutors
          </Link>

          {/* Mobile Categories */}
          <button
            onClick={() => setCategoriesOpen(!categoriesOpen)}
            className="flex items-center justify-between w-full px-5 py-3 text-gray-700 hover:bg-gray-50 font-medium"
          >
            Categories
            <ChevronDown className={`w-4 h-4 transition-transform ${categoriesOpen ? 'rotate-180' : ''}`} />
          </button>
          {categoriesOpen && (
            <div className="bg-gray-50 py-1">
              {CATEGORIES.map((cat) => (
                <Link
                  key={cat.slug}
                  href={`/categories/${cat.slug}`}
                  onClick={() => setMobileOpen(false)}
                  className="block px-8 py-2.5 text-sm text-gray-600 hover:text-[#003153] hover:bg-gray-100"
                >
                  {cat.name}
                </Link>
              ))}
            </div>
          )}

          <Link
            href="/about"
            onClick={() => setMobileOpen(false)}
            className="block px-5 py-3 text-gray-700 hover:bg-gray-50 font-medium"
          >
            About
          </Link>

          <hr className="my-3" />

          {user ? (
            <>
              <Link
                href={isAdmin ? '/admin' : '/dashboard'}
                onClick={() => setMobileOpen(false)}
                className="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-50 font-medium"
              >
                <LayoutDashboard className="w-4 h-4" />
                Dashboard
              </Link>
              <button
                onClick={() => {
                  signOut()
                  setMobileOpen(false)
                }}
                className="flex items-center gap-3 w-full px-5 py-3 text-red-600 hover:bg-red-50 font-medium"
              >
                <LogOut className="w-4 h-4" />
                Logout
              </button>
            </>
          ) : (
            <>
              <Link
                href="/login"
                onClick={() => setMobileOpen(false)}
                className="block px-5 py-3 text-gray-700 hover:bg-gray-50 font-medium"
              >
                Login
              </Link>
              <div className="px-5 py-3">
                <Link
                  href="/register"
                  onClick={() => setMobileOpen(false)}
                  className="block text-center bg-[#003153] text-white py-3 rounded-xl font-semibold hover:bg-[#002040] transition-colors"
                >
                  Register as Tutor
                </Link>
              </div>
            </>
          )}
        </div>
      </div>
    </>
  )
}
