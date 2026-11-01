<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function departmentAdminUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

function departmentEmployee(array $overrides = []): Employee
{
    return Employee::factory()->create(array_merge([
        'department' => 'it',
        'status' => 'actif',
    ], $overrides));
}

function departmentRecord(string $slug, string $name, array $overrides = []): Department
{
    return Department::factory()->create(array_merge([
        'slug' => $slug,
        'name' => $name,
        'description' => null,
        'code' => null,
        'manager_id' => null,
        'monthly_budget' => '0.000',
    ], $overrides));
}

it('renders real headcount payroll and budget data', function () {
    $admin = departmentAdminUser();
    $manager = departmentEmployee(['name' => 'Yasmine Trabelsi', 'position' => 'Directrice IT', 'department' => 'rh']);
    $active = departmentEmployee(['name' => 'Anis Mansour', 'department' => 'it', 'status' => 'actif']);
    $inactive = departmentEmployee(['name' => 'Sonia Inactive', 'department' => 'it', 'status' => 'inactif']);
    $period = now('Africa/Tunis')->format('Y-m');

    departmentRecord('it', 'Ingénierie & IT', [
        'manager_id' => $manager->id,
        'monthly_budget' => '7000.000',
        'description' => 'Plateforme et infrastructure.',
    ]);

    Payslip::factory()->create([
        'employee_id' => $active->id,
        'period' => $period,
        'base_salary' => '3000.000',
        'prime' => '250.500',
    ]);
    Payslip::factory()->create([
        'employee_id' => $inactive->id,
        'period' => $period,
        'base_salary' => '9000.000',
        'prime' => '0.000',
    ]);

    $response = $this->withCookies(tokenCookiesFor($admin))->get(route('admin.departments'));

    $response->assertOk()
        ->assertSee('Gestion des Départements')
        ->assertSee('Ingénierie & IT')
        ->assertSee('Yasmine Trabelsi')
        ->assertSee('3 250,500 TND')
        ->assertSee('7 000,000 TND')
        ->assertSee('Conforme')
        ->assertSee("Voir l'équipe", false)
        ->assertDontSee('16.57')
        ->assertDontSee('16,57')
        ->assertDontSee('Vue Organigramme')
        ->assertDontSee('Cotisations CNSS');
});

it('filters and sorts departments on the server', function () {
    $admin = departmentAdminUser();
    $manager = departmentEmployee(['name' => 'Yasmine Trabelsi', 'department' => 'rh']);

    departmentRecord('it', 'Alpha', ['monthly_budget' => '9000.000']);
    departmentRecord('rh', 'Beta', ['monthly_budget' => '1000.000']);
    departmentRecord('finance', 'Gamma', ['manager_id' => $manager->id, 'monthly_budget' => '4000.000']);

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.departments', ['q' => 'Beta']))
        ->assertOk()
        ->assertSee('Beta')
        ->assertDontSee('Alpha')
        ->assertDontSee('Gamma');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.departments', ['q' => 'Yasmine']))
        ->assertOk()
        ->assertSee('Gamma')
        ->assertDontSee('Alpha')
        ->assertDontSee('Beta');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.departments', ['sort' => 'budget', 'direction' => 'asc']))
        ->assertOk()
        ->assertViewHas('departments', fn ($paginator) => $paginator->first()->department->name === 'Beta');
});

it('shows honest empty states', function () {
    $admin = departmentAdminUser();

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.departments'))
        ->assertOk()
        ->assertSee('Aucun département')
        ->assertSee('Créer un département');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.departments', ['q' => 'inexistant']))
        ->assertOk()
        ->assertSee('Aucun résultat')
        ->assertSee('Effacer la recherche');
});

it('paginates departments nine at a time', function () {
    $admin = departmentAdminUser();
    Department::factory()->count(10)->create();

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.departments'))
        ->assertOk()
        ->assertSee('Page 1 sur 2')
        ->assertViewHas('departments', fn ($paginator) => $paginator->count() === 9 && $paginator->total() === 10);
});

