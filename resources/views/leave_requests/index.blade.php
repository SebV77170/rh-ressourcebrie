@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h4 mb-0">Demandes de congés</h1>
                    <small class="text-muted">Suivi des validations par le CA</small>
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
                                    <td>
                                        <strong>{{ $leaveRequest->employee_name }}</strong><br>
                                        <span class="text-muted small">{{ $leaveRequest->employee_email }}</span>
                                    </td>
                                    <td>
                                        {{ $leaveRequest->start_date->format('d/m/Y') }}<br>
                                        <span class="text-muted small">au {{ $leaveRequest->end_date->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="text-break" style="max-width: 240px;">
                                        {{ $leaveRequest->reason ?? '—' }}
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                            ][$leaveRequest->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }} text-uppercase">{{ $leaveRequest->status }}</span>
                                        @if ($leaveRequest->decision_notes)
                                            <div class="small text-muted mt-1">{{ $leaveRequest->decision_notes }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($leaveRequest->status === 'pending')
                                            <div class="d-flex justify-content-end gap-2">
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
    <div class="col-lg-4">
        <div class="card">
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
