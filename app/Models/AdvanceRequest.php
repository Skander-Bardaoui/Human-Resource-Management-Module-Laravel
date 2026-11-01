<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvanceRequest extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending' => 'En Attente',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'paid' => 'Versé',
    ];

    public const OPEN_STATUSES = ['pending', 'approved'];

    protected $fillable = [
        'employee_id',
        'period',
        'amount',
        'reason',
        'status',
        'reviewed_at',
        'reviewed_by',
        'decision_note',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'reviewed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeForEmployee($query, Employee|int $employee)
    {
        return $query->where('employee_id', $employee instanceof Employee ? $employee->id : $employee);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusPillClasses(): string
    {
        return match ($this->status) {
            'approved' => 'bg-tertiary-fixed text-on-tertiary-fixed',
            'rejected' => 'bg-error-container text-on-error-container',
            'paid' => 'bg-surface-container text-on-surface',
            default => 'bg-secondary-container text-on-secondary-fixed',
        };
    }

    public function reference(): string
    {
        return 'AV-'.str_replace('-', '', (string) $this->period).'-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function periodLabel(): string
    {
        return ucfirst(
            Carbon::createFromFormat('Y-m', (string) $this->period)->locale('fr')->translatedFormat('F Y')
        );
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }
}
