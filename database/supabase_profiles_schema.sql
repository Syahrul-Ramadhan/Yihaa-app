-- ====================================
-- PROFILES TABLE
-- ====================================
-- Table untuk menyimpan data profile user yang lebih lengkap
CREATE TABLE IF NOT EXISTS profiles (
    profile_id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    phone VARCHAR(20),
    university VARCHAR(255),
    program_study VARCHAR(255),
    user_type VARCHAR(20) DEFAULT 'mahasiswa' CHECK (user_type IN ('mahasiswa', 'dosen', 'alumni')),
    bio TEXT,
    role_in_team TEXT, -- Peran dalam tim (UI/UX Designer, etc)
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- ====================================
-- USER CONTRIBUTIONS TABLE
-- ====================================
-- Table untuk menyimpan kontribusi terbaru user
CREATE TABLE IF NOT EXISTS user_contributions (
    contribution_id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    contribution_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- ====================================
-- USER EVENTS TABLE
-- ====================================
-- Table untuk tracking user apply ke event apa aja
CREATE TABLE IF NOT EXISTS user_events (
    user_event_id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_type VARCHAR(50) NOT NULL CHECK (event_type IN ('seminar', 'beasiswa', 'lomba')),
    event_id BIGINT NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    applied_at TIMESTAMP DEFAULT NOW(),
    status VARCHAR(50) DEFAULT 'applied' CHECK (status IN ('applied', 'accepted', 'rejected', 'completed'))
);

-- ====================================
-- SAVED MATERIALS TABLE  
-- ====================================
-- Table untuk menyimpan materi yang di-save user
CREATE TABLE IF NOT EXISTS saved_materials (
    saved_id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    material_id BIGINT NOT NULL REFERENCES materials(material_id) ON DELETE CASCADE,
    saved_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, material_id) -- Prevent duplicate saves
);

-- ====================================
-- LIKES TABLE (untuk post likes)
-- ====================================
CREATE TABLE IF NOT EXISTS likes (
    like_id BIGSERIAL PRIMARY KEY,
    post_id BIGINT NOT NULL REFERENCES posts(post_id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(post_id, user_id) -- User can only like once per post
);

-- ====================================
-- COMMENTS TABLE (untuk post comments)
-- ====================================
CREATE TABLE IF NOT EXISTS comments (
    comment_id BIGSERIAL PRIMARY KEY,
    post_id BIGINT NOT NULL REFERENCES posts(post_id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    parent_comment_id BIGINT REFERENCES comments(comment_id) ON DELETE CASCADE, -- For replies
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- ====================================
-- INDEXES untuk performance
-- ====================================
CREATE INDEX IF NOT EXISTS idx_profiles_user_id ON profiles(user_id);
CREATE INDEX IF NOT EXISTS idx_contributions_user_id ON user_contributions(user_id);
CREATE INDEX IF NOT EXISTS idx_user_events_user_id ON user_events(user_id);
CREATE INDEX IF NOT EXISTS idx_saved_materials_user_id ON saved_materials(user_id);
CREATE INDEX IF NOT EXISTS idx_likes_post_id ON likes(post_id);
CREATE INDEX IF NOT EXISTS idx_likes_user_id ON likes(user_id);
CREATE INDEX IF NOT EXISTS idx_comments_post_id ON comments(post_id);
CREATE INDEX IF NOT EXISTS idx_comments_user_id ON comments(user_id);
CREATE INDEX IF NOT EXISTS idx_comments_parent_id ON comments(parent_comment_id);

-- ====================================
-- TRIGGER untuk auto-create profile saat user baru
-- ====================================
CREATE OR REPLACE FUNCTION create_profile_for_new_user()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO profiles (user_id, user_type)
    VALUES (NEW.id, 'mahasiswa');
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_create_profile
    AFTER INSERT ON users
    FOR EACH ROW
    EXECUTE FUNCTION create_profile_for_new_user();

-- ====================================
-- COMMENTS:
-- ====================================
-- 1. Run SQL ini di Supabase SQL Editor
-- 2. Tidak perlu migrate:refresh, table baru akan ditambahkan
-- 3. Existing data tidak akan hilang
-- 4. Auto-create profile untuk user baru via trigger
