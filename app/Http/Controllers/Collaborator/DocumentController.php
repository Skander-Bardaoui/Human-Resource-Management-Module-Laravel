<?php

namespace App\Http\Controllers\Collaborator;

use App\Models\Document;
use App\Models\DocumentRequest;
use App\Support\AttendanceClock;
use App\Support\HrDocumentPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends CollaboratorController
{
    public function index(Request $request)
    {
        $employee = $this->employee($request);

        $documents = Document::query()
            ->forEmployee($employee)
            ->orderByDesc('created_at')
            ->get();

        $categoryCounts = collect(Document::CATEGORIES)
            ->mapWithKeys(fn (string $label, string $key) => [$key => $documents->where('category', $key)->count()]);

        $requests = DocumentRequest::query()
            ->forEmployee($employee)
            ->latest()
            ->limit(10)
            ->get();

        return view('collaborator.documents', [
            'employee' => $employee,
            'documents' => $documents,
            'categoryCounts' => $categoryCounts,
            'totalSize' => $documents->sum('size'),
            'expiringCount' => $documents->filter(fn (Document $document) => $document->isExpiringSoon())->count(),
            'requests' => $requests,
            'requestTypes' => DocumentRequest::TYPES,
            'requestStatuses' => DocumentRequest::STATUSES,
        ]);
    }

    public function download(Request $request, Document $document)
    {
        $employee = $this->employee($request);
        $this->assertOwned($document, $employee);

        if (! Storage::disk('local')->exists($document->path)) {
            return back()->with('error', 'Fichier introuvable sur le disque.');
        }

        return Storage::disk('local')->download($document->path, $document->original_name ?: $document->name);
    }

    public function requestStore(Request $request)
    {
        $employee = $this->employee($request);

        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(DocumentRequest::TYPES))],
            'purpose' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['type'] === 'declaration_situation' && blank($validated['purpose'] ?? null)) {
            return back()->withInput()->withErrors([
                'purpose' => 'Précisez la situation à déclarer pour une déclaration de situation.',
            ]);
        }

        DocumentRequest::create([
            'employee_id' => $employee->id,
            'type' => $validated['type'],
            'purpose' => $validated['purpose'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Demande enregistrée. Le service RH vous notifiera dès que votre document sera prêt.');
    }

    public function attestation(Request $request)
    {
        $employee = $this->employee($request);
        $issuedAt = AttendanceClock::now();
        $pdf = HrDocumentPdf::attestationTravail($employee, $issuedAt);

        $filename = 'attestation-travail-'.$issuedAt->format('Ymd-His').'.pdf';
        $path = 'documents/generated/'.$employee->id.'/'.$filename;

        Storage::disk('local')->put($path, $pdf);

        Document::create([
            'employee_id' => $employee->id,
            'name' => 'Attestation de Travail - '.$issuedAt->format('d/m/Y'),
            'original_name' => $employee->matricule().'-attestation-travail-'.$issuedAt->format('Ymd').'.pdf',
            'path' => $path,
            'size' => strlen($pdf),
            'mime_type' => 'application/pdf',
            'category' => 'attestations',
            'status' => 'conforme',
            'notes' => 'Généré automatiquement depuis le portail collaborateur.',
        ]);

        return back()->with('success', 'Votre attestation de travail certifiée est prête et disponible dans le coffre-fort.');
    }

    public function updateProfile(Request $request)
    {
        $employee = $this->employee($request);

        $validated = $request->validate([
            'mobile' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:120'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $employee->update($validated);

        return back()->with('success', 'Vos coordonnées ont été mises à jour.');
    }
}
