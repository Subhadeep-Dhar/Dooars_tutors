-- ============================================
-- Dooars Tutors - Supabase PostgreSQL Schema
-- Migration from MySQL to PostgreSQL
-- ============================================

-- ========================
-- ENUMS
-- ========================
CREATE TYPE user_role AS ENUM ('admin', 'tutor');
CREATE TYPE tutor_type AS ENUM ('individual', 'organisation');
CREATE TYPE tutor_status AS ENUM ('active', 'inactive', 'pending', 'suspended');
CREATE TYPE payment_status AS ENUM ('pending', 'paid', 'failed', 'refunded');
CREATE TYPE plan_type AS ENUM ('free', 'basic', 'premium');

-- ========================
-- PROFILES (extends auth.users)
-- ========================
CREATE TABLE profiles (
  id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  role user_role NOT NULL DEFAULT 'tutor',
  phone VARCHAR(20),
  full_name VARCHAR(100),
  avatar_url TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- ========================
-- TUTORS
-- ========================
CREATE TABLE tutors (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(100),
  experience TEXT,
  boards TEXT,
  classes TEXT,
  subjects TEXT,
  teaching_preferences TEXT,
  city VARCHAR(100),
  address TEXT,
  latitude DECIMAL(10,7),
  longitude DECIMAL(10,7),
  plan plan_type DEFAULT 'free',
  status tutor_status DEFAULT 'pending',
  type tutor_type DEFAULT 'individual',
  rating DECIMAL(2,1) DEFAULT 0.0,
  rating_count INTEGER DEFAULT 0,
  profession VARCHAR(255),
  profession_details JSONB DEFAULT '{}',
  profile_picture TEXT,
  last_login TIMESTAMPTZ,
  last_logout TIMESTAMPTZ,
  referral_code VARCHAR(20) UNIQUE,
  referred_by UUID REFERENCES tutors(id) ON DELETE SET NULL,
  wallet_balance DECIMAL(10,2) DEFAULT 0.00,
  referral_code_created_at TIMESTAMPTZ,
  payment_status payment_status DEFAULT 'pending',
  payment_id VARCHAR(255),
  payment_date TIMESTAMPTZ,
  payment_amount DECIMAL(10,2),
  order_id VARCHAR(255),
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_tutors_user_id ON tutors(user_id);
CREATE INDEX idx_tutors_status ON tutors(status);
CREATE INDEX idx_tutors_city ON tutors(city);
CREATE INDEX idx_tutors_profession ON tutors(profession);
CREATE INDEX idx_tutors_rating ON tutors(rating DESC);
CREATE INDEX idx_tutors_payment ON tutors(payment_status);
CREATE INDEX idx_tutors_profession_details ON tutors USING GIN(profession_details);

-- ========================
-- TUTOR_SUBJECTS
-- ========================
CREATE TABLE tutor_subjects (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  tutor_id UUID NOT NULL REFERENCES tutors(id) ON DELETE CASCADE,
  subject VARCHAR(100) NOT NULL,
  class VARCHAR(10) NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_tutor_subjects_tutor ON tutor_subjects(tutor_id);
CREATE INDEX idx_tutor_subjects_subject ON tutor_subjects(subject);

-- ========================
-- REVIEWS
-- ========================
CREATE TABLE reviews (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  tutor_id UUID NOT NULL REFERENCES tutors(id) ON DELETE CASCADE,
  student_name VARCHAR(100),
  rating INTEGER CHECK (rating >= 1 AND rating <= 5),
  review_text TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_reviews_tutor ON reviews(tutor_id);

-- ========================
-- REFERRALS
-- ========================
CREATE TABLE referrals (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  referrer_id UUID NOT NULL REFERENCES tutors(id) ON DELETE CASCADE,
  referee_id UUID NOT NULL REFERENCES tutors(id) ON DELETE CASCADE,
  coupon_code VARCHAR(20),
  discount_applied BOOLEAN DEFAULT FALSE,
  reward_given BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_referrals_referrer ON referrals(referrer_id);
CREATE INDEX idx_referrals_referee ON referrals(referee_id);

-- ========================
-- GALLERY
-- ========================
CREATE TABLE gallery (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  title VARCHAR(255) NOT NULL,
  description TEXT,
  image_url TEXT,
  category VARCHAR(50) DEFAULT 'general',
  organization_name VARCHAR(255),
  event_date DATE,
  location VARCHAR(255),
  link VARCHAR(500),
  status VARCHAR(20) DEFAULT 'active',
  priority INTEGER DEFAULT 0,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- ========================
-- TUTOR VIEWS (analytics)
-- ========================
CREATE TABLE tutor_views (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  tutor_id UUID NOT NULL REFERENCES tutors(id) ON DELETE CASCADE,
  visitor_ip VARCHAR(45),
  visited_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_tutor_views_tutor ON tutor_views(tutor_id);

-- ========================
-- SITE ANALYTICS
-- ========================
CREATE TABLE site_analytics (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  total_views INTEGER DEFAULT 0,
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Insert initial analytics row
INSERT INTO site_analytics (total_views) VALUES (0);

-- ============================================
-- TRIGGERS
-- ============================================

-- Auto update rating on review changes
CREATE OR REPLACE FUNCTION update_tutor_rating()
RETURNS TRIGGER AS $$
DECLARE
  target_tutor_id UUID;
BEGIN
  -- Use NEW for INSERT/UPDATE, OLD for DELETE
  IF TG_OP = 'DELETE' THEN
    target_tutor_id := OLD.tutor_id;
  ELSE
    target_tutor_id := NEW.tutor_id;
  END IF;

  UPDATE tutors SET
    rating = COALESCE((SELECT ROUND(AVG(rating)::numeric, 1) FROM reviews WHERE tutor_id = target_tutor_id), 0),
    rating_count = (SELECT COUNT(*) FROM reviews WHERE tutor_id = target_tutor_id)
  WHERE id = target_tutor_id;

  IF TG_OP = 'DELETE' THEN
    RETURN OLD;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_tutor_rating
  AFTER INSERT OR UPDATE OR DELETE ON reviews
  FOR EACH ROW EXECUTE FUNCTION update_tutor_rating();

-- Auto update updated_at
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_tutors_updated_at
  BEFORE UPDATE ON tutors
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

CREATE TRIGGER trigger_profiles_updated_at
  BEFORE UPDATE ON profiles
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

-- Auto create profile on auth signup
CREATE OR REPLACE FUNCTION handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
  INSERT INTO profiles (id, role, full_name, phone)
  VALUES (
    NEW.id,
    'tutor',
    COALESCE(NEW.raw_user_meta_data->>'full_name', ''),
    COALESCE(NEW.raw_user_meta_data->>'phone', '')
  );
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE FUNCTION handle_new_user();

-- ============================================
-- ROW LEVEL SECURITY
-- ============================================

ALTER TABLE profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE tutors ENABLE ROW LEVEL SECURITY;
ALTER TABLE reviews ENABLE ROW LEVEL SECURITY;
ALTER TABLE referrals ENABLE ROW LEVEL SECURITY;
ALTER TABLE tutor_subjects ENABLE ROW LEVEL SECURITY;
ALTER TABLE tutor_views ENABLE ROW LEVEL SECURITY;
ALTER TABLE gallery ENABLE ROW LEVEL SECURITY;

-- PROFILES
CREATE POLICY "Profiles are viewable by everyone" ON profiles
  FOR SELECT USING (true);

CREATE POLICY "Users can update own profile" ON profiles
  FOR UPDATE USING (auth.uid() = id);

-- TUTORS
CREATE POLICY "Active tutors visible to everyone" ON tutors
  FOR SELECT USING (true);

CREATE POLICY "Users can insert own tutor record" ON tutors
  FOR INSERT WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Tutors can update own record" ON tutors
  FOR UPDATE USING (auth.uid() = user_id);

CREATE POLICY "Users can delete own tutor record" ON tutors
  FOR DELETE USING (auth.uid() = user_id);

-- REVIEWS
CREATE POLICY "Reviews viewable by everyone" ON reviews
  FOR SELECT USING (true);

CREATE POLICY "Anyone can create reviews" ON reviews
  FOR INSERT WITH CHECK (true);

-- TUTOR_SUBJECTS
CREATE POLICY "Subjects viewable by everyone" ON tutor_subjects
  FOR SELECT USING (true);

CREATE POLICY "Owners can manage subjects" ON tutor_subjects
  FOR ALL USING (
    EXISTS (SELECT 1 FROM tutors WHERE tutors.id = tutor_subjects.tutor_id AND tutors.user_id = auth.uid())
  );

-- TUTOR_VIEWS
CREATE POLICY "Views insertable by everyone" ON tutor_views
  FOR INSERT WITH CHECK (true);

CREATE POLICY "Views viewable by tutor owner" ON tutor_views
  FOR SELECT USING (
    EXISTS (SELECT 1 FROM tutors WHERE tutors.id = tutor_views.tutor_id AND tutors.user_id = auth.uid())
  );

-- GALLERY
CREATE POLICY "Gallery viewable by everyone" ON gallery
  FOR SELECT USING (true);

-- REFERRALS
CREATE POLICY "Referrals viewable by participants" ON referrals
  FOR SELECT USING (true);

CREATE POLICY "Referrals insertable" ON referrals
  FOR INSERT WITH CHECK (true);
