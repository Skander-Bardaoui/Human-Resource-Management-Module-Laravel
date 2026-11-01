<?php

namespace App\Services;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class TokenService
{
    public function issueTokens(User $user, bool $remember = false): array
    {
        $refreshTtl = $remember
            ? (int) config('auth.tokens.remember_ttl')
            : (int) config('auth.tokens.refresh_ttl');

        return [
            'access_token' => $this->createAccessToken($user),
            'refresh_token' => $this->createRefreshToken($user, $refreshTtl),
            'refresh_ttl' => $refreshTtl,
            'expires_in' => $this->accessTtl() * 60,
        ];
    }

    public function createAccessToken(User $user): string
    {
        $issuedAt = time();

        $payload = [
            'iss' => config('app.url'),
            'sub' => (string) $user->getKey(),
            'email' => $user->email,
            'role' => $user->role,
            'type' => 'access',
            'iat' => $issuedAt,
            'exp' => $issuedAt + ($this->accessTtl() * 60),
            'jti' => (string) Str::uuid(),
        ];

        $segments = [
            $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'])),
            $this->base64UrlEncode(json_encode($payload)),
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $this->secret(), true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    public function parseAccessToken(string $token): ?array
    {
        $segments = explode('.', $token);

        if (count($segments) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $segments;

        $decodedHeader = json_decode($this->base64UrlDecode($header), true);

        if (! is_array($decodedHeader) || ($decodedHeader['alg'] ?? null) !== 'HS256') {
            return null;
        }

        $expected = $this->base64UrlEncode(
            hash_hmac('sha256', $header.'.'.$payload, $this->secret(), true)
        );

        if (! hash_equals($expected, $signature)) {
            return null;
        }

        $claims = json_decode($this->base64UrlDecode($payload), true);

        if (! is_array($claims)) {
            return null;
        }

        if (($claims['type'] ?? null) !== 'access') {
            return null;
        }

        if (! isset($claims['sub'], $claims['exp']) || (int) $claims['exp'] < time()) {
            return null;
        }

        return $claims;
    }

    public function rotateRefreshToken(string $plain): ?array
    {
        $stored = $this->findValidRefreshToken($plain);

        if ($stored === null || $stored->user === null || ! $stored->user->isActive()) {
            return null;
        }

        $user = $stored->user;
        $remainingMinutes = max(1, (int) ceil(now()->diffInMinutes($stored->expires_at)));

        $stored->forceFill(['revoked_at' => now()])->save();

        return [
            'user' => $user,
            'access_token' => $this->createAccessToken($user),
            'refresh_token' => $this->createRefreshToken($user, $remainingMinutes),
            'refresh_ttl' => $remainingMinutes,
        ];
    }

    public function revokeRefreshToken(?string $plain): void
    {
        if ($plain === null || $plain === '') {
            return;
        }

        RefreshToken::query()
            ->where('token_hash', hash('sha256', $plain))
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    public function queueAuthCookies(string $accessToken, string $refreshToken, int $refreshTtl): void
    {
        $secure = app()->environment('production');

        Cookie::queue(
            config('auth.tokens.access_cookie'),
            $accessToken,
            $this->accessTtl(),
            '/',
            null,
            $secure,
            true,
            false,
            'lax'
        );

        Cookie::queue(
            config('auth.tokens.refresh_cookie'),
            $refreshToken,
            $refreshTtl,
            '/',
            null,
            $secure,
            true,
            false,
            'lax'
        );
    }

    public function clearAuthCookies(): void
    {
        Cookie::queue(Cookie::forget(config('auth.tokens.access_cookie'), '/', null));
        Cookie::queue(Cookie::forget(config('auth.tokens.refresh_cookie'), '/', null));
    }

    private function findValidRefreshToken(string $plain): ?RefreshToken
    {
        return RefreshToken::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $plain))
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    private function createRefreshToken(User $user, int $minutes): string
    {
        $plain = Str::random(80);

        RefreshToken::create([
            'user_id' => $user->getKey(),
            'token_hash' => hash('sha256', $plain),
            'expires_at' => now()->addMinutes($minutes),
        ]);

        return $plain;
    }

    private function accessTtl(): int
    {
        return (int) config('auth.tokens.access_ttl', 15);
    }

    private function secret(): string
    {
        $key = (string) config('auth.tokens.secret');

        if (Str::startsWith($key, 'base64:')) {
            return (string) base64_decode(substr($key, 7));
        }

        return $key;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $remainder = strlen($value) % 4;

        if ($remainder !== 0) {
            $value .= str_repeat('=', 4 - $remainder);
        }

        return (string) base64_decode(strtr($value, '-_', '+/'), true);
    }
}
