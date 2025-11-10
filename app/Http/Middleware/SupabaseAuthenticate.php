<?php
namespace App\Http\Middleware;

use App\Models\User;
use App\Services\SupabaseAuth;
use Closure;
use Illuminate\Http\Request;

class SupabaseAuthenticate {
  public function __construct(private SupabaseAuth $auth) {}
  public function handle(Request $request, Closure $next) {
    $authHeader = $request->header('Authorization');
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
      return response()->json(['message'=>'Unauthorized'], 401);
    }
    $token = substr($authHeader, 7);
    $payload = $this->auth->verify($token);
    if (!$payload) return response()->json(['message'=>'Invalid token'], 401);
    
    $supabaseId = $payload['sub'] ?? null;
    $email = $payload['email'] ?? null;
    $userMetadata = $payload['user_metadata'] ?? [];
    $role = $userMetadata['role'] ?? 'user'; // Default to 'user' if no role in metadata
    
    if (!$supabaseId) return response()->json(['message'=>'Missing sub'], 401);
    
    $user = User::where('supabase_id', $supabaseId)->first();
    if (!$user) {
      $user = User::create([
        'name' => $userMetadata['name'] ?? ($email ? explode('@',$email)[0] : 'user'),
        'email' => $email,
        'supabase_id' => $supabaseId,
        'password' => bcrypt(str()->random(32)),
        'role' => $role,
        'avatar_url' => $userMetadata['avatar_url'] ?? null,
      ]);
    } else {
      // Update role if changed in Supabase
      if ($user->role !== $role) {
        $user->update(['role' => $role]);
      }
    }
    
    auth()->setUser($user);
    return $next($request);
  }
}