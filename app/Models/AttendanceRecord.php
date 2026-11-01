<?php

namespace App\Models;

use Carbon\Carbon;
use App\Support\AttendanceClock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    public const STATUSES = [
        'present' => 'Présent',
        'late' => 'Retard Notifié',
        'remote' => 'Télétravail Validé',
        'absent' => 'Absent Injustifié',
        'leave' => 'En Congé',
    ];

    public const MODES = [
        'biometric' => 'Biométrie Lac 2',
        'rfid' => 'Badge RFID RDC',
        'web' => 'Portail Web SSO',
        'manual' => 'Saisie Manuelle RH',
        'none' => 'Aucun enregistrement',
    ];

    protected $fillable = [
        'employee_id',
        'work_date',
        'check_in',
        'check_out',
        'break_start',
        'break_end',
        'break_minutes',
        'mode',
        'status',
        'notes',
        'anomaly_ignored',
    ];

    protected $casts = [
        'work_date' => 'date',
        'break_minutes' => 'integer',
        'anomaly_ignored' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeForEmployee($query, Employee|int $employee)
    {
        return $query->where('employee_id', $employee instanceof Employee ? $employee->id : $employee);
    }

    public function isOpenSession(): bool
    {
        return $this->check_in !== null && $this->check_out === null;
    }

    public function isOnBreak(): bool
    {
        return $this->break_start !== null && $this->break_end === null;
    }

    public function effectiveBreakMinutes(): int
    {
        $minutes = (int) $this->break_minutes;

        if ($this->isOnBreak()) {
            $start = AttendanceClock::combine($this->work_date, (string) $this->break_start);
            $minutes += max(0, (int) $start->diffInMinutes(AttendanceClock::now()));
        }

        return $minutes;
    }

    public function liveWorkedMinutes(): int
    {
        $in = $this->minutesOf($this->check_in);

        if ($in === null) {
            return 0;
        }

        $out = $this->check_out !== null
            ? $this->minutesOf($this->check_out)
            : AttendanceClock::now()->hour * 60 + AttendanceClock::now()->minute;

        return max(0, $out - $in - $this->effectiveBreakMinutes());
    }

    public function checkInShort(): ?string
    {
        return $this->shortTime($this->check_in);
    }

    public function checkOutShort(): ?string
    {
        return $this->shortTime($this->check_out);
    }

    public function breakStartShort(): ?string
    {
        return $this->shortTime($this->break_start);
    }

    public function breakEndShort(): ?string
    {
        return $this->shortTime($this->break_end);
    }

    public function workedMinutes(): int
    {
        $in = $this->minutesOf($this->check_in);
        $out = $this->minutesOf($this->check_out);

        if ($in === null || $out === null || $out <= $in) {
            return 0;
        }

        return max(0, $out - $in - (int) $this->break_minutes);
    }

    public function lateMinutes(): int
    {
        $in = $this->minutesOf($this->check_in);

        if ($in === null) {
            return 0;
        }

        return max(0, $in - (8 * 60 + 30));
    }

    public function workedLabel(): string
    {
        $minutes = $this->workedMinutes();

        return intdiv($minutes, 60).'h '.str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT).'m';
    }

    public function breakLabel(): string
    {
        if ($this->break_minutes <= 0) {
            return '—';
        }

        return $this->break_minutes.' min';
    }

    public function modeLabel(): string
    {
        return self::MODES[$this->mode] ?? $this->mode;
    }

    public function modeIcon(): string
    {
        return match ($this->mode) {
            'biometric' => 'fingerprint',
            'rfid' => 'contactless',
            'web' => 'laptop_mac',
            'manual' => 'edit_note',
            default => 'block',
        };
    }

    public function modeColor(): string
    {
        return match ($this->mode) {
            'biometric' => 'text-primary',
            'rfid' => 'text-secondary',
            'web' => 'text-primary-container',
            'manual' => 'text-tertiary',
            default => 'text-on-surface-variant',
        };
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusPillClasses(): string
    {
        return match ($this->status) {
            'present' => 'bg-surface-container text-tertiary',
            'late' => 'bg-error-container/40 text-error',
            'remote' => 'bg-secondary-container text-on-secondary-fixed',
            'absent' => 'bg-error-container text-on-error-container',
            default => 'bg-surface-container text-on-surface-variant',
        };
    }

    public function statusDotClasses(): string
    {
        return match ($this->status) {
            'present' => 'bg-tertiary',
            'late' => 'bg-error',
            'remote' => 'bg-primary-container',
            'absent' => 'bg-error',
            default => 'bg-outline',
        };
    }

    public function rowClasses(): string
    {
        return match ($this->status) {
            'late' => 'bg-error-container/10',
            'absent' => 'bg-error-container/20',
            default => '',
        };
    }

    public function isLateArrival(): bool
    {
        return $this->check_in !== null && $this->lateMinutes() > 15;
    }

    public function hasMissingCheckout(): bool
    {
        return $this->check_in !== null && $this->check_out === null && $this->status !== 'absent' && $this->status !== 'leave';
    }

    public function hasEarlyExit(): bool
    {
        return $this->check_in !== null && $this->check_out !== null && $this->workedMinutes() < 450;
    }

    private function shortTime(?string $time): ?string
    {
        return $time ? substr($time, 0, 5) : null;
    }

    private function minutesOf(?string $time): ?int
    {
        if (! $time) {
            return null;
        }

        [$hours, $minutes] = array_pad(explode(':', $time), 2, 0);

        return (int) $hours * 60 + (int) $minutes;
    }
}
