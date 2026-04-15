'use client'

import { useState } from 'react'
import { createClient } from '@/lib/supabase/client'
import { Search, Trash2, CheckCircle, XCircle, ChevronDown } from 'lucide-react'
import { formatDate } from '@/lib/utils'
import toast from 'react-hot-toast'

interface TutorRow {
  id: string; name: string; email: string | null; phone: string; city: string | null
  experience: string | null; rating: number; rating_count: number; plan: string
  status: string; type: string; profession: string | null; payment_status: string
  created_at: string
}

export default function AdminTutorTable({ tutors: initialTutors }: { tutors: TutorRow[] }) {
  const [tutors, setTutors] = useState(initialTutors)
  const [search, setSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState('')
  const [typeFilter, setTypeFilter] = useState('')
  const supabase = createClient()

  const filtered = tutors.filter((t) => {
    const matchSearch = !search || t.name?.toLowerCase().includes(search.toLowerCase()) ||
      t.city?.toLowerCase().includes(search.toLowerCase()) ||
      t.phone?.includes(search)
    const matchStatus = !statusFilter || t.status === statusFilter
    const matchType = !typeFilter || t.type === typeFilter
    return matchSearch && matchStatus && matchType
  })

  async function updateStatus(id: string, status: string) {
    const { error } = await supabase.from('tutors').update({ status }).eq('id', id)
    if (error) { toast.error('Failed to update'); return }
    setTutors(tutors.map((t) => (t.id === id ? { ...t, status } : t)))
    toast.success(`Status updated to ${status}`)
  }

  async function deleteTutor(id: string) {
    if (!confirm('Are you sure you want to delete this tutor?')) return
    const { error } = await supabase.from('tutors').delete().eq('id', id)
    if (error) { toast.error('Failed to delete'); return }
    setTutors(tutors.filter((t) => t.id !== id))
    toast.success('Tutor deleted')
  }

  return (
    <div>
      {/* Filters */}
      <div className="p-4 border-b border-gray-200 flex flex-wrap gap-3">
        <div className="relative flex-1 min-w-[200px]">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            type="text"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Search by name, city, phone..."
            className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-[#003153] outline-none"
          />
        </div>
        <select
          value={statusFilter}
          onChange={(e) => setStatusFilter(e.target.value)}
          className="px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-[#003153] outline-none"
        >
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="pending">Pending</option>
        </select>
        <select
          value={typeFilter}
          onChange={(e) => setTypeFilter(e.target.value)}
          className="px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-[#003153] outline-none"
        >
          <option value="">All Types</option>
          <option value="individual">Individual</option>
          <option value="organisation">Organisation</option>
        </select>
        <span className="self-center text-sm text-gray-500">{filtered.length} results</span>
      </div>

      {/* Table */}
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="bg-gray-50 text-left">
              <th className="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
              <th className="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Contact</th>
              <th className="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">City</th>
              <th className="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Rating</th>
              <th className="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th className="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Payment</th>
              <th className="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Type</th>
              <th className="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Joined</th>
              <th className="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filtered.map((tutor) => (
              <tr key={tutor.id} className="hover:bg-gray-50 transition-colors">
                <td className="px-4 py-3">
                  <div className="font-medium text-gray-900 text-sm">{tutor.name}</div>
                  <div className="text-xs text-gray-500 truncate max-w-[150px]">{tutor.profession}</div>
                </td>
                <td className="px-4 py-3">
                  <div className="text-sm text-gray-900">{tutor.phone}</div>
                  <div className="text-xs text-gray-500">{tutor.email}</div>
                </td>
                <td className="px-4 py-3 text-sm text-gray-600">{tutor.city || '-'}</td>
                <td className="px-4 py-3">
                  <span className="text-yellow-500 text-sm">★ {tutor.rating}</span>
                  <span className="text-gray-400 text-xs ml-1">({tutor.rating_count})</span>
                </td>
                <td className="px-4 py-3">
                  <StatusBadge status={tutor.status} />
                </td>
                <td className="px-4 py-3">
                  <StatusBadge status={tutor.payment_status} />
                </td>
                <td className="px-4 py-3">
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    tutor.type === 'organisation' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'
                  }`}>
                    {tutor.type}
                  </span>
                </td>
                <td className="px-4 py-3 text-sm text-gray-500">{formatDate(tutor.created_at)}</td>
                <td className="px-4 py-3">
                  <div className="flex items-center gap-1">
                    {tutor.status !== 'active' && (
                      <button
                        onClick={() => updateStatus(tutor.id, 'active')}
                        className="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                        title="Activate"
                      >
                        <CheckCircle className="w-4 h-4" />
                      </button>
                    )}
                    {tutor.status === 'active' && (
                      <button
                        onClick={() => updateStatus(tutor.id, 'inactive')}
                        className="p-1.5 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                        title="Deactivate"
                      >
                        <XCircle className="w-4 h-4" />
                      </button>
                    )}
                    <button
                      onClick={() => deleteTutor(tutor.id)}
                      className="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      title="Delete"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && (
          <div className="text-center py-12 text-gray-400">
            <p>No tutors found</p>
          </div>
        )}
      </div>
    </div>
  )
}

function StatusBadge({ status }: { status: string }) {
  const styles: Record<string, string> = {
    active: 'bg-green-100 text-green-700',
    inactive: 'bg-red-100 text-red-700',
    pending: 'bg-blue-100 text-blue-700',
    paid: 'bg-green-100 text-green-700',
    suspended: 'bg-gray-100 text-gray-700',
  }
  return (
    <span className={`px-2.5 py-1 rounded-full text-xs font-medium capitalize ${styles[status] || 'bg-gray-100 text-gray-500'}`}>
      {status}
    </span>
  )
}
