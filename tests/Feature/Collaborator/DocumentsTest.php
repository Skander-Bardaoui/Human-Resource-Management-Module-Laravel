<?php

use App\Models\Document;
use App\Models\DocumentRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-19 09:00:00', 'Africa/Tunis'));
});

afterEach(function () {
    Carbon::setTestNow();
});

function collabDocument(?int $employeeId, string $name, array $overrides = []): Document
{
    return Document::create(array_merge([
        'employee_id' => $employeeId,
        'name' => $name,
        'original_name' => $name,
        'path' => 'documents/'.$name,
        'size' => 2048,
        'mime_type' => 'application/pdf',
        'category' => 'contrats',
        'status' => 'actif',
    ], $overrides));
}

it('lists only the collaborator own documents', function () {
    [$user, $employee] = collaboratorAccount();
    [, $otherEmployee] = collaboratorAccount();

    collabDocument($employee->id, 'Contrat_CDI_Self.pdf');
    collabDocument($otherEmployee->id, 'Contrat_CDI_Autre.pdf');
    collabDocument(null, 'Document_Sans_Collaborateur.pdf');

    $response = $this->withCookies(tokenCookiesFor($user))->get(route('collaborator.documents'));

    $response->assertOk()
        ->assertSee('Contrat_CDI_Self.pdf')
        ->assertDontSee('Contrat_CDI_Autre.pdf')
        ->assertDontSee('Document_Sans_Collaborateur.pdf');

    expect($response->viewData('documents'))->toHaveCount(1)
        ->and($response->viewData('categoryCounts')['contrats'])->toBe(1);
});

it('downloads only owned documents', function () {
    Storage::fake('local');

    [$user, $employee] = collaboratorAccount();
    [, $otherEmployee] = collaboratorAccount();

    $own = collabDocument($employee->id, 'Mon_Contrat.pdf');
    $foreign = collabDocument($otherEmployee->id, 'Contrat_Autre.pdf');

    Storage::disk('local')->put($own->path, '%PDF-1.4 test');

    $this->withCookies(tokenCookiesFor($user))
        ->get(route('collaborator.documents.download', $own))
        ->assertOk()
        ->assertDownload('Mon_Contrat.pdf');

    $this->withCookies(tokenCookiesFor($user))
        ->get(route('collaborator.documents.download', $foreign))
        ->assertNotFound();
});

it('persists a payroll attestation request', function () {
    [$user, $employee] = collaboratorAccount();

    $this->from(route('collaborator.documents'))
        ->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.documents.request'), [
            'type' => 'attestation_salaire',
            'purpose' => 'Dossier de crédit bancaire.',
        ])
        ->assertRedirect(route('collaborator.documents'))
        ->assertSessionHas('success');

    $request = DocumentRequest::first();

    expect($request->employee_id)->toBe($employee->id)
        ->and($request->type)->toBe('attestation_salaire')
        ->and($request->status)->toBe('pending')
        ->and($request->purpose)->toBe('Dossier de crédit bancaire.');
});

it('requires a purpose for a situation declaration', function () {
    [$user] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.documents.request'), ['type' => 'declaration_situation'])
        ->assertSessionHasErrors('purpose');

    expect(DocumentRequest::count())->toBe(0);
});

it('shows inline feedback and reopens the request modal after a validation failure', function () {
    [$user] = collaboratorAccount();

    $errors = (new ViewErrorBag)->put('default', new MessageBag([
        'purpose' => ['Précisez la situation à déclarer pour une déclaration de situation.'],
    ]));

    $response = $this->withCookies(tokenCookiesFor($user))
        ->withSession([
            'errors' => $errors,
            '_old_input' => ['form' => 'document_request', 'type' => 'declaration_situation'],
        ])
        ->get(route('collaborator.documents'));

    $response->assertOk()
        ->assertSee('Précisez la situation à déclarer', false)
        ->assertSee('name="form" value="document_request"', false)
        ->assertSee('name="form" value="profile_update"', false)
        ->assertSee('hrm-invalid', false)
        ->assertSee('openModal("requestModal")', false)
        ->assertDontSee('openModal("updateModal")', false);
});

it('generates and stores an instant work attestation', function () {
    Storage::fake('local');

    [$user, $employee] = collaboratorAccount();

    $this->from(route('collaborator.documents'))
        ->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.documents.attestation'))
        ->assertRedirect(route('collaborator.documents'))
        ->assertSessionHas('success');

    $document = Document::first();

    expect($document)->not->toBeNull()
        ->and($document->employee_id)->toBe($employee->id)
        ->and($document->category)->toBe('attestations')
        ->and($document->status)->toBe('conforme')
        ->and($document->mime_type)->toBe('application/pdf')
        ->and($document->size)->toBeGreaterThan(0);

    Storage::disk('local')->assertExists($document->path);
});

it('updates only the editable contact fields', function () {
    [$user, $employee] = collaboratorAccount(['name' => 'Nom Stable']);

    $this->from(route('collaborator.documents'))
        ->withCookies(tokenCookiesFor($user))
        ->patch(route('collaborator.documents.profile'), [
            'mobile' => '+216 55 123 456',
            'address' => '12 rue de Carthage, Tunis',
            'emergency_contact_name' => 'Leila Ben Salem',
            'emergency_contact_phone' => '+216 22 555 120',
        ])
        ->assertRedirect(route('collaborator.documents'))
        ->assertSessionHas('success');

    $employee->refresh();

    expect($employee->mobile)->toBe('+216 55 123 456')
        ->and($employee->address)->toBe('12 rue de Carthage, Tunis')
        ->and($employee->emergency_contact_name)->toBe('Leila Ben Salem')
        ->and($employee->emergency_contact_phone)->toBe('+216 22 555 120')
        ->and($employee->name)->toBe('Nom Stable');
});
