/**
 * Data Migration Script: MySQL → Supabase PostgreSQL
 * 
 * HOW TO USE:
 * 1. Export your MySQL tables as JSON (using a tool like phpMyAdmin export or mysql2json)
 * 2. Place the JSON files in a /data directory: tutors.json, reviews.json, referrals.json, etc.
 * 3. Set your Supabase credentials in .env.local
 * 4. Run: npx tsx scripts/migrate-data.ts
 * 
 * PREREQUISITES:
 * - Run the SQL migration (supabase/migrations/001_initial_schema.sql) first
 * - Create auth users for each tutor/admin before running
 */

import { createClient } from '@supabase/supabase-js'
import * as fs from 'fs'
import * as path from 'path'

const SUPABASE_URL = process.env.NEXT_PUBLIC_SUPABASE_URL || ''
const SERVICE_ROLE_KEY = process.env.SUPABASE_SERVICE_ROLE_KEY || ''

if (!SUPABASE_URL || !SERVICE_ROLE_KEY) {
  console.error('Error: Set NEXT_PUBLIC_SUPABASE_URL and SUPABASE_SERVICE_ROLE_KEY in .env.local')
  process.exit(1)
}

const supabase = createClient(SUPABASE_URL, SERVICE_ROLE_KEY, {
  auth: { autoRefreshToken: false, persistSession: false },
})

// Map of old MySQL IDs → new Supabase UUIDs
const tutorIdMap = new Map<number, string>()

async function migrateTutors() {
  console.log('\n📦 Migrating tutors...')

  const dataPath = path.join(__dirname, '../data/tutors.json')
  if (!fs.existsSync(dataPath)) {
    console.log('  ⚠ No tutors.json found in /data directory. Skipping.')
    return
  }

  const tutors = JSON.parse(fs.readFileSync(dataPath, 'utf-8'))
  console.log(`  Found ${tutors.length} tutors to migrate`)

  for (const tutor of tutors) {
    try {
      // 1. Create auth user (email + password)
      const email = tutor.email || `tutor_${tutor.id}@dooarstutors.local`
      const password = tutor.password || 'TempPassword123!' // They'll need to reset

      const { data: authUser, error: authError } = await supabase.auth.admin.createUser({
        email,
        password,
        email_confirm: true,
        user_metadata: {
          full_name: tutor.name,
          phone: tutor.phone,
        },
      })

      if (authError) {
        console.log(`  ⚠ Auth error for ${tutor.name}: ${authError.message}. Trying next...`)
        continue
      }

      const userId = authUser.user.id

      // 2. Map profession_details (handle both string and object)
      let professionDetails = {}
      if (typeof tutor.profession_details === 'string') {
        try { professionDetails = JSON.parse(tutor.profession_details) } catch { professionDetails = {} }
      } else if (tutor.profession_details) {
        professionDetails = tutor.profession_details
      }

      // 3. Insert tutor record
      const { data: newTutor, error: tutorError } = await supabase
        .from('tutors')
        .insert({
          user_id: userId,
          name: tutor.name,
          phone: tutor.phone,
          email: tutor.email || null,
          experience: tutor.experience || null,
          boards: tutor.boards || null,
          classes: tutor.classes || null,
          subjects: tutor.subjects || null,
          teaching_preferences: tutor.teaching_preferences || null,
          city: tutor.city || null,
          address: tutor.address || null,
          latitude: tutor.latitude || null,
          longitude: tutor.longitude || null,
          plan: tutor.plan || 'free',
          status: tutor.status || 'active',
          type: tutor.type || 'individual',
          rating: tutor.rating || 0,
          rating_count: tutor.rating_count || 0,
          profession: tutor.profession || null,
          profession_details: professionDetails,
          referral_code: tutor.referral_code || null,
          wallet_balance: tutor.wallet_balance || 0,
          referral_code_created_at: tutor.referral_code_created_at || null,
          payment_status: tutor.payment_status || 'pending',
          payment_id: tutor.payment_id || null,
          payment_date: tutor.payment_date || null,
          payment_amount: tutor.payment_amount || null,
          order_id: tutor.order_id || null,
          created_at: tutor.created_at || new Date().toISOString(),
        })
        .select('id')
        .single()

      if (tutorError) {
        console.log(`  ✗ Tutor insert error for ${tutor.name}: ${tutorError.message}`)
      } else {
        tutorIdMap.set(tutor.id, newTutor.id)
        console.log(`  ✓ Migrated: ${tutor.name} (old ID: ${tutor.id} → ${newTutor.id})`)
      }
    } catch (err) {
      console.log(`  ✗ Error migrating ${tutor.name}:`, err)
    }
  }

  console.log(`  ✅ Migrated ${tutorIdMap.size}/${tutors.length} tutors`)
}

