<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use App\Support\AttendanceClock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conges extends Model
{
    use HasFactory;

    public const TYPES = [
        'annuel' => 'Congé Annuel Payé',
        'maladie' => 'Congé Maladie',
        'famille' => 'Événement Familial',
        'sans-solde' => 'Sans Solde',
        'recup' => 'Récupération Heures Sup.',
    ];

    public const STATUSES = [
        'pending' => 'En Attente',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'cancelled' => 'Annulé',
    ];

    public const TYPE_ICONS = [
        'annuel' => 'beach_access',
        'maladie' => 'medical_services',
        'famille' => 'favorite',
        'sans-solde' => 'money_off',
        'recup' => 'hotel',
    ];

    public const TYPE_COLORS = [
        'annuel' => 'text-primary',
        'maladie' => 'text-error',
        'famille' => 'text-primary',
        'sans-solde' => 'text-on-surface-variant',
        'recup' => 'text-on-surface-variant',
    ];

    public const REASON_ICONS = [
        'annuel' => 'chat_bubble_outline',
        'maladie' => 'assignment',
        'famille' => 'gavel',
        'sans-solde' => 'assignment',
        'recup' => 'hotel',
    ];

    protected $fillable = [
        'employee_id',
        'employee_name',
        'type',
        'status',
        'start_date',
        'end_date',
        'working_days',
        'reason',
        'attachment_path',
        'attachment_name',
        'reviewed_at',
        'reviewed_by',
        'decision_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'working_days' => 'integer',
        'employee_id' => 'integer',
        'reviewed_by' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public static function workingDaysFor(CarbonInterface|string $start, CarbonInterface|string $end): int
    {
        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->startOfDay();

        if ($end->lt($start)) {
            return 0;
        }

        $days = 0;

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            if ($day->dayOfWeek !== Carbon::SUNDAY) {
                $days++;
            }
        }

        return $days;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCancellable(): bool
    {
        if ($this->start_date === null || $this->end_date === null) {
            return false;
        }

        $today = AttendanceClock::today()->toDateString();

        if ($this->isPending()) {
            return $this->start_date->toDateString() >= $today;
        }

        if ($this->isApproved()) {
            return $this->end_date->toDateString() >= $today;
        }

        return false;
    }

    public function isInProgress(): bool
    {
        if (! $this->isApproved() || $this->start_date === null || $this->end_date === null) {
            return false;
        }

        $today = AttendanceClock::today()->toDateString();

        return $this->start_date->toDateString() <= $today
            && $this->end_date->toDateString() >= $today;
    }

    public function remainingWorkingDays(): int
    {
        if ($this->start_date === null || $this->end_date === null) {
            return 0;
        }

        $today = AttendanceClock::today();
        $timezone = AttendanceClock::timezone();

        $start = $this->start_date->toDateString() >= $today->toDateString()
            ? Carbon::parse($this->start_date->toDateString(), $timezone)
            : $today->copy();
        $end = Carbon::parse($this->end_date->toDateString(), $timezone);

        if ($end->lt($start)) {
            return 0;
        }

        return self::workingDaysFor($start, $end);
    }

    public function scopeForEmployee($query, Employee|int $employee)
    {
        return $query->where('employee_id', $employee instanceof Employee ? $employee->id : $employee);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }

    public function reference(): string
    {
        return 'CG-'.($this->start_date?->format('Y') ?? now()->format('Y'))
            .'-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function typeIcon(): string
    {
        return self::TYPE_ICONS[$this->type] ?? 'event_note';
    }

    public function typeColor(): string
    {
        return self::TYPE_COLORS[$this->type] ?? 'text-on-surface-variant';
    }

    public function reasonIcon(): string
    {
        return self::REASON_ICONS[$this->type] ?? 'chat_bubble_outline';
    }

    public function employeeName(): string
    {
        return $this->employee?->name ?? $this->employee_name;
    }

    public function employeeInitials(): string
    {
        if ($this->employee) {
            return $this->employee->initials();
        }

        $parts = preg_split('/\s+/', trim((string) $this->employee_name));
        $first = substr($parts[0] ?? '', 0, 1);
        $last = count($parts) > 1 ? substr(end($parts), 0, 1) : '';

        return strtoupper($first.$last) ?: '?';
    }

    public function employeeAvatar(): ?string
    {
        return $this->employee?->avatar_url;
    }

    public function employeePosition(): string
    {
        return $this->employee?->position ?? 'Collaborateur';
    }

    public function employeeDepartment(): string
    {
        return $this->employee?->departmentLabel() ?? 'Non renseigné';
    }

    public function employeeDepartmentShort(): string
    {
        return $this->employee?->departmentShortLabel() ?? 'Non renseigné';
    }

    public function employeeCin(): ?string
    {
        return $this->employee?->cin;
    }

    public function employeeEmail(): ?string
    {
        return $this->employee?->email;
    }

    public function employeeMobile(): ?string
    {
        return $this->employee?->mobile;
    }

    public function balanceDays(): ?int
    {
        $balance = $this->employee?->token_leave_days;

        return $balance === null ? null : (int) $balance;
    }

    public function balanceAfterDays(): ?int
    {
        $balance = $this->balanceDays();

        if ($balance === null) {
            return null;
        }

        if ($this->type === 'annuel' && $this->isPending()) {
            return max(0, $balance - (int) $this->working_days);
        }

        return $balance;
    }

    public function balanceCaption(): string
    {
        if ($this->type === 'annuel') {
            return match ($this->status) {
                'pending' => 'projeté',
                'approved' => 'Validé RH',
                'rejected' => 'inchangé',
                'cancelled' => 'annulé',
                default => '',
            };
        }

        return match ($this->type) {
            'maladie' => 'CNSS',
            'famille' => 'Hors annuel',
            'sans-solde', 'recup' => 'Sans impact',
            default => '',
        };
    }

    public function periodNote(): string
    {
        if ($this->isCancelled()) {
            return 'Demande annulée';
        }

        if ($this->start_date->equalTo($this->end_date)) {
            return 'Journée complète';
        }

        if ($this->isPending()) {
            return $this->start_date->gt(now())
                ? 'Départ '.$this->frenchWeekday($this->start_date).' matin'
                : 'En attente de validation';
        }

        if ($this->isRejected()) {
            return 'Demande rejetée';
        }

        if ($this->start_date->lte(now()) && $this->end_date->gte(now())) {
            return 'En cours';
        }

        if ($this->end_date->lt(now())) {
            return 'Période clôturée';
        }

        return $this->working_days.' jour'.($this->working_days > 1 ? 's' : '').' ouvré'.($this->working_days > 1 ? 's' : '');
    }

    private function frenchWeekday(Carbon $date): string
    {
        return ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'][$date->dayOfWeek];
    }
}
