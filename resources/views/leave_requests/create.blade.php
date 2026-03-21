@extends('layouts.app')

@section('title', 'Créer une demande de congé')
@section('page_title', 'Nouvelle demande de congé')
@section('page_intro', "Saisissez une demande dans un formulaire clair, structuré et entièrement construit avec les composants Bootstrap.")

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
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white py-3">
                <h2 class="h4 mb-1">Demande de congé</h2>
                <p class="mb-0 opacity-75">Complétez les informations ci-dessous pour transmettre la demande au CA.</p>
            </div>
            <div class="card-body p-4 p-lg-5">
                <form method="POST" action="{{ route('leave-requests.store') }}" class="row g-4">
                    @csrf

                    <div class="col-12">
                        <div class="card bg-body-tertiary border-0">
                            <div class="card-body">
                                <h3 class="h5 text-info-emphasis mb-3">Informations salarié</h3>

                                @if ($isAdmin)
                                    <div class="mb-3">
                                        <label for="selected_employee_id" class="form-label fw-semibold">Employé</label>
                                        <select name="selected_employee_id" id="selected_employee_id" class="form-select form-select-lg" data-employee-selector>
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
                                        <div class="form-text">Choisissez l’employé concerné pour remplir automatiquement les champs.</div>
                                    </div>
                                @endif

                                <input type="hidden" name="employee_name" id="employee_name" value="{{ $nameSource }}">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="employee_first_name" class="form-label fw-semibold">Prénom</label>
                                        <input type="text" name="employee_first_name" id="employee_first_name" class="form-control form-control-lg" value="{{ $firstName }}" readonly required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="employee_last_name" class="form-label fw-semibold">Nom</label>
                                        <input type="text" name="employee_last_name" id="employee_last_name" class="form-control form-control-lg" value="{{ $lastName }}" readonly required>
                                    </div>

                                    <div class="col-12">
                                        <label for="employee_email" class="form-label fw-semibold">Adresse email</label>
                                        <input type="email" name="employee_email" id="employee_email" class="form-control form-control-lg" value="{{ $email }}" readonly required>
                                        <div class="form-text">
                                            @if ($isAdmin)
                                                Les informations se mettent à jour selon l’employé sélectionné.
                                            @else
                                                Vos informations sont déjà renseignées : il ne reste qu’à choisir la période.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card bg-body-tertiary border-0">
                            <div class="card-body">
                                <h3 class="h5 text-success-emphasis mb-3">Période souhaitée</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="start_date" class="form-label fw-semibold">Date de début</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control form-control-lg" value="{{ old('start_date') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_date" class="form-label fw-semibold">Date de fin</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control form-control-lg" value="{{ old('end_date') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-text">Vérifiez que les dates couvrent bien toute la période d’absence souhaitée.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card bg-body-tertiary border-0">
                            <div class="card-body">
                                <h3 class="h5 text-info-emphasis mb-3">Motif ou précision</h3>
                                <label for="reason" class="form-label fw-semibold">Commentaire</label>
                                <textarea name="reason" id="reason" rows="5" class="form-control" placeholder="Ex. congé annuel, rendez-vous personnel, événement familial...">{{ old('reason') }}</textarea>
                                <div class="form-text">Ce champ est optionnel, mais il peut aider à accélérer le traitement.</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-secondary">Retour au tableau de bord</a>
                        <button type="submit" class="btn btn-success btn-lg">Envoyer la demande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selector = document.querySelector('[data-employee-selector]');

        if (!selector) {
            return;
        }

        const firstNameInput = document.getElementById('employee_first_name');
        const lastNameInput = document.getElementById('employee_last_name');
        const emailInput = document.getElementById('employee_email');
        const hiddenNameInput = document.getElementById('employee_name');

        const syncEmployee = () => {
            const selectedOption = selector.options[selector.selectedIndex];

            firstNameInput.value = selectedOption.dataset.firstName || '';
            lastNameInput.value = selectedOption.dataset.lastName || '';
            emailInput.value = selectedOption.dataset.email || '';
            hiddenNameInput.value = selectedOption.dataset.fullName || '';
        };

        selector.addEventListener('change', syncEmployee);
        syncEmployee();
    });
</script>
@endpush