async function migrateReviews() {
  console.log('\n📝 Migrating reviews...')

  const dataPath = path.join(__dirname, '../data/reviews.json')
  if (!fs.existsSync(dataPath)) {
    console.log('  ⚠ No reviews.json found. Skipping.')
    return
  }

  const reviews = JSON.parse(fs.readFileSync(dataPath, 'utf-8'))
  let migrated = 0

  for (const review of reviews) {
    const newTutorId = tutorIdMap.get(review.tutor_id)
    if (!newTutorId) {
      console.log(`  ⚠ Skipping review for old tutor ID ${review.tutor_id} (not migrated)`)
      continue
    }

    const { error } = await supabase.from('reviews').insert({
      tutor_id: newTutorId,
      student_name: review.student_name,
      rating: review.rating,
      review_text: review.review_text,
      created_at: review.created_at,
    })

    if (!error) migrated++
  }

  console.log(`  ✅ Migrated ${migrated}/${reviews.length} reviews`)
}

async function migrateReferrals() {
  console.log('\n🔗 Migrating referrals...')

  const dataPath = path.join(__dirname, '../data/referrals.json')
  if (!fs.existsSync(dataPath)) {
    console.log('  ⚠ No referrals.json found. Skipping.')
    return
  }

  const referrals = JSON.parse(fs.readFileSync(dataPath, 'utf-8'))
  let migrated = 0

  for (const ref of referrals) {
    const newReferrerId = tutorIdMap.get(ref.referrer_id)
    const newRefereeId = tutorIdMap.get(ref.referee_id)

    if (!newReferrerId || !newRefereeId) continue

    const { error } = await supabase.from('referrals').insert({
      referrer_id: newReferrerId,
      referee_id: newRefereeId,
      coupon_code: ref.coupon_code,
      discount_applied: ref.discount_applied === 1,
      reward_given: ref.reward_given === 1,
      created_at: ref.created_at,
    })

    if (!error) migrated++
  }

  console.log(`  ✅ Migrated ${migrated}/${referrals.length} referrals`)
}

async function migrateTutorSubjects() {
  console.log('\n📚 Migrating tutor_subjects...')

  const dataPath = path.join(__dirname, '../data/tutor_subjects.json')
  if (!fs.existsSync(dataPath)) {
    console.log('  ⚠ No tutor_subjects.json found. Skipping.')
    return
  }

  const subjects = JSON.parse(fs.readFileSync(dataPath, 'utf-8'))
  let migrated = 0

  for (const sub of subjects) {
    const newTutorId = tutorIdMap.get(sub.tutor_id)
    if (!newTutorId) continue

    const { error } = await supabase.from('tutor_subjects').insert({
      tutor_id: newTutorId,
      subject: sub.subject,
      class: sub.class,
    })

    if (!error) migrated++
  }

  console.log(`  ✅ Migrated ${migrated}/${subjects.length} tutor_subjects`)
}

async function createAdmin() {
  console.log('\n👑 Creating admin user...')

  const { data: adminUser, error } = await supabase.auth.admin.createUser({
    email: 'admin@dooarstutors.com',
    password: 'AdminPassword123!',
    email_confirm: true,
    user_metadata: { full_name: 'Admin', phone: '' },
  })

  if (error) {
    console.log(`  ⚠ Admin creation: ${error.message}`)
    return
  }

  // Update profile to admin role
  await supabase.from('profiles').update({ role: 'admin' }).eq('id', adminUser.user.id)
  console.log(`  ✅ Admin created: admin@dooarstutors.com`)
}

async function main() {
  console.log('🚀 Starting Dooars Tutors Data Migration')
  console.log('==========================================')

  await createAdmin()
  await migrateTutors()
  await migrateReviews()
  await migrateReferrals()
  await migrateTutorSubjects()

  console.log('\n==========================================')
  console.log('✅ Migration complete!')
  console.log(`   ID Map saved: ${tutorIdMap.size} tutors mapped`)
  console.log('\n⚠ REMEMBER:')
  console.log('  1. All migrated users have temporary passwords - require reset')
  console.log('  2. Profile pictures need to be uploaded to Supabase Storage separately')
  console.log('  3. Review the data in Supabase Dashboard for accuracy')
}

main().catch(console.error)
