<?php

namespace App\Http\Controllers\Collaborator;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Support\AttendanceClock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class CollaboratorController extends Controller
{
    protected function employee(Request $request): Employee
    {
        $employee = $request->attributes->get('employee');

        abort_unless($employee instanceof Employee, 403);

        return $employee;
    }

    protected function assertOwned(Model $model, Employee $employee): void
    {
        abort_unless((int) $model->employee_id === (int) $employee->id, 404);
    }

    protected function currentPeriod(): string
    {
        return AttendanceClock::now()->format('Y-m');
    }
}