it('creates a department without touching presentation fields', function () {
    $admin = departmentAdminUser();
    $manager = departmentEmployee(['name' => 'Yasmine Trabelsi', 'position' => 'Directrice IT']);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.departments.store'), [
            'name' => 'Data & Analytics',
            'manager_id' => $manager->id,
            'code' => 'CC-DATA',
            'monthly_budget' => '12500.750',
            'description' => 'Pilotage analytique.',
            '_form' => 'create',
        ]);

    $response->assertRedirect(route('admin.departments'))
        ->assertSessionHas('success');

    $department = Department::query()->where('slug', 'data-analytics')->firstOrFail();

    expect($department->name)->toBe('Data & Analytics')
        ->and($department->manager_id)->toBe($manager->id)
        ->and($department->code)->toBe('CC-DATA')
        ->and($department->monthly_budget)->toBe('12500.750')
        ->and($department->description)->toBe('Pilotage analytique.')
        ->and($department->tag)->toBeNull()
        ->and($department->specialties)->toBeNull()
        ->and($department->tags)->toBeNull()
        ->and($department->sub_teams)->toBeNull()
        ->and($department->occupancy)->toBe(0);
});

it('validates department input with French messages', function () {
    $admin = departmentAdminUser();

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.departments.store'), [
            'name' => '',
            'manager_id' => 999999,
            'code' => str_repeat('b', 51),
            'monthly_budget' => '-5',
            'description' => str_repeat('a', 1001),
        ]);

    $response->assertSessionHasErrors(['name', 'manager_id', 'code', 'monthly_budget', 'description']);

    $errors = session('errors');

    expect($errors->first('name'))->toBe('Le nom du département est obligatoire.')
        ->and($errors->first('manager_id'))->toBe('Le responsable sélectionné est introuvable.')
        ->and($errors->first('code'))->toBe('Le code analytique ne peut pas dépasser 50 caractères.')
        ->and($errors->first('monthly_budget'))->toBe('Le budget mensuel ne peut pas être négatif.')
        ->and($errors->first('description'))->toBe('La description ne peut pas dépasser 1 000 caractères.');

    $this->assertDatabaseCount('departments', 0);
});

it('rejects budgets with more than three decimals', function () {
    $admin = departmentAdminUser();

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.departments.store'), [
            'name' => 'Data & Analytics',
            'monthly_budget' => '12.3456',
        ])
        ->assertSessionHasErrors('monthly_budget');

    expect(session('errors')->first('monthly_budget'))->toBe('Le budget mensuel accepte au maximum trois décimales.');
});

it('updates a department and clears optional fields', function () {
    $admin = departmentAdminUser();
    $manager = departmentEmployee();
    $department = departmentRecord('it', 'Ingénierie & IT', [
        'manager_id' => $manager->id,
        'code' => 'CC-IT',
        'monthly_budget' => '5000.000',
        'description' => 'Ancienne description.',
    ]);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->put(route('admin.departments.update', $department), [
            'name' => 'Ingénierie Produit',
            'manager_id' => '',
            'code' => '',
            'monthly_budget' => '',
            'description' => '',
        ]);

    $response->assertRedirect(route('admin.departments'))
        ->assertSessionHas('success');

    $department->refresh();

    expect($department->slug)->toBe('it')
        ->and($department->name)->toBe('Ingénierie Produit')
        ->and($department->manager_id)->toBeNull()
        ->and($department->code)->toBeNull()
        ->and($department->monthly_budget)->toBe('0.000')
        ->and($department->description)->toBeNull();
});

it('rejects duplicate department names on update', function () {
    $admin = departmentAdminUser();
    departmentRecord('it', 'Ingénierie & IT');
    $other = departmentRecord('rh', 'Ressources Humaines');

    $this->withCookies(tokenCookiesFor($admin))
        ->put(route('admin.departments.update', $other), ['name' => 'Ingénierie & IT'])
        ->assertSessionHasErrors('name');

    expect(session('errors')->first('name'))->toBe('Un département porte déjà ce nom.');
});

