<?php

namespace Database\Seeders;

use App\Models\Payslip;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayslipSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(PayrollService::class);
        $timezone = (string) config('payroll.timezone', 'Africa/Tunis');
        $current = now($timezone)->format('Y-m');
        $previous = now($timezone)->subMonthNoOverflow()->format('Y-m');

        $service->generate($previous);
        Payslip::query()
            ->forPeriod($previous)
            ->update([
                'status' => 'paid',
                'paid_at' => Carbon::createFromFormat('!Y-m', $previous, $timezone)->endOfMonth(),
            ]);

        $service->generate($current);
        Payslip::query()
            ->forPeriod($current)
            ->orderBy('id')
            ->first()
            ?->update(['status' => 'review']);
    }
}
