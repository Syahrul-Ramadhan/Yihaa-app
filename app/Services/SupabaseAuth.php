<?php

namespace App\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SupabaseAuth
{
    public function verify(string $jwt): ?array
    {
        $conf = Config::get('supabase');
        $jwks = $this->getJwks($conf['jwks_url'], $conf['cache_ttl']);

        $header = $this->decodeHeader($jwt);
        $kid = $header['kid'] ?? null;
        if (!$kid) return null;

        $keys = JWK::parseKeySet($jwks);
        if (!isset($keys[$kid])) return null;

        try {
            $payload = (array) JWT::decode($jwt, $keys[$kid]);
        } catch (\Exception $e) {
            return null;
        }

        if (($payload['aud'] ?? null) !== $conf['aud']) return null;
        if (($payload['iss'] ?? null) !== $conf['iss']) return null;

        return $payload;
    }

    protected function getJwks(string $url, int $ttl): array
    {
        return Cache::remember('supabase_jwks', $ttl, function () use ($url) {
            $resp = (new Client(['timeout' => 5]))->get($url);
            return json_decode($resp->getBody()->getContents(), true) ?? [];
        });
    }

    protected function decodeHeader(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) < 2) return [];
        $seg = $parts[0];
        $pad = strlen($seg) % 4;
        if ($pad) $seg .= str_repeat('=', 4 - $pad);
        return json_decode(base64_decode(strtr($seg, '-_', '+/')) ?: '', true) ?? [];
    }
}