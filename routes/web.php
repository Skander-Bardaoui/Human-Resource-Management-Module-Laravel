<?php

use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\PayrollController as AdminPayrollController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CongesController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\ReportsController;







/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/





Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['token.auth', 'verified'])->name('dashboard');

Route::middleware(['token.auth', 'verified'])->group(function () {
    Route::view('/mes-conges', 'collaborator.conges')->name('collaborator.conges');
    Route::view('/mes-fiches-de-paie', 'collaborator.payslips')->name('collaborator.payslips');
    Route::view('/mon-pointage', 'collaborator.attendance')->name('collaborator.attendance');
    Route::view('/mes-documents', 'collaborator.documents')->name('collaborator.documents');
});

Route::middleware(['token.auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    Route::get('/employees', [AdminEmployeeController::class, 'index'])->name('employees');
    Route::post('/employees', [AdminEmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/export', [AdminEmployeeController::class, 'export'])->name('employees.export');
    Route::delete('/employees/bulk', [AdminEmployeeController::class, 'bulkDestroy'])->name('employees.bulkDestroy');
    Route::get('/employees/{employee}/cin', [AdminEmployeeController::class, 'downloadCin'])->name('employees.cin');
    Route::put('/employees/{employee}', [AdminEmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [AdminEmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('/leaves', [AdminLeaveController::class, 'index'])->name('leaves');
    Route::post('/leaves', [AdminLeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/export', [AdminLeaveController::class, 'export'])->name('leaves.export');
    Route::get('/leaves/{leave}/attachment', [AdminLeaveController::class, 'downloadAttachment'])->name('leaves.attachment');
    Route::patch('/leaves/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leaves.approve');
    Route::patch('/leaves/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leaves.reject');
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/punch', [AdminAttendanceController::class, 'punch'])->name('attendance.punch');
    Route::get('/attendance/export', [AdminAttendanceController::class, 'export'])->name('attendance.export');
    Route::post('/attendance/anomalies/resolve-all', [AdminAttendanceController::class, 'resolveAll'])->name('attendance.anomalies.resolveAll');
    Route::post('/attendance/anomalies/{record}/resolve', [AdminAttendanceController::class, 'resolveAnomaly'])->name('attendance.anomalies.resolve');
    Route::post('/attendance/anomalies/{record}/justify', [AdminAttendanceController::class, 'justifyAnomaly'])->name('attendance.anomalies.justify');
    Route::post('/attendance/anomalies/{record}/ignore', [AdminAttendanceController::class, 'ignoreAnomaly'])->name('attendance.anomalies.ignore');

    Route::get('/departments', [AdminDepartmentController::class, 'index'])->name('departments');
    Route::post('/departments', [AdminDepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/export', [AdminDepartmentController::class, 'export'])->name('departments.export');
    Route::put('/departments/{department}', [AdminDepartmentController::class, 'update'])->name('departments.update');

    Route::get('/payroll', [AdminPayrollController::class, 'index'])->name('payroll');
    Route::post('/payroll/close', [AdminPayrollController::class, 'close'])->name('payroll.close');
    Route::get('/payroll/export/journal', [AdminPayrollController::class, 'exportJournal'])->name('payroll.export.journal');
    Route::get('/payroll/export/cnss', [AdminPayrollController::class, 'exportCnss'])->name('payroll.export.cnss');
    Route::get('/payroll/export/bank', [AdminPayrollController::class, 'exportBank'])->name('payroll.export.bank');
    Route::get('/payroll/{payslip}/payslip', [AdminPayrollController::class, 'downloadPayslip'])->name('payroll.payslip');
    Route::get('/recruitment', [RecruitmentController::class, 'index'])->name('recruitment');
    Route::get('/offers', [RecruitmentController::class, 'offers'])->name('offers');
    Route::post('/recruitment/candidates', [RecruitmentController::class, 'storeCandidate'])->name('recruitment.candidates.store');
    Route::put('/recruitment/candidates/{candidate}', [RecruitmentController::class, 'updateCandidate'])->name('recruitment.candidates.update');
    Route::delete('/recruitment/candidates/{candidate}', [RecruitmentController::class, 'destroyCandidate'])->name('recruitment.candidates.destroy');
    Route::patch('/recruitment/candidates/{candidate}/stage', [RecruitmentController::class, 'moveStage'])->name('recruitment.candidates.stage');
    Route::post('/recruitment/candidates/{candidate}/interviews', [RecruitmentController::class, 'storeInterview'])->name('recruitment.candidates.interviews.store');
    Route::delete('/recruitment/interviews/{interview}', [RecruitmentController::class, 'destroyInterview'])->name('recruitment.interviews.destroy');
    Route::post('/recruitment/offers', [RecruitmentController::class, 'storeOffer'])->name('recruitment.offers.store');
    Route::put('/recruitment/offers/{jobOffer}', [RecruitmentController::class, 'updateOffer'])->name('recruitment.offers.update');
    Route::delete('/recruitment/offers/{jobOffer}', [RecruitmentController::class, 'destroyOffer'])->name('recruitment.offers.destroy');

    Route::get('/documents', [DocumentsController::class, 'index'])->name('documents');
    Route::post('/documents', [DocumentsController::class, 'store'])->name('documents.store');
    Route::post('/documents/zip', [DocumentsController::class, 'zipSelection'])->name('documents.zip');
    Route::get('/documents/{document}/download', [DocumentsController::class, 'download'])->name('documents.download');
    Route::put('/documents/{document}', [DocumentsController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentsController::class, 'destroy'])->name('documents.destroy');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/export/{dataset}', [ReportsController::class, 'exportCsv'])->name('reports.export');
    Route::post('/reports/schedules', [ReportsController::class, 'storeSchedule'])->name('reports.schedules.store');
    Route::put('/reports/schedules/{schedule}', [ReportsController::class, 'updateSchedule'])->name('reports.schedules.update');
    Route::delete('/reports/schedules/{schedule}', [ReportsController::class, 'destroySchedule'])->name('reports.schedules.destroy');
    Route::post('/reports/schedules/{schedule}/send', [ReportsController::class, 'sendNow'])->name('reports.schedules.send');
    Route::view('/settings', 'admin.settings')->name('settings');
});

Route::middleware('token.auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('/employees', EmployeeController::class);
Route::resource('/conges', CongesController::class);

require __DIR__.'/auth.php';
