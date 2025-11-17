<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user dummy untuk testing
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin Yihaa',
                'email' => 'admin@yihaa.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'avatar_url' => 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/avatars/default-user.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'User Test',
                'email' => 'user@yihaa.com',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'avatar_url' => 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/avatars/default-user.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Buat data seminar dummy
        DB::table('seminar')->insert([
            [
                'nama_seminar' => 'Seminar Teknologi AI 2025',
                'tanggal_pelaksanaan' => '2025-12-01',
                'mulai_pendaftaran' => '2025-11-18',
                'akhir_pendaftaran' => '2025-11-30',
                'lokasi' => 'Auditorium Kampus',
                'pembicara' => 'Dr. John Doe',
                'deskripsi' => 'Seminar tentang perkembangan AI dan machine learning',
                'link_pendaftaran' => 'https://example.com/seminar-ai',
                'created_at' => now(),
            ],
        ]);

        // Buat data beasiswa dummy
        DB::table('beasiswa')->insert([
            [
                'nama_beasiswa' => 'Beasiswa Prestasi 2025',
                'jenjang_beasiswa' => 'S1',
                'mulai_pendaftaran' => '2025-11-18',
                'akhir_pendaftaran' => '2025-12-31',
                'syarat_beasiswa' => 'IPK minimal 3.5, aktif organisasi',
                'benefit_beasiswa' => 'Biaya kuliah penuh + uang saku',
                'pemberi_beasiswa' => 'Yayasan Pendidikan Indonesia',
                'link_pendaftaran' => 'https://example.com/beasiswa',
                'created_at' => now(),
            ],
        ]);

        // Buat data lomba dummy
        DB::table('lomba')->insert([
            [
                'nama_lomba' => 'Lomba Programming Competition 2025',
                'tanggal_pelaksanaan' => '2025-12-15',
                'mulai_pendaftaran' => '2025-11-18',
                'akhir_pendaftaran' => '2025-12-10',
                'lokasi' => 'Online',
                'kategori_lomba' => 'Programming',
                'deskripsi' => 'Kompetisi pemrograman tingkat nasional',
                'penyelenggara' => 'Himpunan Mahasiswa IT',
                'link_pendaftaran' => 'https://example.com/lomba',
                'created_at' => now(),
            ],
        ]);

        // Buat data materials dummy
        DB::table('materials')->insert([
            [
                'tittle' => 'Introduction to Laravel',
                'description' => 'Materi dasar Laravel untuk pemula',
                'uploaded_by' => 1,
                'file_url' => 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/materials/laravel-intro.pdf',
                'thumbnail_url' => 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/materials/laravel-thumb.jpg',
                'created_at' => now(),
            ],
        ]);
    }
}
