<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\TokenService;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(RouteServiceProvider::HOME);
    $response->assertCookie('access_token');
    $response->assertCookie('refresh_token');

    $this->assertDatabaseHas('refresh_tokens', [
        'user_id' => $user->id,
        'revoked_at' => null,
    ]);
});

test('access token carries the authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $accessToken = $response->getCookie('access_token')->getValue();
    $claims = app(TokenService::class)->parseAccessToken($accessToken);

    expect($claims)->not->toBeNull()
        ->and($claims['sub'])->toBe((string) $user->id)
        ->and($claims['type'])->toBe('access');
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $response->assertCookieMissing('access_token');
    $response->assertCookieMissing('refresh_token');

    $this->assertDatabaseCount('refresh_tokens', 0);
});

test('deactivated accounts can not authenticate', function () {
    $user = User::factory()->create(['is_active' => false]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'email' => "Ce compte a été désactivé par l'administration. Contactez le service RH.",
    ]);
    $response->assertCookieMissing('access_token');
    $response->assertCookieMissing('refresh_token');

    $this->assertDatabaseCount('refresh_tokens', 0);
});

test('access tokens are rejected for deactivated accounts', function () {
    $user = User::factory()->create(['is_active' => false]);

    $this->withCookies(tokenCookiesFor($user))
        ->get('/dashboard')
        ->assertRedirect('/login');
});

test('refresh tokens are rejected for deactivated accounts', function () {
    $user = User::factory()->create();
    $tokens = tokenCookiesFor($user);

    $user->forceFill(['is_active' => false])->save();

    $this->withCookies(['refresh_token' => $tokens['refresh_token']])
        ->get('/dashboard')
        ->assertRedirect('/login');
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this
        ->withCookies(tokenCookiesFor($user))
        ->post('/logout');

    $response->assertRedirect('/');
    $response->assertCookieExpired('access_token');
    $response->assertCookieExpired('refresh_token');

    $this->assertDatabaseMissing('refresh_tokens', [
        'user_id' => $user->id,
        'revoked_at' => null,
    ]);
});

test('protected routes require token cookies', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect('/login');
});
