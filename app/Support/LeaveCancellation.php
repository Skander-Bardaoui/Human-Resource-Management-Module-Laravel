<?php

namespace App\Support;

use App\Models\Conges;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveCancellation
{
    public const CANCELLED = 'cancelled';

    public const INTERRUPTED = 'interrupted';

    /**
     * Stop a pending or approved leave. Approved leaves that already started are
     * truncated at yesterday so the consumed days stay accounted for.
     *
     * @return array{result: string, restored_days: int}|null
     */
    public static function stop(Conges $leave, string $actor, ?int $reviewedBy = null): ?array
    {
        return DB::transaction(function () use ($leave, $actor, $reviewedBy) {
            $locked = Conges::query()->lockForUpdate()->find($leave->id);

            if (! $locked || ! $locked->isCancellable()) {
                return null;
            }

            $now = AttendanceClock::now();
            $stamp = $now->format('d/m/Y H:i');
            $employee = $locked->employee;
            $storedDays = (int) $locked->working_days;

            if ($locked->isPending()) {
                $locked->update([
                    'status' => 'cancelled',
                    'reviewed_at' => $now,
                    'reviewed_by' => $reviewedBy ?? $locked->reviewed_by,
                    'decision_note' => 'Annulée par '.$actor.' le '.$stamp.'.',
                ]);

                return ['result' => self::CANCELLED, 'restored_days' => 0];
            }

            $start = Carbon::parse($locked->start_date->toDateString(), AttendanceClock::timezone());
            $today = AttendanceClock::today();

            if ($start->gte($today)) {
                $restored = $locked->type === 'annuel' ? $storedDays : 0;

                $locked->update([
                    'status' => 'cancelled',
                    'reviewed_at' => $now,
                    'reviewed_by' => $reviewedBy ?? $locked->reviewed_by,
                    'decision_note' => 'Annulée par '.$actor.' le '.$stamp
                        .' (congé approuvé, '.$restored.' jour(s) réintégré(s)).',
                ]);

                self::restoreBalance($employee, $restored);

                return ['result' => self::CANCELLED, 'restored_days' => $restored];
            }

            $lastDay = $today->copy()->subDay();
            $consumed = min($storedDays, Conges::workingDaysFor($start, $lastDay));

            if ($consumed < 1) {
                $restored = $locked->type === 'annuel' ? $storedDays : 0;

                $locked->update([
                    'status' => 'cancelled',
                    'reviewed_at' => $now,
                    'reviewed_by' => $reviewedBy ?? $locked->reviewed_by,
                    'decision_note' => 'Annulée par '.$actor.' le '.$stamp
                        .' (congé approuvé, '.$restored.' jour(s) réintégré(s)).',
                ]);

                self::restoreBalance($employee, $restored);

                return ['result' => self::CANCELLED, 'restored_days' => $restored];
            }

            $restored = $locked->type === 'annuel' ? max(0, $storedDays - $consumed) : 0;

            $previousNote = trim((string) $locked->decision_note);
            $note = 'Congé arrêté par '.$actor.' le '.$stamp
                .' ; fin du congé ramenée au '.$lastDay->format('d/m/Y')
                .($restored > 0 ? ' ('.$restored.' jour(s) réintégré(s)).' : '.');

            $locked->update([
                'end_date' => $lastDay->toDateString(),
                'working_days' => $consumed,
                'reviewed_at' => $now,
                'reviewed_by' => $reviewedBy ?? $locked->reviewed_by,
                'decision_note' => $previousNote === '' ? $note : $previousNote.' '.$note,
            ]);

            self::restoreBalance($employee, $restored);

            return ['result' => self::INTERRUPTED, 'restored_days' => $restored];
        });
    }

    private static function restoreBalance(?Employee $employee, int $days): void
    {
        if ($days < 1 || $employee === null) {
            return;
        }

        $employee->update([
            'token_leave_days' => (int) $employee->token_leave_days + $days,
        ]);
    }
}
