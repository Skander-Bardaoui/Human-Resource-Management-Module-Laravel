<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    const CATEGORIES = [
        'contrats' => 'Contrats de Travail (CDI/CDD/SIVP)',
        'identite' => 'Copies CIN & Passeport',
        'paie' => 'Fiches de Paie signées',
        'diplomes' => 'Diplômes & Certificats',
        'attestations' => 'Attestations & B3',
    ];

    const STATUSES = [
        'actif' => 'Actif',
        'en_attente_signature' => 'En attente de signature',
        'cloture' => 'Clôturé',
        'conforme' => 'Conforme',
    ];

    protected $fillable = [
        'employee_id',
        'name',
        'original_name',
        'path',
        'size',
        'mime_type',
        'category',
        'status',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeForEmployee($query, Employee|int $employee)
    {
        return $query->where('employee_id', $employee instanceof Employee ? $employee->id : $employee);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusPillClasses(): string
    {
        return match ($this->status) {
            'conforme', 'actif' => 'bg-tertiary-fixed text-on-tertiary-fixed',
            'en_attente_signature' => 'bg-secondary-container text-on-secondary-fixed',
            default => 'bg-surface-container text-on-surface-variant',
        };
    }

    public function sizeLabel(): string
    {
        $bytes = (int) $this->size;

        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / 1024 / 1024, 1, ',', ' ').' Mo';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', ' ').' Ko';
        }

        return $bytes.' o';
    }

    public function isExpiringSoon(int $days = 60): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->gte(now()->startOfDay())
            && $this->expires_at->lte(now()->addDays($days)->startOfDay());
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->lt(now()->startOfDay());
    }
}
