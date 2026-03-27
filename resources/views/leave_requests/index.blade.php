@extends('layouts.app')

@section('title', 'Demandes de congés')
@section('page_title', 'Tableau de bord des demandes de congés')
@section('page_intro', "Visualisez les demandes, leurs statuts et les actions disponibles.")

@section('content')
@php
    $canManageRequests = auth()->user()->hasStatus(\App\Models\User::STATUS_ADMIN);
    $canCreateRequest = auth()->user()->hasStatus(\App\Models\User::STATUS_ADMIN, \App\Models\User::STATUS_EMPLOYEE);
    $canViewPayrollReport = auth()->user()->hasStatus(\App\Models\User::STATUS_PAYROLL_MANAGER);
@endphp

<div class="row g-4">
    @if ($canCreateRequest)
        <div class="col-12 {{ $canViewPayrollReport ? 'col-xl-8' : 'col-xl-8' }}">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center flex-wrap gap-3 py-3">
                    <div>
                        <h2 class="h4 mb-1">Synthèse des demandes</h2>
                        <p class="mb-0 opacity-75">{{ $canManageRequests ? 'Suivi des validations et arbitrages du CA.' : 'Suivi de vos demandes de congés en temps réel.' }}</p>
                    </div>
                    <a href="{{ route('leave-requests.create') }}" class="btn btn-light text-info-emphasis">Nouvelle demande</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Collaborateur</th>
                                    <th>Période</th>
                                    <th>Motif</th>
                                    <th>Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leaveRequests as $leaveRequest)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $leaveRequest->employee_name }}</div>
                                            <div class="text-secondary small">{{ $leaveRequest->employee_email }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $leaveRequest->start_date->format('d/m/Y') }}</div>
                                            <div class="text-secondary small">au {{ $leaveRequest->end_date->format('d/m/Y') }}</div>
                                        </td>
                                        <td class="text-secondary">{{ $leaveRequest->reason ?? '—' }}</td>
                                        <td>
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
                                                ][$leaveRequest->status] ?? ucfirst($leaveRequest->status);
                                            @endphp
                                            @php
                                                $rejectionMessage = $leaveRequest->rejectionMessage?->message ?? $leaveRequest->decision_notes;
                                            @endphp
                                            <span class="badge rounded-pill text-bg-{{ $statusClass }}">{{ $statusLabel }}</span>

                                            @if ($rejectionMessage)
                                                <div class="text-secondary small mt-2">{{ $rejectionMessage }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($canManageRequests && $leaveRequest->status === 'pending')
                                                <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                                    <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="decision_notes" value="Validé par le CA">
                                                        <button type="submit" class="btn btn-success btn-sm">Valider</button>
                                                    </form>

                                                    <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}" class="d-flex gap-2 align-items-start">
                                                        @csrf
                                                        @method('PATCH')
                                                        <textarea name="decision_notes" rows="2" class="form-control form-control-sm" placeholder="Motif du refus" required></textarea>
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">Refuser</button>
                                                    </form>
                                                </div>
                                            @elseif (auth()->user()->hasStatus(\App\Models\User::STATUS_EMPLOYEE) && $leaveRequest->status === 'pending')
                                                <form method="POST" action="{{ route('leave-requests.cancel', $leaveRequest) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Annuler ma demande</button>
                                                </form>
                                            @else
                                                @if ($canManageRequests && $leaveRequest->status === 'rejected')
                                                    <form method="POST" action="{{ route('leave-requests.update-rejection-message', $leaveRequest) }}" class="d-grid gap-2 mb-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <textarea name="decision_notes" rows="2" class="form-control form-control-sm" required>{{ old('decision_notes', $rejectionMessage) }}</textarea>
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm">Mettre à jour le motif</button>
                                                    </form>
                                                @endif
                                                <span class="text-secondary small">
                                                    @if ($canManageRequests)
                                                        Décision le {{ optional($leaveRequest->decision_made_at)->format('d/m/Y H:i') ?? '—' }}
                                                    @else
                                                        Consultation uniquement
                                                    @endif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-secondary">
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

    @if ($canViewPayrollReport)
        <div class="col-12 {{ $canCreateRequest ? 'col-xl-4' : 'col-xl-5' }}">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white py-3">
                    <h2 class="h5 mb-1">Rapport mensuel pour la paie</h2>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end mb-4">
                        <div class="col-6">
                            <label for="month" class="form-label fw-semibold">Mois</label>
                            <select id="month" name="month" class="form-select">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected($m === $month)>{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6">
                            <label for="year" class="form-label fw-semibold">Année</label>
                            <input type="number" id="year" name="year" class="form-control" value="{{ $year }}" min="2020" max="2100">
                        </div>

                        <div class="col-12 d-grid">
                            <button class="btn btn-outline-success" type="submit">Mettre à jour</button>
                        </div>
                    </form>

                    @if ($reportRequests->isEmpty())
                        <div class="alert alert-light border mb-0">Aucune absence approuvée pour cette période.</div>
                    @else
                        <div class="list-group mb-3">
                            @foreach ($reportRequests as $request)
                                <div class="list-group-item py-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <div class="fw-semibold text-success-emphasis">{{ $request->employee_name }}</div>
                                            <div class="small text-secondary">
                                                {{ $request->report_start_date->format('d/m') }} au {{ $request->report_end_date->format('d/m/Y') }}
                                            </div>
                                            @if ($request->reason)
                                                <div class="small mt-2">{{ $request->reason }}</div>
                                            @endif
                                        </div>
                                        <span class="badge text-bg-success rounded-pill">Validée</span>
                                    </div>
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

    @if (! $canViewPayrollReport)
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h2 class="h5 mb-1">Récapitulatif</h2>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Total des demandes</span>
                                <span class="badge text-bg-info rounded-pill">{{ $leaveRequests->count() }}</span>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>En attente</span>
                                <span class="badge text-bg-warning rounded-pill">{{ $leaveRequests->where('status', 'pending')->count() }}</span>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Validées</span>
                                <span class="badge text-bg-success rounded-pill">{{ $leaveRequests->where('status', 'approved')->count() }}</span>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Rejetées</span>
                                <span class="badge text-bg-danger rounded-pill">{{ $leaveRequests->where('status', 'rejected')->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    @if ($canCreateRequest)
                        <a href="{{ route('leave-requests.create') }}" class="btn btn-success w-100">Créer une demande</a>
                    @else
                        <div class="alert alert-info mb-0">Vous pouvez consulter l’état des dossiers sans créer de nouvelle demande.</div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
