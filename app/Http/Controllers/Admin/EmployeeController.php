<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CollaboratorAcceptedMail;
use App\Models\Candidate;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);

        $employees = $this->filteredQuery($filters)
            ->orderBy('name')
            ->paginate(8)
            ->appends(array_filter($filters, fn ($value) => $value !== ''));

        $editEmployee = $request->filled('edit') ? Employee::find($request->integer('edit')) : null;
        $viewEmployee = $request->filled('view') ? Employee::find($request->integer('view')) : null;
        $onboardValues = $this->onboardValues($request);

        $totalEmployees = Employee::count();
        $activeProfiles = Employee::where('status', '!=', 'inactif')->count();
        $probationCount = Employee::where('status', 'essai')->count();
        $onLeaveCount = Employee::where('status', 'conge')->count();
        $cnssComplete = Employee::whereNotNull('cnss_number')
            ->whereNotIn('cnss_number', ['', 'Non renseigné'])
            ->count();
        $cnssRate = $totalEmployees > 0 ? (int) round(($cnssComplete / $totalEmployees) * 100) : 0;
        $newThisQuarter = Employee::whereNotNull('hire_date')
            ->whereDate('hire_date', '>=', now()->startOfQuarter())
            ->count();

        return view('admin.employees', compact(
            'employees',
            'filters',
            'editEmployee',
            'viewEmployee',
            'onboardValues',
            'totalEmployees',
            'activeProfiles',
            'probationCount',
            'onLeaveCount',
            'cnssComplete',
            'cnssRate',
            'newThisQuarter',
        ));
    }

    public function store(Request $request)
    {
        $payload = $this->payload($request);

        if ($request->hasFile('cin_file')) {
            $payload['cin_file_path'] = $request->file('cin_file')->store('cin-documents', 'local');
        }

        $employee = Employee::create($payload);

        if ($request->filled('candidate_id')) {
            Candidate::where('id', $request->integer('candidate_id'))
                ->whereNull('onboarded_at')
                ->update(['onboarded_at' => now()]);
        }

        $password = Str::password(12);

        User::updateOrCreate(
            ['email' => $employee->email],
            [
                'name' => $employee->name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        try {
            Mail::to($employee->email)->send(new CollaboratorAcceptedMail($employee, $password));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('admin.employees')
            ->with('success', 'Le collaborateur '.$employee->name.' a été ajouté avec succès.');
    }

    public function update(Request $request, Employee $employee)
    {
        $payload = $this->payload($request, $employee);

        if ($request->boolean('remove_cin_file')) {
            $this->deleteCinFile($employee);
            $payload['cin_file_path'] = null;
        }

        if ($request->hasFile('cin_file')) {
            $this->deleteCinFile($employee);
            $payload['cin_file_path'] = $request->file('cin_file')->store('cin-documents', 'local');
        }

        $employee->update($payload);

        return redirect()
            ->route('admin.employees')
            ->with('success', 'Le profil de '.$employee->name.' a été mis à jour.');
    }

    public function destroy(Employee $employee)
    {
        $this->deleteCinFile($employee);
        $employee->delete();

        return redirect()
            ->route('admin.employees')
            ->with('success', 'Le collaborateur a été supprimé du registre.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:employees,id'],
        ]);

        $employees = Employee::whereIn('id', $validated['ids'])->get();

        foreach ($employees as $employee) {
            $this->deleteCinFile($employee);
            $employee->delete();
        }

        return redirect()
            ->route('admin.employees')
            ->with('success', $employees->count().' collaborateur(s) supprimé(s) du registre.');
    }

    public function export(Request $request): StreamedResponse
    {
        $employees = $this->filteredQuery($this->filters($request))
            ->orderBy('name')
            ->get();

        return response()->streamDownload(function () use ($employees) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Nom',
                'Email',
                'Poste',
                'Département',
                'Contrat',
                'Statut',
                'CIN',
                'Mobile',
                'CNSS',
                'Date d\'embauche',
                'Ancienneté',
                'Droits congés',
            ], ';');

            foreach ($employees as $employee) {
                fputcsv($handle, [
                    $employee->name,
                    $employee->email,
                    $employee->position,
                    $employee->departmentLabel(),
                    $employee->contract_type,
                    $employee->statusLabel(),
                    $employee->cin,
                    $employee->mobile,
                    $employee->cnss_number,
                    $employee->hire_date?->format('d/m/Y'),
                    $employee->seniorityLabel(),
                    $employee->leave_rights,
                ], ';');
            }

            fclose($handle);
        }, 'collaborateurs-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadCin(Employee $employee)
    {
        abort_unless(
            $employee->cin_file_path && Storage::disk('local')->exists($employee->cin_file_path),
            404
        );

        $extension = pathinfo($employee->cin_file_path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download(
            $employee->cin_file_path,
            'CIN-'.$employee->cin.($extension ? '.'.$extension : '')
        );
    }

    private function onboardValues(Request $request): ?array
    {
        if (! $request->filled('onboard')) {
            return null;
        }

        $candidate = Candidate::find($request->integer('onboard'));

        if (! $candidate || $candidate->stage !== 'hired' || $candidate->onboarded_at) {
            return null;
        }

        $nameParts = preg_split('/\s+/', trim($candidate->name)) ?: [];
        $departmentMap = [
            'Ingénierie & R&D' => 'it',
            'Produit & Design' => 'produit',
            'Commercial & Marketing' => 'commerce',
            'Finance & Opérations RH' => 'finance',
        ];
        $contractType = in_array($candidate->contract_type_target, array_keys(Employee::CONTRACT_TYPES), true)
            ? $candidate->contract_type_target
            : 'CDI';

        return [
            'candidate_id' => $candidate->id,
            'candidate_name' => $candidate->name,
            'firstname' => $nameParts[0] ?? '',
            'lastname' => count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '',
            'email' => $candidate->email ?? '',
            'mobile' => $candidate->phone ?? '',
            'position' => $candidate->position,
            'department' => $departmentMap[$candidate->jobOffer?->department] ?? 'it',
            'contract_type' => $contractType,
            'hire_date' => $candidate->hired_at?->format('Y-m-d'),
            'status' => 'essai',
        ];
    }

    private function filters(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'department' => (string) $request->query('department', ''),
            'status' => (string) $request->query('status', ''),
            'seniority' => (string) $request->query('seniority', ''),
        ];
    }

    private function filteredQuery(array $filters)
    {
        return Employee::query()
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $term = '%'.$filters['q'].'%';

                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('cin', 'like', $term)
                        ->orWhere('position', 'like', $term)
                        ->orWhere('mobile', 'like', $term)
                        ->orWhere('cnss_number', 'like', $term);
                });
            })
            ->when(
                array_key_exists($filters['department'], Employee::DEPARTMENTS),
                fn ($query) => $query->where('department', $filters['department'])
            )
            ->when(
                array_key_exists($filters['status'], Employee::STATUSES),
                fn ($query) => $query->where('status', $filters['status'])
            )
            ->when(
                in_array($filters['seniority'], array_keys(Employee::SENIORITY_CATEGORIES), true),
                fn ($query) => $query->where('seniority', $filters['seniority'])
            );
    }

    private function payload(Request $request, ?Employee $employee = null): array
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'cin' => ['required', 'digits:8'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee?->id),
            ],
            'mobile' => ['required', 'string', 'max:30'],
            'family_status' => ['nullable', Rule::in(['celibataire', 'marie', 'divorce', 'veuf'])],
            'children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'department' => ['required', Rule::in(array_keys(Employee::DEPARTMENTS))],
            'contract_type' => ['required', Rule::in(array_keys(Employee::CONTRACT_TYPES))],
            'hire_date' => ['required', 'date', 'before_or_equal:today'],
            'work_mobile' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(array_keys(Employee::STATUSES))],
            'cnss_number' => ['nullable', 'string', 'max:50'],
            'bank_account' => ['nullable', 'string', 'max:100'],
            'leave_rights' => ['nullable', 'integer', 'min:0', 'max:365'],
            'token_leave_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'token_sick_leave_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'employee_note' => ['nullable', 'string', 'max:2000'],
            'cin_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'remove_cin_file' => ['nullable', 'boolean'],
            'candidate_id' => ['nullable', 'integer', 'exists:candidates,id'],
        ]);

        $hireDate = Carbon::parse($validated['hire_date']);
        $years = max(0, (int) $hireDate->diffInYears(now()));
        $mobile = $this->normalizePhone($validated['mobile']);
        $workMobile = filled($validated['work_mobile'] ?? null)
            ? $this->normalizePhone($validated['work_mobile'])
            : $mobile;

        return [
            'name' => trim($validated['firstname'].' '.$validated['lastname']),
            'position' => $validated['position'],
            'department' => $validated['department'],
            'contract_type' => $validated['contract_type'],
            'hire_date' => $hireDate->toDateString(),
            'email' => $validated['email'],
            'seniority' => $years >= 2 ? 'senior' : ($years >= 1 ? 'mid' : 'junior'),
            'status' => $validated['status'],
            'bank_account' => filled($validated['bank_account'] ?? null)
                ? trim($validated['bank_account'])
                : 'Non renseigné',
            'cin' => $validated['cin'],
            'date_of_birth' => $validated['date_of_birth'],
            'family_status' => $validated['family_status'] ?? null,
            'children' => $validated['children'] ?? 0,
            'address' => $validated['address'] ?? null,
            'mobile' => $mobile,
            'work_mobile' => $workMobile,
            'cnss_number' => filled($validated['cnss_number'] ?? null)
                ? trim($validated['cnss_number'])
                : 'Non renseigné',
            'employee_note' => $validated['employee_note'] ?? null,
            'leave_rights' => $validated['leave_rights'] ?? 30,
            'token_leave_days' => $validated['token_leave_days'] ?? 30,
            'token_sick_leave_days' => $validated['token_sick_leave_days'] ?? 15,
            'start_time' => $hireDate->copy()->setTime(8, 0),
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', ' ', trim($phone));

        if (str_starts_with($phone, '00216')) {
            return '+216 '.ltrim(substr($phone, 5));
        }

        if (str_starts_with($phone, '216')) {
            return '+216 '.ltrim(substr($phone, 3));
        }

        if (! str_starts_with($phone, '+')) {
            return '+216 '.ltrim($phone, '0');
        }

        return $phone;
    }

    private function deleteCinFile(Employee $employee): void
    {
        if ($employee->cin_file_path) {
            Storage::disk('local')->delete($employee->cin_file_path);
        }
    }
}
