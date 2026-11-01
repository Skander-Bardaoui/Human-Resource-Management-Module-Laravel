<?php

use App\Models\Employee;
use App\Models\User;
use App\Services\TokenService;

function adminAccessUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

it('deactivates a collaborator portal account and revokes its sessions', function () {
    $admin = adminAccessUser();
    [$user, $employee] = collaboratorAccount();
    app(TokenService::class)->issueTokens($user);

    $response = $this->from(route('admin.employees'))
        ->withCookies(tokenCookiesFor($admin))
        ->patch(route('admin.employees.access', $employee));

    $response->assertRedirect(route('admin.employees'))
        ->assertSessionHas('success');

    expect($user->fresh()->is_active)->toBeFalse();

    $this->assertDatabaseMissing('refresh_tokens', [
        'user_id' => $user->id,
        'revoked_at' => null,
    ]);
});

it('reactivates a collaborator portal account', function () {
    $admin = adminAccessUser();
    [$user, $employee] = collaboratorAccount([], ['is_active' => false]);

    $this->withCookies(tokenCookiesFor($admin))
        ->patch(route('admin.employees.access', $employee))
        ->assertSessionHas('success');

    expect($user->fresh()->is_active)->toBeTrue();
});

it('refuses to toggle access when no portal account matches the employee email', function () {
    $admin = adminAccessUser();
    $employee = Employee::factory()->create(['email' => 'unknown.person@example.tn']);

    $this->withCookies(tokenCookiesFor($admin))
        ->patch(route('admin.employees.access', $employee))
        ->assertSessionHas('error');
});

it('refuses to deactivate an administrator account', function () {
    $admin = adminAccessUser();
    $employee = Employee::factory()->create(['email' => mb_strtoupper($admin->email)]);

    $this->withCookies(tokenCookiesFor($admin))
        ->patch(route('admin.employees.access', $employee))
        ->assertSessionHas('error');

    expect($admin->fresh()->is_active)->toBeTrue();
});

it('protects the access toggle from collaborators', function () {
    [$user, $employee] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->patch(route('admin.employees.access', $employee))
        ->assertRedirect(route('dashboard'));

    expect($user->fresh()->is_active)->toBeTrue();
});

it('renders the portal access state and confirmation dialog on the directory', function () {
    $admin = adminAccessUser();
    [$user, $employee] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.employees'))
        ->assertOk()
        ->assertSee('Portail actif')
        ->assertSee('id="access-toggle-dialog"', false)
        ->assertSee('data-access-toggle', false)
        ->assertSee('data-action="disable"', false);

    $user->forceFill(['is_active' => false])->save();

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.employees'))
        ->assertOk()
        ->assertSee('Portail désactivé')
        ->assertSee('data-action="enable"', false);
});