it('blocks deleting structural and staffed departments', function () {
    $admin = departmentAdminUser();
    $structural = departmentRecord('it', 'Ingénierie & IT');

    $this->withCookies(tokenCookiesFor($admin))
        ->delete(route('admin.departments.destroy', $structural))
        ->assertRedirect(route('admin.departments'))
        ->assertSessionHas('error');

    expect(session('error'))->toContain('module Collaborateurs');
    $this->assertDatabaseHas('departments', ['id' => $structural->id]);

    $staffed = departmentRecord('ops', 'Opérations');
    departmentEmployee(['department' => 'ops']);

    $this->withCookies(tokenCookiesFor($admin))
        ->delete(route('admin.departments.destroy', $staffed))
        ->assertRedirect(route('admin.departments'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('departments', ['id' => $staffed->id]);
});

it('deletes an empty custom department', function () {
    $admin = departmentAdminUser();
    $custom = departmentRecord('data', 'Data & Analytics');

    $this->withCookies(tokenCookiesFor($admin))
        ->delete(route('admin.departments.destroy', $custom))
        ->assertRedirect(route('admin.departments'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('departments', ['id' => $custom->id]);
});

it('exports the filtered synthesis as a french csv', function () {
    $admin = departmentAdminUser();
    $active = departmentEmployee(['department' => 'it']);
    $period = now('Africa/Tunis')->format('Y-m');

    departmentRecord('it', 'Ingénierie & IT', [
        'code' => 'CC-IT',
        'monthly_budget' => '7000.000',
    ]);
    departmentRecord('rh', 'Ressources Humaines', ['monthly_budget' => '1000.000']);
    Payslip::factory()->create([
        'employee_id' => $active->id,
        'period' => $period,
        'base_salary' => '3000.500',
        'prime' => '250.000',
    ]);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.departments.export', ['q' => 'Ingénierie']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('text/csv');

    $csv = $response->streamedContent();

    expect($csv)
        ->toContain('Département')
        ->toContain('Masse salariale brute (TND)')
        ->toContain('Ingénierie & IT')
        ->toContain('3250,500')
        ->toContain('7000,000')
        ->toContain('Conforme')
        ->not->toContain('Ressources Humaines')
        ->not->toContain('Cotisations CNSS');
});

it('neutralises formulas in exported department names', function () {
    $admin = departmentAdminUser();
    departmentRecord('formula', '=HYPERLINK("https://example.test")');

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.departments.export'));

    $response->assertOk();

    expect($response->streamedContent())->toContain("'=HYPERLINK");
});

it('protects department management from guests and collaborators', function () {
    $this->get(route('admin.departments'))->assertRedirect(route('login'));

    $collaborator = User::factory()->create([
        'role' => 'collaborator',
        'email_verified_at' => now(),
    ]);

    $this->withCookies(tokenCookiesFor($collaborator))
        ->get(route('admin.departments'))
        ->assertRedirect(route('dashboard'));

    $this->withCookies(tokenCookiesFor($collaborator))
        ->post(route('admin.departments.store'), ['name' => 'Interdit'])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseCount('departments', 0);
});

it('keeps the department page query count constant', function () {
    $admin = departmentAdminUser();
    $employee = departmentEmployee(['department' => 'it']);

    departmentRecord('it', 'Ingénierie & IT', ['monthly_budget' => '7000.000']);
    Payslip::factory()->create([
        'employee_id' => $employee->id,
        'period' => now('Africa/Tunis')->format('Y-m'),
        'base_salary' => '2500.000',
        'prime' => '100.000',
    ]);

    $this->withCookies(tokenCookiesFor($admin))->get(route('admin.departments'))->assertOk();

    DB::enableQueryLog();
    DB::flushQueryLog();
    $this->withCookies(tokenCookiesFor($admin))->get(route('admin.departments'))->assertOk();
    $singleCount = count(DB::getQueryLog());

    Department::factory()->count(8)->create();

    DB::flushQueryLog();
    $this->withCookies(tokenCookiesFor($admin))->get(route('admin.departments'))->assertOk();
    $manyCount = count(DB::getQueryLog());

    DB::disableQueryLog();

    expect($manyCount)->toBe($singleCount);
});
