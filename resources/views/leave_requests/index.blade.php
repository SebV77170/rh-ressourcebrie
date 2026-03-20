@extends('layouts.app')

@section('title', 'Demandes de congés')

@section('content')
@php
    $canManageRequests = auth()->user()->hasStatus(\App\Models\User::STATUS_ADMIN);
@endphp

@push('styles')
<style>
    .leave-dashboard {
        color: #1e293b;
    }

    .leave-dashboard .main-card,
    .leave-dashboard .side-card {
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .leave-dashboard .main-card .card-header h1,
    .leave-dashboard .side-card .card-header h2 {
        font-weight: 700;
    }

    .leave-dashboard .main-card .card-header small {
        color: rgba(255, 255, 255, 0.85);
    }

    .leave-dashboard .btn-primary {
        padding: 0.65rem 1rem;
    }

    .leave-dashboard .btn-outline-primary {
        border-radius: 0.75rem;
        font-weight: 600;
        border-color: #2563eb;
        color: #2563eb;
    }

    .leave-dashboard .btn-outline-primary:hover {
        background-color: #2563eb;
        color: #fff;
    }

    .leave-dashboard .btn-success,
    .leave-dashboard .btn-outline-danger {
        border-radius: 0.65rem;
        font-weight: 600;
    }

    .leave-dashboard .btn-success {
        background: #16a34a;
        border-color: #16a34a;
    }

    .leave-dashboard .btn-success:hover {
        background: #15803d;
        border-color: #15803d;
    }

    .leave-dashboard .btn-outline-danger:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }

    .leave-dashboard .card-body {
        background: #f8fafc;
    }

    .leave-dashboard .table {
        margin-bottom: 0;
        background: #fff;
    }

    .leave-dashboard .table thead th {
        font-size: 0.78rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        background: #eaf2ff;
        padding-top: 1rem;
        padding-bottom: 1rem;
        font-weight: 700;
    }

    .leave-dashboard .table tbody td {
        vertical-align: top;
        border-color: #edf2f7;
        padding: 1rem;
        background: #fff;
    }

    .leave-dashboard .table tbody tr:hover td {
        background: #f8fbff;
        transition: background 0.2s ease;
    }

    .leave-dashboard .status-badge {
        letter-spacing: 0.03em;
        font-weight: 700;
        padding: 0.5rem 0.7rem;
        border-radius: 999px;
        font-size: 0.78rem;
    }

    .leave-dashboard .bg-warning.status-badge,
    .leave-dashboard .badge.bg-warning {
        background: #fef3c7 !important;
        color: #92400e !important;
    }

    .leave-dashboard .bg-success.status-badge,
    .leave-dashboard .badge.bg-success {
        background: #dcfce7 !important;
        color: #166534 !important;
    }

    .leave-dashboard .bg-danger.status-badge,
    .leave-dashboard .badge.bg-danger {
        background: #fee2e2 !important;
        color: #991b1b !important;
    }

    .leave-dashboard .bg-secondary.status-badge,
    .leave-dashboard .badge.bg-secondary {
        background: #e2e8f0 !important;
        color: #334155 !important;
    }

    .leave-dashboard .employee-cell {
        min-width: 230px;
    }

    .leave-dashboard .period-cell {
        min-width: 170px;
    }

    .leave-dashboard .reason-cell {
        max-width: 280px;
    }

    .leave-dashboard .actions-cell {
        min-width: 190px;
    }

    .leave-dashboard .table-responsive {
        border-radius: 0 0 1rem 1rem;
        overflow: hidden;
    }

    .leave-dashboard .list-group-item {
        border: 1px solid #e2e8f0;
        border-radius: 0.9rem !important;
        margin-bottom: 0.75rem;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }

    .leave-dashboard .alert-info {
        border: 0;
        border-radius: 0.9rem;
        background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
        color: #1d4ed8;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.08);
    }

    .leave-dashboard .empty-state {
        padding: 2rem 1rem;
        color: #64748b;
    }

    .leave-dashboard .muted-box {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 1rem;
        padding: 1rem;
        color: #64748b;
    }

    @media (max-width: 767.98px) {
        .leave-dashboard .table-responsive {
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .leave-dashboard .table thead {
            display: none;
        }

        .leave-dashboard .table,
        .leave-dashboard .table tbody,
        .leave-dashboard .table tr,
        .leave-dashboard .table td {
            display: block;
            width: 100%;
        }

        .leave-dashboard .table tr {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            background: #fff;
        }

        .leave-dashboard .table tr:last-child {
            border-bottom: 0;
        }

        .leave-dashboard .table td {
            border: 0;
            padding: 0.5rem 0;
        }

        .leave-dashboard .table td::before {
            content: attr(data-label);
            display: block;
            font-size: 0.76rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
            font-weight: 700;
        }

        .leave-dashboard .actions-cell .d-flex {
            justify-content: flex-start !important;
        }
    }
</style>
@endpush

<div class="row g-4 mb-4 leave-dashboard">
    @if (auth()->user()->hasStatus(\App\Models\User::STATUS_ADMIN, \App\Models\User::STATUS_EMPLOYEE))

    <div class="col-xl-8">
        <div class="card h-100 main-card view-card">
            <div class="card-header view-card-header-gradient d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h1 class="h4 mb-1">Synthèse des demandes de congés</h1>
                    <small>{{ $canManageRequests ? 'Suivi clair des validations par le CA' : 'Suivi de vos demandes de congés' }}</small>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Collaborateur</th>
                                <th>Période</th>
                                <th>Motif</th>
                                <th>Statut</th>
                                <th class="text-end">Actions CA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leaveRequests as $leaveRequest)
                                <tr>
                                    <td class="employee-cell" data-label="Collaborateur">
                                        <strong>{{ $leaveRequest->employee_name }}</strong><br>
                                        <span class="text-muted small">{{ $leaveRequest->employee_email }}</span>
                                    </td>

                                    <td class="period-cell" data-label="Période">
                                        <strong>{{ $leaveRequest->start_date->format('d/m/Y') }}</strong><br>
                                        <span class="text-muted small">au {{ $leaveRequest->end_date->format('d/m/Y') }}</span>
                                    </td>

                                    <td class="reason-cell text-break" data-label="Motif">
                                        {{ $leaveRequest->reason ?? '—' }}
                                    </td>

                                    <td data-label="Statut">
                                        @php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                            ][$leaveRequest->status] ?? 'secondary';

                                            $statusLabel = [
                                                'pending' => 'En attente',
                                                'approved' => 'Validée',
                                                'rejected' => 'Refusée',
                                            ][$leaveRequest->status] ?? $leaveRequest->status;
                                        @endphp

                                        <span class="badge status-badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>

                                        @if ($leaveRequest->decision_notes)
                                            <div class="small text-muted mt-2">{{ $leaveRequest->decision_notes }}</div>
                                        @endif
                                    </td>

                                    <td class="actions-cell text-end" data-label="Actions CA">
                                        @if ($canManageRequests && $leaveRequest->status === 'pending')
                                            <div class="d-flex justify-content-end flex-wrap gap-2">
                                                <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="decision_notes" value="Validé par le CA">
                                                    <button class="btn btn-sm btn-success">Valider</button>
                                                </form>

                                                <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="decision_notes" value="Refusé par le CA">
                                                    <button class="btn btn-sm btn-outline-danger">Refuser</button>
                                                </form>
                                            </div>
                                        @else
                                            <small class="text-muted">
                                                @if ($canManageRequests)
                                                    Décision le {{ optional($leaveRequest->decision_made_at)->format('d/m/Y H:i') ?? '—' }}
                                                @else
                                                    Consultation uniquement
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center empty-state">
                                        Aucune demande enregistrée pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if (auth()->user()->hasStatus(\App\Models\User::STATUS_PAYROLL_MANAGER))

    <div class="col-xl-4">
        <div class="card side-card h-100 view-card">
            <div class="card-header view-card-header-gradient">
                <h2 class="h5 mb-0">Rapport mensuel pour la paie</h2>
            </div>

            <div class="card-body p-4">
                <form method="GET" class="row g-3 align-items-end mb-4">
                    <div class="col-6">
                        <label for="month" class="form-label view-label">Mois</label>
                        <select id="month" name="month" class="form-select view-select">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" @selected($m === $month)>
                                    {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6">
                        <label for="year" class="form-label view-label">Année</label>
                        <input type="number" id="year" name="year" class="form-control view-input" value="{{ $year }}" min="2020" max="2100">
                    </div>

                    <div class="col-12 d-grid">
                        <button class="btn btn-outline-primary" type="submit">Mettre à jour</button>
                    </div>
                </form>

                @if ($reportRequests->isEmpty())
                    <div class="muted-box">
                        Aucune absence approuvée pour cette période.
                    </div>
                @else
                    <div class="list-group mb-3">
                        @foreach ($reportRequests as $request)
                            <div class="list-group-item">
                                <div class="fw-bold text-primary">{{ $request->employee_name }}</div>
                                <div class="small text-muted mb-1">
                                    {{ $request->report_start_date->format('d/m') }} au {{ $request->report_end_date->format('d/m/Y') }}
                                </div>
                                @if ($request->reason)
                                    <div class="small">{{ $request->reason }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-info mb-0">
                        <strong>Total validé :</strong> {{ $reportRequests->count() }} demande(s)
                    </div>
                @endif
            </div>
        </div>
    </div>

    @endif
</div>
@endsection
