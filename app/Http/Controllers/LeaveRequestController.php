<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $leaveRequests = LeaveRequest::latest()->get();

        $reportRequests = LeaveRequest::where('status', 'approved')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->orderBy('start_date')
            ->get();

        return view('leave_requests.index', [
            'leaveRequests' => $leaveRequests,
            'reportRequests' => $reportRequests,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function create(): View
    {
        return view('leave_requests.create');
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
}
