<?php
return [
  'url' => env('SUPABASE_URL'),
  'anon_key' => env('SUPABASE_ANON_KEY'),
  'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),
  'jwks_url' => env('SUPABASE_JWT_JWKS_URL'),
  'aud' => env('SUPABASE_JWT_AUD', 'authenticated'),
  'iss' => env('SUPABASE_JWT_ISS'),
  'legacy_secret' => env('SUPABASE_LEGACY_JWT_SECRET'),
  'cache_ttl' => 3600,
];