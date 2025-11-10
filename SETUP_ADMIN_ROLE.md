# Setup Admin Role & Auto-Sync Users from Supabase Auth

## 🔧 Setup Database Trigger di Supabase

### Masalah
User yang register via Supabase Auth **tidak otomatis masuk** ke tabel `public.users` di PostgreSQL.

### Solusi: Database Trigger
Buat trigger di Supabase yang otomatis insert user baru ke `public.users` setiap ada signup.

---

## 📋 Langkah Setup

### 1. Jalankan Migrasi Laravel (Lokal)

```powershell
# Aktifkan ekstensi PostgreSQL di php.ini dulu:
# C:\xampp\php\php.ini
#   ;extension=pdo_pgsql  →  extension=pdo_pgsql
#   ;extension=pgsql      →  extension=pgsql

# Restart Apache/terminal, verifikasi:
php -m | Select-String pgsql

# Jalankan migrasi
php artisan migrate
```

Ini akan membuat tabel `users` dengan kolom:
- `id` (bigint, primary key)
- `name` (varchar)
- `email` (varchar, unique)
- `email_verified_at` (timestamp)
- `password` (varchar)
- `remember_token` (varchar)
- `created_at` (timestamp)
- `updated_at` (timestamp)
- `supabase_id` (uuid, unique, nullable) ← dari migrasi `add_supabase_id_to_users`
- `avatar_url` (text, nullable) ← dari migrasi `add_avatar_url_to_users_table`
- `role` (varchar, default 'user') ← dari migrasi `add_role_to_users_table`

---

### 2. Setup Trigger di Supabase Dashboard

1. **Buka Supabase Dashboard**
   - Go to: https://supabase.com/dashboard/project/qdfotopajdiuailyeprh
   - Klik **SQL Editor** (menu kiri)

2. **Run SQL Script**
   - Klik **New Query**
   - Copy paste isi file: `database/supabase_trigger_auto_create_user.sql`
   - Klik **Run** (atau Ctrl+Enter)

3. **Verifikasi Trigger Aktif**
   ```sql
   SELECT * FROM pg_trigger WHERE tgname = 'on_auth_user_created';
   SELECT proname FROM pg_proc WHERE proname = 'handle_new_user';
   ```
   Harus muncul 2 row (trigger dan function).

---

### 3. Sync Existing Users (Opsional)

Jika sudah ada user di `auth.users` tapi belum masuk `public.users`:

```sql
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
```

---

## 🔐 Set User Jadi Admin

### Cara 1: Via Supabase SQL Editor
```sql
UPDATE public.users 
SET role = 'admin' 
WHERE email = 'admin@example.com';
```

### Cara 2: Via Supabase Table Editor
1. Buka **Table Editor** → Pilih tabel `users`
2. Cari row user yang mau dijadikan admin
3. Double-click kolom `role`
4. Ubah dari `user` ke `admin`
5. Tekan Enter untuk save

---

## 🧪 Testing Flow

### User Flow (Default)
1. Register di `/register` dengan email & password
2. ✅ User otomatis masuk ke `auth.users` (Supabase Auth)
3. ✅ Trigger otomatis insert ke `public.users` dengan `role = 'user'`
4. Confirm email (klik link di email)
5. Login di `/` → redirect ke `/home`
6. Akses `/api/me` → dapat profile dengan `role: "user"`, `is_admin: false`
7. Coba akses `/admin` → **403 Forbidden** ❌

### Admin Flow
1. Set user jadi admin via SQL:
   ```sql
   UPDATE public.users SET role = 'admin' WHERE email = 'youremail@example.com';
   ```
2. Login di `/admin-login` dengan email admin
3. ✅ Sistem cek `is_admin` via API `/api/me`
4. ✅ Redirect ke `/admin` (admin dashboard)
5. Akses `/api/me` → dapat `role: "admin"`, `is_admin: true`

---

## 📊 Database Schema

### Table: `public.users`
```sql
CREATE TABLE public.users (
  id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
  name VARCHAR NOT NULL,
  email VARCHAR UNIQUE NOT NULL,
  email_verified_at TIMESTAMP,
  password VARCHAR NOT NULL,
  remember_token VARCHAR,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW(),
  supabase_id UUID UNIQUE,
  avatar_url TEXT,
  role VARCHAR DEFAULT 'user' CHECK (role IN ('user', 'admin'))
);
```

### Trigger Function
```sql
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
  INSERT INTO public.users (supabase_id, email, name, role, avatar_url, password, created_at, updated_at)
  VALUES (
    NEW.id,
    NEW.email,
    COALESCE(NEW.raw_user_meta_data->>'name', SPLIT_PART(NEW.email, '@', 1)),
    COALESCE(NEW.raw_user_meta_data->>'role', 'user'),
    NEW.raw_user_meta_data->>'avatar_url',
    '',
    NOW(),
    NOW()
  );
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
```

---

## 🔍 Troubleshooting

### User register tapi tidak masuk `public.users`
1. **Cek trigger aktif:**
   ```sql
   SELECT * FROM pg_trigger WHERE tgname = 'on_auth_user_created';
   ```
   Kalau kosong → jalankan SQL trigger lagi.

2. **Cek function exist:**
   ```sql
   SELECT proname FROM pg_proc WHERE proname = 'handle_new_user';
   ```

3. **Test manual insert:**
   ```sql
   SELECT public.handle_new_user();
   ```

### Admin tidak bisa akses `/admin`
1. **Cek role di database:**
   ```sql
   SELECT id, email, role FROM public.users WHERE email = 'youremail@example.com';
   ```
   Harus `role = 'admin'`.

2. **Clear session & login ulang** (Laravel cache role dari session).

3. **Cek API response:**
   ```javascript
   // Di browser console setelah login:
   fetch('/api/me', {
     headers: { 
       'Authorization': 'Bearer ' + (await supabase.auth.getSession()).data.session.access_token 
     }
   }).then(r => r.json()).then(console.log)
   ```
   Harus return `is_admin: true`.

---

## 📝 Notes

- **Role Source of Truth**: `public.users.role` (bukan di Supabase Auth metadata)
- **Auto-Sync**: Trigger hanya jalan saat **signup baru**, tidak update existing users
- **Default Role**: Semua user baru otomatis `role = 'user'`
- **Change Role**: Update manual di SQL atau Table Editor Supabase
- **Security**: Middleware `admin` di Laravel cek `auth()->user()->isAdmin()`

---

## 🚀 Quick Commands

```powershell
# Enable PostgreSQL extension
# Edit C:\xampp\php\php.ini → uncomment pdo_pgsql & pgsql

# Verify
php -m | Select-String pgsql

# Migrate
php artisan migrate

# Start servers
php artisan serve
npm run dev
```

```sql
-- Supabase: Run trigger setup
-- Copy dari database/supabase_trigger_auto_create_user.sql

-- Set admin
UPDATE public.users SET role = 'admin' WHERE email = 'admin@example.com';

-- Check users
SELECT id, email, role, supabase_id FROM public.users;
```
