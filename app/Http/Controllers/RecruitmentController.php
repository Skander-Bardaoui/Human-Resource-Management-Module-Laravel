<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecruitmentController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->query('view', 'kanban');

        $candidates = Candidate::query()
            ->with(['jobOffer', 'interviews'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('skills', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('department'), function ($query) use ($request) {
                $query->whereHas('jobOffer', function ($q) use ($request) {
                    $q->where('department', $request->query('department'));
                });
            })
            ->when($request->filled('contract_type'), function ($query) use ($request) {
                $query->where('contract_type_target', $request->query('contract_type'));
            })
            ->when($request->filled('source'), function ($query) use ($request) {
                $query->where('source', $request->query('source'));
            })
            ->get();

        $candidatesByStage = collect(Candidate::STAGES)->mapWithKeys(function ($label, $stage) use ($candidates) {
            return [$stage => $candidates->where('stage', $stage)->values()];
        });

        $offers = JobOffer::withCount('candidates')->get();

        $stats = [
            'active_candidates' => Candidate::where('stage', '!=', 'hired')->count(),
            'interviews_this_week' => Interview::whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'interviews_today' => Interview::whereDate('scheduled_at', today())->count(),
            'pending_offers' => JobOffer::where('status', 'pending_validation')->count(),
            'hired_this_month' => Candidate::where('stage', 'hired')
                ->whereMonth('hired_at', now()->month)
                ->whereYear('hired_at', now()->year)
                ->count(),
            'growth_pct' => $this->candidateGrowth(),
        ];

        $departments = JobOffer::distinct()->orderBy('department')->pluck('department')->filter()->values();

        return view('admin.recruitment', compact('view', 'candidatesByStage', 'offers', 'stats', 'departments'));
    }

    public function offers(Request $request)
    {
        $request->query->set('view', 'offers');

        return $this->index($request);
    }

    public function storeCandidate(Request $request)
    {
        $validated = $this->candidateRules($request);

        Candidate::create($validated);

        return redirect()->route('admin.recruitment')->with('success', 'Candidat ajouté avec succès.');
    }

    public function updateCandidate(Request $request, Candidate $candidate)
    {
        $validated = $this->candidateRules($request);

        $candidate->update($validated);

        if ($candidate->wasChanged('stage')) {
            $candidate->hired_at = $candidate->stage === 'hired' ? now() : null;
            $candidate->save();
        }

        return redirect()->route('admin.recruitment')->with('success', 'Candidat mis à jour.');
    }

    public function destroyCandidate(Candidate $candidate)
    {
        $candidate->delete();

        return redirect()->route('admin.recruitment')->with('success', 'Candidat supprimé.');
    }

    public function moveStage(Request $request, Candidate $candidate)
    {
        $request->validate([
            'stage' => ['required', Rule::in(array_keys(Candidate::STAGES))],
        ]);

        $candidate->stage = $request->stage;
        $candidate->hired_at = $request->stage === 'hired' ? now() : null;
        $candidate->save();

        return response()->json(['ok' => true]);
    }

    public function storeOffer(Request $request)
    {
        $validated = $this->offerRules($request);

        JobOffer::create($validated);

        return redirect()->route('admin.offers')->with('success', 'Offre créée avec succès.');
    }

    public function updateOffer(Request $request, JobOffer $jobOffer)
    {
        $validated = $this->offerRules($request);

        $jobOffer->update($validated);

        return redirect()->route('admin.offers')->with('success', 'Offre mise à jour.');
    }

    public function destroyOffer(JobOffer $jobOffer)
    {
        $jobOffer->delete();

        return redirect()->route('admin.offers')->with('success', 'Offre supprimée.');
    }

    public function storeInterview(Request $request, Candidate $candidate)
    {
        $request->validate([
            'scheduled_at' => ['required', 'date'],
            'interviewer' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Interview::TYPES))],
            'status' => ['nullable', Rule::in(array_keys(Interview::STATUSES))],
            'notes' => ['nullable', 'string'],
        ]);

        $candidate->interviews()->create([
            'scheduled_at' => $request->scheduled_at,
            'interviewer' => $request->interviewer,
            'type' => $request->type,
            'status' => $request->status ?? 'planned',
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.recruitment')->with('success', 'Entretien planifié.');
    }

    public function destroyInterview(Interview $interview)
    {
        $interview->delete();

        return redirect()->route('admin.recruitment')->with('success', 'Entretien supprimé.');
    }

    private function candidateGrowth(): ?int
    {
        $previous = Candidate::where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', now()->subDays(30))
            ->count();

        $current = Candidate::where('created_at', '>=', now()->subDays(30))->count();

        if ($current === 0) {
            return null;
        }

        if ($previous === 0) {
            return 100;
        }

        return (int) round((($current - $previous) / $previous) * 100);
    }

    private function candidateRules(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'job_offer_id' => ['nullable', 'exists:job_offers,id'],
            'avatar_url' => ['nullable', 'string', 'max:2048'],
            'match_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'skills' => ['nullable', 'string', 'max:1000'],
            'source' => ['nullable', 'string', 'max:255'],
            'salary_claim' => ['nullable', 'numeric', 'min:0'],
            'availability' => ['nullable', 'string', 'max:255'],
            'contract_type_target' => ['nullable', 'string', 'max:255'],
            'stage' => ['required', Rule::in(array_keys(Candidate::STAGES))],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function offerRules(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'contract_type' => ['required', Rule::in(JobOffer::CONTRACT_TYPES)],
            'status' => ['required', Rule::in(array_keys(JobOffer::STATUSES))],
            'location' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
