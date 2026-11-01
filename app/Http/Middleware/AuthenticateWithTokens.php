<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithTokens
{
    public function __construct(private readonly TokenService $tokens)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $this->userFromAccessToken($request);

        if ($user === null) {
            $refreshed = $this->refresh($request);

            if ($refreshed === null) {
                $this->tokens->clearAuthCookies();
                Auth::forgetGuards();

                return redirect()->guest(route('login'));
            }

            $user = $refreshed['user'];

            $this->tokens->queueAuthCookies(
                $refreshed['access_token'],
                $refreshed['refresh_token'],
                $refreshed['refresh_ttl']
            );
        }

        Auth::setUser($user);

        return $next($request);
    }

    private function userFromAccessToken(Request $request): ?User
    {
        $token = $request->cookie(config('auth.tokens.access_cookie'));

        if (! is_string($token) || $token === '') {
            return null;
        }

        $claims = $this->tokens->parseAccessToken($token);

        if ($claims === null) {
            return null;
        }

        return User::query()
            ->whereKey($claims['sub'])
            ->where('is_active', true)
            ->first();
    }

    private function refresh(Request $request): ?array
    {
        $plain = $request->cookie(config('auth.tokens.refresh_cookie'));

        if (! is_string($plain) || $plain === '') {
            return null;
        }

        return $this->tokens->rotateRefreshToken($plain);
    }
}
