@extends('layouts.app')

@section('content')
<style>
    .leave-dashboard .main-card,
    .leave-dashboard .side-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1.5rem rgba(15, 23, 42, 0.08);
    }

    .leave-dashboard .card-header {
        border-bottom-color: #edf1f7;
    }

    .leave-dashboard .table thead th {
        font-size: 0.78rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 0;
        background: #f8fafc;
    }

    .leave-dashboard .table tbody td {
        vertical-align: top;
        border-color: #edf1f7;
    }

    .leave-dashboard .table tbody tr:hover {
        background: #f8fbff;
    }

    .leave-dashboard .status-badge {
        letter-spacing: 0.03em;
        font-weight: 600;
        padding: 0.45rem 0.55rem;
    }

    .leave-dashboard .employee-cell {
        min-width: 230px;
    }

    .leave-dashboard .period-cell {
        min-width: 160px;
    }

    .leave-dashboard .reason-cell {
        max-width: 280px;
    }

    .leave-dashboard .actions-cell {
        min-width: 180px;
    }

    @media (max-width: 767.98px) {
        .leave-dashboard .table-responsive {
            border-radius: 0.75rem;
            border: 1px solid #edf1f7;
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
            padding: 0.9rem;
            border-bottom: 1px solid #edf1f7;
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
            letter-spacing: 0.03em;
            margin-bottom: 0.2rem;
            font-weight: 600;
        }

        .leave-dashboard .actions-cell .d-flex {
            justify-content: flex-start !important;
        }
    }
</style>

<div class="row g-4 mb-4 leave-dashboard">
    <div class="col-xl-8">
        <div class="card h-100 main-card">
            <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h1 class="h4 mb-0">Demandes de congés</h1>
                    <small class="text-muted">Suivi clair des validations par le CA</small>
                </div>
                <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">Nouvelle demande</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
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
                                        {{ $leaveRequest->start_date->format('d/m/Y') }}<br>
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
                                            <div class="small text-muted mt-1">{{ $leaveRequest->decision_notes }}</div>
                                        @endif
                                    </td>
                                    <td class="actions-cell text-end" data-label="Actions CA">
                                        @if ($leaveRequest->status === 'pending')
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
                                            <small class="text-muted">Décision le {{ optional($leaveRequest->decision_made_at)->format('d/m/Y H:i') ?? '—' }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Aucune demande enregistrée pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card side-card">
            <div class="card-header bg-white">
                <h2 class="h5 mb-0">Rapport mensuel pour la paie</h2>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end mb-3">
                    <div class="col-6">
                        <label for="month" class="form-label">Mois</label>
                        <select id="month" name="month" class="form-select">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" @selected($m === $month)>{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="year" class="form-label">Année</label>
                        <input type="number" id="year" name="year" class="form-control" value="{{ $year }}" min="2020" max="2100">
                    </div>
                    <div class="col-12 d-grid">
                        <button class="btn btn-outline-primary" type="submit">Mettre à jour</button>
                    </div>
                </form>
                @if ($reportRequests->isEmpty())
                    <p class="text-muted mb-0">Aucune absence approuvée pour cette période.</p>
                @else
                    <div class="list-group mb-3">
                        @foreach ($reportRequests as $request)
                            <div class="list-group-item">
                                <div class="fw-bold">{{ $request->employee_name }}</div>
                                <div class="small text-muted">{{ $request->start_date->format('d/m') }} au {{ $request->end_date->format('d/m') }}</div>
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
</div>
@endsection
