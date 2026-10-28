<?php

namespace App\Models;

use App\Support\Money;
use App\Support\PayrollCalculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    public const STATUSES = [
        'ready' => 'Virement Prêt',
        'review' => 'En Révision',
        'paid' => 'Payé',
    ];

    protected $fillable = [
        'employee_id',
        'period',
        'base_salary',
        'prime',
        'transport_allowance',
        'cnss',
        'taxable_base',
        'irpp',
        'css',
        'net',
        'status',
        'paid_at',
        'bank',
        'rib',
    ];

    protected $casts = [
        'base_salary' => 'decimal:3',
        'prime' => 'decimal:3',
        'transport_allowance' => 'decimal:3',
        'cnss' => 'decimal:3',
        'taxable_base' => 'decimal:3',
        'irpp' => 'decimal:3',
        'css' => 'decimal:3',
        'net' => 'decimal:3',
        'paid_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeForPeriod(Builder $query, string $period): Builder
    {
        return $query->where('period', $period);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusTone(): string
    {
        return match ($this->status) {
            'ready' => 'success',
            'review' => 'warning',
            'paid' => 'primary',
            default => 'neutral',
        };
    }

    public function matricule(): string
    {
        return 'MAT-'.str_pad((string) $this->employee_id, 4, '0', STR_PAD_LEFT);
    }

    public function baseSalary(): Money
    {
        return Money::fromDatabase($this->base_salary);
    }

    public function primeAmount(): Money
    {
        return Money::fromDatabase($this->prime);
    }

    public function transportAllowance(): Money
    {
        return Money::fromDatabase($this->transport_allowance);
    }

    public function cnssAmount(): Money
    {
        return Money::fromDatabase($this->cnss);
    }

    public function taxableBase(): Money
    {
        return Money::fromDatabase($this->taxable_base);
    }

    public function irppAmount(): Money
    {
        return Money::fromDatabase($this->irpp);
    }

    public function cssAmount(): Money
    {
        return Money::fromDatabase($this->css);
    }

    public function netAmount(): Money
    {
        return Money::fromDatabase($this->net);
    }

    public function gross(): Money
    {
        return $this->baseSalary()->plus($this->primeAmount());
    }

    public function employerCnss(): Money
    {
        return PayrollCalculator::employerCnss($this->gross());
    }
}
