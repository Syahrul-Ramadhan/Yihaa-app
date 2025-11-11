<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function viewLogin()
    {
        return view('pages.users.login');
    }

    public function viewRegister()
    {
        return view('pages.users.register');
    }

    public function register(Request $request)
    {
        // 1️⃣ Validasi input
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // 2️⃣ GraphQL mutation untuk insert user ke Supabase
        $mutation = <<<'GRAPHQL'
        mutation InsertUser($name: String!, $email: String!, $password: String!, $avatar_url: String!, $role: String!) {
          insertIntousersCollection(
            objects: {
              name: $name,
              email: $email,
              password: $password,
              avatar_url: $avatar_url,
              role: $role
            }
          ) {
            affectedCount
            records {
              id
              name
              email
              role
              created_at
            }
          }
        }
        GRAPHQL;

        // 3️⃣ Kirim request ke Supabase GraphQL API
        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'avatar_url' => 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/avatars/default-user.jpg',
                'role' => 'user',
            ],
        ]);

        // 4️⃣ Debug respons penuh dari Supabase
        if ($response->failed()) {
            dd([
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json(),
            ]);
        }

        // 5️⃣ Cek jika tidak ada error tapi affectedCount = 0
        $result = $response->json('data.insertIntousersCollection');
        if (empty($result) || ($result['affectedCount'] ?? 0) === 0) {
            dd([
                'message' => 'Tidak ada data yang ditambahkan!',
                'response' => $response->json(),
            ]);
        }

        // 6️⃣ Jika sukses
        $user = $result['records'][0];
        return redirect('/')
            ->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user['name']);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // Query GraphQL untuk mencari user berdasarkan email
        $query = <<<'GRAPHQL'
        query GetUser($email: String!) {
            usersCollection(filter: { email: { eq: $email } }) {
                edges {
                    node {
                        id
                        name
                        email
                        password
                        role
                    }
                }
            }
        }
        GRAPHQL;

        // Kirim request ke Supabase GraphQL API
        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query,
            'variables' => [
                'email' => $email,
            ],
        ]);

        // Cek jika gagal
        if ($response->failed()) {
            return back()->with('error', 'Gagal terhubung ke server Supabase.');
        }

        $edges = $response->json('data.usersCollection.edges') ?? [];

        if (empty($edges)) {
            return back()->with('error', 'Email tidak ditemukan.');
        }

        $user = $edges[0]['node'];

        // Cek password (karena disimpan plain di Supabase)
        if ($user['password'] !== $password) {
            return back()->with('error', 'Password salah.');
        }

        // Simpan data user di session
        session([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
        ]);

        return redirect()->route('home')->with('success', 'Login berhasil!');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'Berhasil logout.');
    }
}
