<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDirectory;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $reportPeriodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $reportPeriodEnd = $reportPeriodStart->copy()->endOfMonth();

        $leaveRequestsQuery = LeaveRequest::query()
            ->when(
                $user->hasStatus(User::STATUS_EMPLOYEE),
                fn (Builder $query): Builder => $query->where('employee_email', $user->email)
            );

        $leaveRequests = (clone $leaveRequestsQuery)
            ->latest()
            ->get();

        $reportRequests = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', $reportPeriodEnd)
            ->whereDate('end_date', '>=', $reportPeriodStart)
            ->orderBy('start_date')
            ->get()
            ->map(function (LeaveRequest $leaveRequest) use ($reportPeriodStart, $reportPeriodEnd): LeaveRequest {
                $leaveRequest->report_start_date = $leaveRequest->start_date->greaterThan($reportPeriodStart)
                    ? $leaveRequest->start_date->copy()
                    : $reportPeriodStart->copy();
                $leaveRequest->report_end_date = $leaveRequest->end_date->lessThan($reportPeriodEnd)
                    ? $leaveRequest->end_date->copy()
                    : $reportPeriodEnd->copy();

                return $leaveRequest;
            });

        return view('leave_requests.index', [
            'leaveRequests' => $leaveRequests,
            'reportRequests' => $reportRequests,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function create(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('leave_requests.create', [
            'currentUser' => $user,
            'employees' => $user->hasStatus(User::STATUS_ADMIN)
                ? $this->employeesForAdmin()
                : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_name' => ['required', 'string', 'max:255'],
            'employee_email' => ['required', 'email', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string'],
        ]);

        LeaveRequest::create($validated);

        return redirect()
            ->route('leave-requests.index')
            ->with('status', 'La demande de congé a été envoyée et est en attente de validation.');
    }

    public function approve(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $leaveRequest->update([
            'status' => 'approved',
            'decision_notes' => $request->input('decision_notes'),
            'decision_made_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('leave-requests.index')
            ->with('status', 'La demande a été validée.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $leaveRequest->update([
            'status' => 'rejected',
            'decision_notes' => $request->input('decision_notes'),
            'decision_made_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('leave-requests.index')
            ->with('status', 'La demande a été refusée.');
    }

    protected function employeesForAdmin(): Collection
    {
        $authConnection = config('database.auth_connection');

        if ($authConnection !== config('database.default')
            && Schema::connection($authConnection)->hasTable('employes')
            && Schema::connection($authConnection)->hasTable('users')) {
            return EmployeeDirectory::activeDirectoryQuery()
                ->get()
                ->map(fn (EmployeeDirectory $employee) => (object) [
                    'id' => $employee->uuid_user,
                    'name' => trim($employee->prenom.' '.$employee->nom),
                    'email' => $employee->mail,
                ]);
        }

        return User::query()
            ->where('status', User::STATUS_EMPLOYEE)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
