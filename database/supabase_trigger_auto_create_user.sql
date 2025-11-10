-- ============================================================
-- Supabase Trigger: Auto Create User in users Table
-- ============================================================
-- Jalankan SQL ini di Supabase SQL Editor untuk:
-- 1. Otomatis insert user baru ke table 'users' saat signup di Supabase Auth
-- 2. Sync supabase_id, email, name dari auth.users ke public.users
-- 3. Set role default 'user' (bisa diubah manual di table users)
--
-- Cara pakai:
-- 1. Buka Supabase Dashboard → SQL Editor
-- 2. Copy-paste script ini
-- 3. Run
-- ============================================================

-- Step 1: Create function yang akan dipanggil trigger
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
  -- Insert user baru ke table public.users
  INSERT INTO public.users (
    supabase_id,
    email,
    name,
    role,
    avatar_url,
    password,
    created_at,
    updated_at
  )
  VALUES (
    NEW.id,                                           -- supabase_id dari auth.users
    NEW.email,                                        -- email dari auth.users
    COALESCE(NEW.raw_user_meta_data->>'name', SPLIT_PART(NEW.email, '@', 1)), -- name dari metadata atau email
    COALESCE(NEW.raw_user_meta_data->>'role', 'user'), -- role dari metadata, default 'user'
    NEW.raw_user_meta_data->>'avatar_url',           -- avatar_url dari metadata
    '',                                               -- password kosong (auth di Supabase)
    NOW(),
    NOW()
  );
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Step 2: Create trigger di auth.users
DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW
  EXECUTE FUNCTION public.handle_new_user();

-- ============================================================
-- OPTIONAL: Update existing auth users ke table users
-- ============================================================
-- Jalankan ini jika sudah ada user di auth.users tapi belum masuk ke public.users

INSERT INTO public.users (supabase_id, email, name, role, avatar_url, password, created_at, updated_at)
SELECT 
  id,
  email,
  COALESCE(raw_user_meta_data->>'name', SPLIT_PART(email, '@', 1)),
  COALESCE(raw_user_meta_data->>'role', 'user'),
  raw_user_meta_data->>'avatar_url',
  '',
  created_at,
  updated_at
FROM auth.users
WHERE id NOT IN (SELECT supabase_id FROM public.users WHERE supabase_id IS NOT NULL)
ON CONFLICT (supabase_id) DO NOTHING;

-- ============================================================
-- Verification Query (cek apakah trigger aktif)
-- ============================================================
-- SELECT * FROM pg_trigger WHERE tgname = 'on_auth_user_created';
-- SELECT proname FROM pg_proc WHERE proname = 'handle_new_user';
