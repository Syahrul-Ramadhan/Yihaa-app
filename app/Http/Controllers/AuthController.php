<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function viewLogin()
    {
        return view('pages.users.login');
    }

    public function loginLoading()
    {
        return view('pages.users.login-loading');
    }

    public function viewRegister()
    {
        return view('pages.users.register');
    }

    public function register(Request $request)
    {
        // 1️⃣ Validasi input
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ],[
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)   // tetap kirim error tapi kita handle custom
                ->withInput();
        }

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
                'password' => Hash::make($request->password),
                'avatar_url' => 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/avatars/default-user.jpg',
                'role' => 'user',
            ],
        ]);

        $json = $response->json();

        // 🔥 Tangani error dari Supabase (misal email duplikat)
        if (isset($json['errors'][0]['message']) && str_contains(strtolower($json['errors'][0]['message']), 'duplicate')) {
            return back()
                ->withErrors(['email' => 'Email telah digunakan, silakan gunakan email lainnya.'])
                ->withInput();
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
        return back()->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user['name']);
        // return redirect('/')
        //     ->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user['name']);
    }

    public function login(Request $request)
    {

        $email = $request->input('email');
        $password = $request->input('password');
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
                        avatar_url
                    }
                }
            }
        }
        GRAPHQL;

        // Kirim request ke Supabase GraphQL API
        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'), // <--- Ganti ini
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'), // <--- Dan ini
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query,
            'variables' => [
                'email' => $email,
            ],
        ]);

        // Cek jika gagal
        if ($response->failed()) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke server Supabase.'], 500);
            }
            return back()->with('error', 'Gagal terhubung ke server Supabase.');
        }
// 1. Ambil data dari response Supabase
        $edges = $response->json('data.usersCollection.edges') ?? [];

        // 2. Cek apakah user ditemukan
        if (empty($edges)) {
            // Jika request dari Katalon/API, kirim JSON
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Email tidak ditemukan.'], 404);
            }
            // Jika dari browser biasa, kembali ke halaman login
            return back()->with('error', 'Email tidak ditemukan.');
        }
        // -------------------------
        $user = $edges[0]['node'];

        // Cek password (karena disimpan plain di Supabase)
       if (!Hash::check($password, $user['password'])) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Password salah.'], 401);
            }
            return back()->with('error', 'Password salah.');
        }

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Simpan data user di session
        session([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
            'avatar_url' => $user['avatar_url'],
        ]);
        // Update user_id di tabel sessions
        DB::table('sessions')
            ->where('id', session()->getId())
            ->update(['user_id' => $user['id']]);


        // Redirect to loading screen instead of showing modal
        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Simpan data user di session
        session([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
            'avatar_url' => $user['avatar_url'],
        ]);

        // Update user_id di tabel sessions
        DB::table('sessions')
            ->where('id', session()->getId())
            ->update(['user_id' => $user['id']]);

        // --- BAGIAN BARU UNTUK KATALON ---
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil!',
                'data' => $user,
            ], 200);
        }
        // ---------------------------------

        // Redirect untuk pengunjung website biasa
        return redirect()->route('login.loading');
    }

    public function logout(Request $request)
    {
        $request->session()->flush(); // hapus semua session
        // Redirect to logout loading screen instead of login with modal
        return view('pages.users.logout-loading');
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

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
                        avatar_url
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

        if ($response->failed()) {
            return back()->with('error', 'Failed to connect to database.');
        }

        $edges = $response->json('data.usersCollection.edges') ?? [];

        if (empty($edges)) {
            return back()->with('error', 'Email atau password salah.');
        }

        $user = $edges[0]['node'];

        // Cek role admin
        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Access denied. Admin only.');
        }

        // Cek password
        // if (!Hash::check($password, $user['password'])) {
        //     return back()->with('error', 'Email atau password salah.');
        // }

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Simpan data user di session
        session([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
            'avatar_url' => $user['avatar_url'],
        ]);

        // Update user_id di tabel sessions
        DB::table('sessions')
            ->where('id', session()->getId())
            ->update(['user_id' => $user['id']]);

        // Redirect ke admin dashboard
        return redirect()->route('dashboard')->with('success', 'Welcome back, ' . $user['name'] . '!');
    }

    public function logoutAdmin(Request $request)
    {
        $request->session()->flush(); // hapus semua session
        // Redirect to logout loading screen instead of login with modal
        return view('pages.admin.logout-loading');
    }

    public function profile(Request $request)
    {
        $user = [
            'id' => $request->session()->get('user_id'),
            'name' => $request->session()->get('user_name'),
            'email' => $request->session()->get('user_email'),
            'role' => $request->session()->get('user_role'),
            'avatar_url' => $request->session()->get('user_avatar'),
        ];

        return view('pages.users.profile', compact('user'));
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        // Query GraphQL: cek user
        $queryCheck = <<<'GRAPHQL'
        query CheckUser($email: String!) {
            usersCollection(filter: { email: { eq: $email } }) {
                edges { node { id email } }
            }
        }
        GRAPHQL;

        $checkUser = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $queryCheck,
            'variables' => [ 'email' => $email ],
        ]);

        if (empty($checkUser->json('data.usersCollection.edges'))) {
            return back()->with('error', 'Email tidak ditemukan.');
        }

        // CREATE TOKEN
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(15)->format('Y-m-d H:i:s');

        // INSERT token ke Supabase
        $mutation = <<<'GRAPHQL'
        mutation InsertToken($email: String!, $token: String!, $expires: Datetime!) {
        insertIntoreset_passwordsCollection(
            objects: [{ email: $email, token: $token, expires_at: $expires }]
        ) {
            records { id }
        }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'email'   => $email,
                'token'   => $token,
                'expires' => $expiresAt,
            ],
        ]);

        // KIRIM EMAIL
        $resetLink = route('password.reset', ['token' => $token]);
        Mail::raw("Klik untuk reset password (berlaku 15 menit)\n\n$resetLink", function ($message) use ($email) {
            $message->to($email)->subject('Password Reset Link');
        });

        return back()->with('success', 'Link reset sudah dikirim ke email.');
    }

    public function resetPage(Request $request)
    {
        if (!$request->token) abort(404);
        return view('pages.users.reset-password', ['token' => $request->token]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        // Check token valid
        $query = <<<'GRAPHQL'
        query CheckToken($token: String!) {
            reset_passwordsCollection(filter: { token: { eq: $token } }) {
                edges { node { email expires_at } }
            }
        }
        GRAPHQL;

        $tokenData = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query,
            'variables' => [ 'token' => $request->token ],
        ]);

        $records = $tokenData->json('data.reset_passwordsCollection.edges');

        if (empty($records) || now()->greaterThan($records[0]['node']['expires_at'])) {
            return back()->with('error', 'Token invalid atau kadaluarsa.');
        }

        $email = $records[0]['node']['email'];

        // Update password user
        $updateMutation = <<<'GRAPHQL'
        mutation UpdatePassword($email: String!, $password: String!) {
            updateUsersCollection(
                filter: { email: { eq: $email } },
                set: { password: $password }
            ) {
                records { id }
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $updateMutation,
            'variables' => [
                'email' => $email,
                'password' => Hash::make($request->password)
            ],
        ]);

        // Delete token
        // DB::table('reset_passwords')->where('token', $request->token)->delete();

        return redirect('/')->with('success', 'Password berhasil diubah!');
    }


}
