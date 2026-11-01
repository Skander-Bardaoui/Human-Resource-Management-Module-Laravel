<?php

namespace App\Support;

use App\Models\AdvanceRequest;
use App\Models\Conges;
use App\Models\DocumentRequest;
use App\Models\Employee;
use App\Models\Payslip;
use App\Models\User;

class CollaboratorContext
{
    public static function forUser(?User $user): ?Employee
    {
        if ($user === null || blank($user->email)) {
            return null;
        }

        return Employee::whereRaw('LOWER(email) = ?', [mb_strtolower(trim((string) $user->email))])->first();
    }

    public static function matricule(?Employee $employee, ?User $user): string
    {
        if ($employee !== null) {
            return $employee->matricule();
        }

        return 'MAT-'.str_pad((string) ($user?->id ?? 0), 4, '0', STR_PAD_LEFT);
    }

    public static function notifications(Employee $employee): array
    {
        $items = [];

        $pendingLeave = Conges::forEmployee($employee)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->first();

        if ($pendingLeave) {
            $items[] = [
                'icon' => 'pending_actions',
                'tone' => 'bg-secondary-container text-on-secondary-fixed',
                'title' => 'Congé en attente de validation',
                'subtitle' => $pendingLeave->reference().' • '.$pendingLeave->typeLabel(),
                'url' => route('collaborator.conges'),
            ];
        }

        $currentPeriod = AttendanceClock::now()->format('Y-m');

        $payslip = Payslip::forEmployee($employee)
            ->whereIn('period', [$currentPeriod, AttendanceClock::now()->copy()->subMonthNoOverflow()->format('Y-m')])
            ->orderByDesc('period')
            ->first();

        if ($payslip) {
            $items[] = [
                'icon' => 'receipt_long',
                'tone' => 'bg-primary-fixed text-on-primary-fixed',
                'title' => 'Bulletin de paie disponible',
                'subtitle' => $payslip->periodLabel().' • '.$payslip->netLabel().' TND net',
                'url' => route('collaborator.payslips', ['bulletin' => $payslip->id]),
            ];
        }

        $documentRequest = DocumentRequest::forEmployee($employee)
            ->whereIn('status', ['pending', 'processing', 'ready'])
            ->orderByDesc('created_at')
            ->first();

        if ($documentRequest) {
            $items[] = [
                'icon' => 'description',
                'tone' => 'bg-surface-container text-on-surface-variant',
                'title' => $documentRequest->typeLabel(),
                'subtitle' => $documentRequest->reference().' • '.$documentRequest->statusLabel(),
                'url' => route('collaborator.documents'),
            ];
        }

        $advance = AdvanceRequest::forEmployee($employee)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->first();

        if ($advance) {
            $items[] = [
                'icon' => 'savings',
                'tone' => 'bg-tertiary-fixed text-on-tertiary-fixed',
                'title' => 'Acompte en cours d\'examen',
                'subtitle' => $advance->reference().' • '.number_format((float) $advance->amount, 3, ',', ' ').' TND',
                'url' => route('collaborator.payslips'),
            ];
        }

        return array_slice($items, 0, 4);
    }
}
