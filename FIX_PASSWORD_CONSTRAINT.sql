-- =====================================================
-- FIX: Password NOT NULL Constraint Error
-- =====================================================

-- Problem: Supabase Auth tries to insert into public.users
-- but password column is NOT NULL while Supabase doesn't
-- provide password (it's hashed in auth.users)

-- Solution 1: Make password nullable
ALTER TABLE public.users ALTER COLUMN password DROP NOT NULL;

-- Solution 2: Set default empty string
ALTER TABLE public.users ALTER COLUMN password SET DEFAULT '';

-- Solution 3: Or remove password column entirely
-- (since auth is handled by Supabase Auth, not public.users)
-- ALTER TABLE public.users DROP COLUMN IF EXISTS password;

-- =====================================================
-- Disable problematic triggers
-- =====================================================

-- Check what triggers exist
SELECT 
    trigger_name,
    event_object_table,
    action_timing,
    event_manipulation
FROM information_schema.triggers
WHERE trigger_schema = 'auth' 
  AND event_object_table = 'users';

-- Disable ALL triggers on auth.users
ALTER TABLE auth.users DISABLE TRIGGER ALL;

-- Or disable specific trigger
-- DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;

-- =====================================================
-- Verify table structure
-- =====================================================

-- Check users table structure
SELECT 
    column_name,
    data_type,
    is_nullable,
    column_default
FROM information_schema.columns
WHERE table_schema = 'public' 
  AND table_name = 'users'
ORDER BY ordinal_position;

-- =====================================================
-- Test signup after fix
-- =====================================================

-- After running above commands, test in browser:
-- http://127.0.0.1:8000/register
