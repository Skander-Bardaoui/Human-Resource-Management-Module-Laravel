<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'work_date' => AttendanceRecord::currentDate()->subDay()->toDateString(),
            'check_in' => '08:20:00',
            'check_out' => '17:35:00',
            'break_start' => '12:30:00',
            'break_end' => '13:30:00',
            'break_minutes' => 60,
            'mode' => 'manual',
            'status' => 'present',
            'notes' => null,
            'anomaly_ignored' => false,
        ];
    }

    public function forDate(Carbon|string $date): static
    {
        return $this->state(fn () => [
            'work_date' => Carbon::parse($date)->toDateString(),
        ]);
    }

    public function late(int $minutes = 20): static
    {
        return $this->state(fn () => [
            'check_in' => self::time(AttendanceRecord::scheduleStartMinutes() + $minutes),
            'check_out' => '17:30:00',
            'break_start' => '12:30:00',
            'break_end' => '13:30:00',
            'break_minutes' => 60,
            'status' => 'late',
        ]);
    }

    public function missingCheckout(): static
    {
        return $this->state(fn () => [
            'check_in' => '08:30:00',
            'check_out' => null,
            'break_start' => null,
            'break_end' => null,
            'break_minutes' => 0,
            'status' => 'present',
        ]);
    }

    public function earlyExit(): static
    {
        return $this->state(fn () => [
            'check_in' => '08:30:00',
            'check_out' => '15:00:00',
            'break_start' => '12:00:00',
            'break_end' => '12:30:00',
            'break_minutes' => 30,
            'status' => 'present',
        ]);
    }

    public function absent(): static
    {
        return $this->state(fn () => [
            'check_in' => null,
            'check_out' => null,
            'break_start' => null,
            'break_end' => null,
            'break_minutes' => 0,
            'mode' => 'none',
            'status' => 'absent',
        ]);
    }

    public function onLeave(): static
    {
        return $this->state(fn () => [
            'check_in' => null,
            'check_out' => null,
            'break_start' => null,
            'break_end' => null,
            'break_minutes' => 0,
            'mode' => 'none',
            'status' => 'leave',
        ]);
    }

    public function ignored(): static
    {
        return $this->state(fn () => [
            'anomaly_ignored' => true,
        ]);
    }

    private static function time(int $minutes): string
    {
        return str_pad((string) intdiv($minutes, 60), 2, '0', STR_PAD_LEFT)
            .':'.str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT)
            .':00';
    }
}
