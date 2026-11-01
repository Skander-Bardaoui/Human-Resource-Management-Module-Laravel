<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequest extends Model
{
    use HasFactory;

    public const TYPES = [
        'attestation_travail' => 'Attestation de travail',
        'attestation_salaire' => 'Attestation de salaire',
        'declaration_situation' => 'Déclaration de situation',
    ];

    public const STATUSES = [
        'pending' => 'En Attente',
        'processing' => 'En Traitement',
        'ready' => 'Prêt à récupérer',
        'rejected' => 'Rejeté',
    ];

    protected $fillable = [
        'employee_id',
        'type',
        'purpose',
        'status',
        'handled_at',
        'handled_by',
        'notes',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeForEmployee($query, Employee|int $employee)
    {
        return $query->where('employee_id', $employee instanceof Employee ? $employee->id : $employee);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusPillClasses(): string
    {
        return match ($this->status) {
            'ready' => 'bg-tertiary-fixed text-on-tertiary-fixed',
            'rejected' => 'bg-error-container text-on-error-container',
            'processing' => 'bg-secondary-container text-on-secondary-fixed',
            default => 'bg-surface-container text-on-surface-variant',
        };
    }

    public function reference(): string
    {
        return 'DR-'.$this->created_at?->format('Y').'-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
