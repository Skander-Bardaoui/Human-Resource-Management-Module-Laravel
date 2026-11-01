<?php

use App\Models\User;

it('redirects guests from every collaborator page', function () {
    foreach ([
        'dashboard',
        'collaborator.conges',
        'collaborator.attendance',
        'collaborator.payslips',
        'collaborator.documents',
    ] as $name) {
        $this->get(route($name))->assertRedirect(route('login'));
    }
});

it('resolves the employee through a case-insensitive email match', function () {
    [$user, $employee] = collaboratorAccount(['name' => 'Skander Bardaoui']);

    expect($user->email)->not->toBe($employee->email);

    $this->withCookies(tokenCookiesFor($user))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Skander')
        ->assertSee($employee->matricule());
});

it('redirects an unlinked collaborator to the profile page with an error', function () {
    $user = User::factory()->create(['role' => 'collaborator']);

    $this->withCookies(tokenCookiesFor($user))
        ->get(route('dashboard'))
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('error');
});

it('redirects administrators to the admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('collaborator.conges'))
        ->assertRedirect(route('admin.dashboard'));
});
