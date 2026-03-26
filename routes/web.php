<?php

use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollManagerController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/conges');

Route::middleware('guest')->group(function (): void {
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store'])->name('login.store');

});

Route::middleware('auth')->group(function (): void {
    Route::post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/conges', [LeaveRequestController::class, 'index'])
        ->middleware('status:'.User::STATUS_ADMIN.','.User::STATUS_EMPLOYEE.','.User::STATUS_PAYROLL_MANAGER)
        ->name('leave-requests.index');

    Route::get('/conges/demande', [LeaveRequestController::class, 'create'])
        ->middleware('status:'.User::STATUS_ADMIN.','.User::STATUS_EMPLOYEE)
        ->name('leave-requests.create');

    Route::post('/conges', [LeaveRequestController::class, 'store'])
        ->middleware('status:'.User::STATUS_ADMIN.','.User::STATUS_EMPLOYEE)
        ->name('leave-requests.store');

    Route::patch('/conges/{leaveRequest}/valider', [LeaveRequestController::class, 'approve'])
        ->middleware('status:'.User::STATUS_ADMIN)
        ->name('leave-requests.approve');

    Route::patch('/conges/{leaveRequest}/rejeter', [LeaveRequestController::class, 'reject'])
        ->middleware('status:'.User::STATUS_ADMIN)
        ->name('leave-requests.reject');

    Route::delete('/conges/{leaveRequest}/annuler', [LeaveRequestController::class, 'cancel'])
        ->middleware('status:'.User::STATUS_EMPLOYEE)
        ->name('leave-requests.cancel');

    Route::get('/gestionnaires-paie', [PayrollManagerController::class, 'index'])
        ->middleware('status:'.User::STATUS_ADMIN)
        ->name('payroll-managers.index');

    Route::post('/gestionnaires-paie', [PayrollManagerController::class, 'store'])
        ->middleware('status:'.User::STATUS_ADMIN)
        ->name('payroll-managers.store');

    Route::delete('/gestionnaires-paie/{payrollManager}', [PayrollManagerController::class, 'destroy'])
        ->middleware('status:'.User::STATUS_ADMIN)
        ->name('payroll-managers.destroy');
});
