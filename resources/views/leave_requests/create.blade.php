@extends('layouts.app')

@section('title', 'Créer une demande de congé')

@php
    use App\Models\User;
    $selectedEmployee = null;

    if ($currentUser->hasStatus(User::STATUS_ADMIN)) {
        $selectedEmployee = $employees->firstWhere('id', (int) old('selected_employee_id')) ?? $employees->first();
    }

    $nameSource = old('employee_name', $selectedEmployee?->name ?? $currentUser->name);
    $nameParts = preg_split('/\s+/', trim((string) $nameSource), 2, PREG_SPLIT_NO_EMPTY) ?: [];
    $firstName = old('employee_first_name', $nameParts[0] ?? '');
    $lastName = old('employee_last_name', $nameParts[1] ?? '');
    $email = old('employee_email', $selectedEmployee?->email ?? $currentUser->email);
    $selectedEmployeeId = old('selected_employee_id', $selectedEmployee?->id);
    $isAdmin = $currentUser->hasStatus(User::STATUS_ADMIN);
@endphp

@section('content')
@push('styles')
<style>
    .leave-create-page .form-card {
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .leave-create-page .card-body {
        padding: 2rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .leave-create-page .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.55rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .leave-create-page .form-control.form-control-lg,
    .leave-create-page .form-select.form-select-lg {
        padding: 0.95rem 1rem;
        font-size: 1rem;
    }

    .leave-create-page textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .leave-create-page .field-hint {
        font-size: 0.84rem;
        color: #64748b;
        margin-top: 0.35rem;
    }

    .leave-create-page .form-block {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.03);
    }

    .leave-create-page .btn-outline-secondary {
        border-radius: 0.85rem;
        font-weight: 600;
        padding: 0.8rem 1.2rem;
        border-color: #cbd5e1;
        color: #475569;
        background: #fff;
    }

    .leave-create-page .btn-outline-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
    }

    .leave-create-page .form-footer {
        margin-top: 1.75rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e2e8f0;
    }

    .leave-create-page .title-icon {
        width: 52px;
        height: 52px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.18);
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    @media (max-width: 767.98px) {
        .leave-create-page .card-body {
            padding: 1.25rem;
        }

        .leave-create-page .form-block {
            padding: 1rem;
        }

        .leave-create-page .form-footer {
            display: flex;
            flex-direction: column-reverse;
            gap: 0.75rem;
            align-items: stretch !important;
        }

        .leave-create-page .form-footer .btn {
            width: 100%;
        }
    }
</style>
@endpush

<div class="container py-4 leave-create-page">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card form-card view-card">
                <div class="card-header view-card-header-gradient">
                    <div class="d-flex align-items-center">
                        <div class="title-icon me-3">🗓️</div>
                        <div>
                            <h1 class="h4 mb-1">Demande de congé</h1>
                            <small>Merci de compléter les informations ci-dessous pour transmission au CA</small>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('leave-requests.store') }}">
                        @csrf

                        <div class="form-block mb-4">
                            <div class="section-title">👤 Informations salarié</div>

                            @if ($isAdmin)
                                <div class="mb-3">
                                    <label for="selected_employee_id" class="form-label view-label">Employé</label>
                                    <select
                                        name="selected_employee_id"
                                        id="selected_employee_id"
                                        class="form-select form-select-lg view-select"
                                        data-employee-selector
                                    >
                                        @foreach ($employees as $employee)
                                            @php
                                                $employeeParts = preg_split('/\s+/', trim($employee->name), 2, PREG_SPLIT_NO_EMPTY) ?: [];
                                            @endphp
                                            <option
                                                value="{{ $employee->id }}"
                                                data-first-name="{{ $employeeParts[0] ?? '' }}"
                                                data-last-name="{{ $employeeParts[1] ?? '' }}"
                                                data-email="{{ $employee->email }}"
                                                data-full-name="{{ $employee->name }}"
                                                @selected((string) $selectedEmployeeId === (string) $employee->id)
                                            >
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="field-hint">Choisissez l’employé concerné pour mettre à jour automatiquement les informations ci-dessous.</div>
                                </div>
                            @endif

                            <input type="hidden" name="employee_name" id="employee_name" value="{{ $nameSource }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="employee_first_name" class="form-label view-label">Prénom</label>
                                    <input
                                        type="text"
                                        name="employee_first_name"
                                        id="employee_first_name"
                                        class="form-control form-control-lg view-input"
                                        value="{{ $firstName }}"
                                        readonly
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label for="employee_last_name" class="form-label view-label">Nom</label>
                                    <input
                                        type="text"
                                        name="employee_last_name"
                                        id="employee_last_name"
                                        class="form-control form-control-lg view-input"
                                        value="{{ $lastName }}"
                                        readonly
                                        required
                                    >
                                </div>
                            </div>

                            <div class="mb-0 mt-3">
                                <label for="employee_email" class="form-label view-label">Adresse email</label>
                                <input
                                    type="email"
                                    name="employee_email"
                                    id="employee_email"
                                    class="form-control view-input"
                                    value="{{ $email }}"
                                    readonly
                                    required
                                >
                                <div class="field-hint">
                                    @if ($isAdmin)
                                        Les champs se remplissent automatiquement selon l’employé sélectionné.
                                    @else
                                        Vos informations sont déjà renseignées : il ne vous reste qu’à choisir les dates et préciser le motif si besoin.
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-block mb-4">
                            <div class="section-title">📅 Période de congé</div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label view-label">Date de début</label>
                                    <input
                                        type="date"
                                        name="start_date"
                                        id="start_date"
                                        class="form-control view-input"
                                        value="{{ old('start_date') }}"
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label for="end_date" class="form-label view-label">Date de fin</label>
                                    <input
                                        type="date"
                                        name="end_date"
                                        id="end_date"
                                        class="form-control view-input"
                                        value="{{ old('end_date') }}"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="field-hint mt-3">
                                Vérifiez que les dates correspondent bien à la période complète d’absence souhaitée.
                            </div>
                        </div>

                        <div class="form-block">
                            <div class="section-title">📝 Motif facultatif</div>

                            <label for="reason" class="form-label view-label">Précision éventuelle</label>
                            <textarea
                                name="reason"
                                id="reason"
                                class="form-control view-input"
                                rows="4"
                                placeholder="Ex : congés payés, événement familial, repos..."
                            >{{ old('reason') }}</textarea>

                            <div class="field-hint">
                                Ce champ est optionnel, mais peut aider à contextualiser la demande.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center form-footer">
                            <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>

                            <button class="btn btn-primary btn-view-primary" type="submit">
                                Envoyer la demande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($isAdmin)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const selector = document.querySelector('[data-employee-selector]');
                const firstNameInput = document.getElementById('employee_first_name');
                const lastNameInput = document.getElementById('employee_last_name');
                const emailInput = document.getElementById('employee_email');
                const fullNameInput = document.getElementById('employee_name');

                if (! selector || ! firstNameInput || ! lastNameInput || ! emailInput || ! fullNameInput) {
                    return;
                }

                const syncEmployeeFields = () => {
                    const selectedOption = selector.options[selector.selectedIndex];

                    if (! selectedOption) {
                        return;
                    }

                    firstNameInput.value = selectedOption.dataset.firstName || '';
                    lastNameInput.value = selectedOption.dataset.lastName || '';
                    emailInput.value = selectedOption.dataset.email || '';
                    fullNameInput.value = selectedOption.dataset.fullName || '';
                };

                selector.addEventListener('change', syncEmployeeFields);
                syncEmployeeFields();
            });
        </script>
    @endpush
@endif
@endsection
