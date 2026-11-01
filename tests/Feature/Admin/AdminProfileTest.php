<?php

use App\Models\User;

function adminProfileUser(): User
{
    return User::factory()->create(['role' => 'admin', 'name' => 'Admin RH']);
}

it('renders the admin profile page inside the admin shell', function () {
    $admin = adminProfileUser();

    $response = $this->withCookies(tokenCookiesFor($admin))->get(route('admin.profile'));

    $response->assertOk()
        ->assertSee('Mon Profil Administrateur')
        ->assertSee($admin->email)
        ->assertSee('Informations du compte')
        ->assertSee('Sécurité du compte')
        ->assertSee('Protection de session')
        ->assertSee('Modules d\'administration', false)
        ->assertSee('Périmètre d\'accès', false)
        ->assertSee('Détails du compte')
        ->assertSee('Administrateur RH')
        ->assertSee(route('admin.leaves'), false)
        ->assertSee(route('admin.employees'), false)
        ->assertDontSee('Évolution de Carrière');
});

it('shows live module counters on the admin profile', function () {
    $admin = adminProfileUser();
    [, $collaborator] = collaboratorAccount();

    collabLeave($collaborator, '2026-11-16', '2026-11-18');
    collabLeave($collaborator, '2026-12-07', '2026-12-08');

    $response = $this->withCookies(tokenCookiesFor($admin))->get(route('admin.profile'));

    $response->assertOk()
        ->assertSee('2 en attente')
        ->assertSee('1 actifs');
});

it('redirects administrators away from the collaborator profile page', function () {
    $admin = adminProfileUser();

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('profile.edit'))
        ->assertRedirect(route('admin.profile'));
});

it('redirects back to the admin profile after an update', function () {
    $admin = adminProfileUser();

    $this->withCookies(tokenCookiesFor($admin))
        ->patch(route('profile.update'), [
            'name' => 'Admin Mis à Jour',
            'email' => $admin->email,
        ])
        ->assertRedirect(route('admin.profile'))
        ->assertSessionHas('status', 'profile-updated');

    expect($admin->fresh()->name)->toBe('Admin Mis à Jour');
});

it('keeps collaborators on the collaborator profile page', function () {
    [$user] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Mon Profil Professionnel');
});
