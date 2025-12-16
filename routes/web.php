<?php

use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/conges');

Route::get('/conges', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
Route::get('/conges/demande', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
Route::post('/conges', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
Route::patch('/conges/{leaveRequest}/valider', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
Route::patch('/conges/{leaveRequest}/rejeter', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
