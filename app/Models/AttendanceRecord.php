<?php

namespace App\Models;

use Carbon\Carbon;
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
        'biometric' => 'Terminal biométrique',
        'rfid' => 'Badge RFID',
        'web' => 'Portail web',
        'manual' => 'Saisie manuelle RH',
        'none' => 'Aucun enregistrement',
    ];

    public const DIRECTIONS = [
        'in' => 'Entrée / Arrivée',
        'out' => 'Sortie / Départ',
        'pause_in' => 'Début de pause',
        'pause_out' => 'Fin de pause',
    ];

    public const MOTIFS = [
        'forgot' => 'Oubli de badge',
        'failure' => 'Panne du terminal',
        'mission' => 'Mission externe',
        'other' => 'Autre justification',
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

        return max(0, $in - self::scheduleStartMinutes());
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
        return $this->check_in !== null
            && ! in_array($this->status, ['absent', 'leave'], true)
            && $this->lateMinutes() > self::lateToleranceMinutes();
    }

    public function hasMissingCheckout(): bool
    {
        return $this->check_in !== null
            && $this->check_out === null
            && ! in_array($this->status, ['absent', 'leave'], true)
            && $this->work_date !== null
            && $this->work_date->toDateString() < self::currentDate()->toDateString();
    }

    public function hasEarlyExit(): bool
    {
        return $this->check_in !== null
            && $this->check_out !== null
            && ! in_array($this->status, ['absent', 'leave'], true)
            && $this->workedMinutes() < self::minimumFullDayMinutes();
    }

    public function overtimeMinutes(): int
    {
        return max(0, $this->workedMinutes() - self::overtimeThresholdMinutes());
    }

    public function hasAnomalySignal(): bool
    {
        return $this->hasMissingCheckout() || $this->hasEarlyExit() || $this->isLateArrival();
    }

    public function isAnomaly(): bool
    {
        return ! $this->anomaly_ignored && $this->hasAnomalySignal();
    }

    public static function currentDate(): Carbon
    {
        return Carbon::now(config('attendance.timezone', 'Africa/Tunis'))->startOfDay();
    }

    public static function scheduleStartMinutes(): int
    {
        return self::scheduleMinutes('start', 8 * 60 + 30);
    }

    public static function scheduleEndTime(): string
    {
        $value = (string) config('attendance.schedule.end', '17:30');

        return preg_match('/^\d{2}:\d{2}:\d{2}$/', $value) === 1
            ? $value
            : substr($value, 0, 5).':00';
    }

    public static function lateToleranceMinutes(): int
    {
        return max(0, (int) config('attendance.late_tolerance_minutes', 15));
    }

    public static function minimumFullDayMinutes(): int
    {
        return max(1, (int) config('attendance.minimum_full_day_minutes', 450));
    }

    public static function overtimeThresholdMinutes(): int
    {
        return max(1, (int) config('attendance.overtime_threshold_minutes', 480));
    }

    public static function timeToMinutes(?string $time): ?int
    {
        if (! $time) {
            return null;
        }

        [$hours, $minutes] = array_pad(explode(':', $time), 2, 0);

        return (int) $hours * 60 + (int) $minutes;
    }

    private static function scheduleMinutes(string $key, int $fallback): int
    {
        $value = (string) config('attendance.schedule.'.$key, '');

        if (preg_match('/^(\d{1,2}):(\d{2})/', $value, $matches) !== 1) {
            return $fallback;
        }

        return (int) $matches[1] * 60 + (int) $matches[2];
    }

    private function shortTime(?string $time): ?string
    {
        return $time ? substr($time, 0, 5) : null;
    }

    private function minutesOf(?string $time): ?int
    {
        return self::timeToMinutes($time);
    }
}
