<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    public const DEPARTMENTS = [
        'it' => 'Ingénierie & IT',
        'produit' => 'Produit & Design',
        'commerce' => 'Marketing & Sales',
        'finance' => 'Finance & Admin',
        'rh' => 'Ressources Humaines',
    ];

    public const DEPARTMENT_SHORT = [
        'it' => 'Tech',
        'produit' => 'Produit',
        'commerce' => 'Commerce',
        'finance' => 'Finance',
        'rh' => 'RH',
    ];

    public const STATUSES = [
        'actif' => 'Actif',
        'essai' => 'Période d\'essai',
        'conge' => 'En Congé',
        'inactif' => 'Inactif',
    ];

    public const CONTRACT_TYPES = [
        'CDI' => 'CDI',
        'CDD' => 'CDD',
        'SIVP' => 'SIVP',
        'Stage' => 'Stage',
    ];

    public const SENIORITY_CATEGORIES = [
        'senior' => '> 2 ans',
        'mid' => '1 à 2 ans',
        'junior' => 'Junior (< 1 an)',
    ];

    protected $fillable = [
        'name',
        'avatar_url',
        'position',
        'department',
        'contract_type',
        'hire_date',
        'email',
        'seniority',
        'status',
        'bank_account',
        'cin',
        'date_of_birth',
        'family_status',
        'children',
        'address',
        'mobile',
        'work_mobile',
        'emergency_contact_name',
        'emergency_contact_phone',
        'cnss_number',
        'cin_file_path',
        'employee_note',
        'leave_rights',
        'token_leave_days',
        'token_sick_leave_days',
        'start_time',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'date_of_birth' => 'date',
        'start_time' => 'datetime',
        'children' => 'integer',
        'leave_rights' => 'integer',
        'token_leave_days' => 'integer',
        'token_sick_leave_days' => 'integer',
    ];

    public function conges(): HasMany
    {
        return $this->hasMany(Conges::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function advanceRequests(): HasMany
    {
        return $this->hasMany(AdvanceRequest::class);
    }

    public function documentRequests(): HasMany
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function matricule(): string
    {
        return 'MAT-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $first = substr($parts[0] ?? '', 0, 1);
        $last = count($parts) > 1 ? substr(end($parts), 0, 1) : '';

        return strtoupper($first.$last) ?: '?';
    }

    public function seniorityYears(): ?int
    {
        if (! $this->hire_date) {
            return null;
        }

        return max(0, (int) $this->hire_date->diffInYears(now()));
    }

    public function seniorityCategory(): string
    {
        $years = $this->seniorityYears();

        if ($years === null) {
            return 'junior';
        }

        return $years >= 2 ? 'senior' : ($years >= 1 ? 'mid' : 'junior');
    }

    public function seniorityLabel(): string
    {
        $years = $this->seniorityYears();

        if ($years === null) {
            return 'Ancienneté non renseignée';
        }

        if ($years === 0) {
            return 'Nouveau collaborateur';
        }

        return $years.' an'.($years > 1 ? 's' : '')." d'ancienneté";
    }

    public function departmentLabel(): string
    {
        return self::DEPARTMENTS[$this->department] ?? $this->department;
    }

    public function departmentShortLabel(): string
    {
        return self::DEPARTMENT_SHORT[$this->department] ?? $this->departmentLabel();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function hasCompleteCnssProfile(): bool
    {
        return filled($this->cnss_number) && $this->cnss_number !== 'Non renseigné';
    }
}
