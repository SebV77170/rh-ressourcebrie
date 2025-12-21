@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card shadow-sm border-0 rounded-4">
                
                {{-- Header --}}
                <div class="card-header bg-primary bg-gradient text-white rounded-top-4 px-4 py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3 fs-3">
                            🗓️
                        </div>
                        <div>
                            <h1 class="h5 mb-0">Demande de congé</h1>
                            <small class="opacity-75">Merci de compléter les informations ci-dessous</small>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('leave-requests.store') }}">
                        @csrf

                        {{-- Identité --}}
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">👤 Informations salarié</h6>

                            <div class="mb-3">
                                <label for="employee_name" class="form-label">Nom et prénom</label>
                                <input
                                    type="text"
                                    name="employee_name"
                                    id="employee_name"
                                    class="form-control form-control-lg"
                                    placeholder="Ex : Jean Dupont"
                                    value="{{ old('employee_name') }}"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="employee_email" class="form-label">Adresse email</label>
                                <input
                                    type="email"
                                    name="employee_email"
                                    id="employee_email"
                                    class="form-control"
                                    placeholder="exemple@email.fr"
                                    value="{{ old('employee_email') }}"
                                    required
                                >
                            </div>
                        </div>

                        {{-- Dates --}}
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">📅 Période de congé</h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Date de début</label>
                                    <input
                                        type="date"
                                        name="start_date"
                                        id="start_date"
                                        class="form-control"
                                        value="{{ old('start_date') }}"
                                        required
                                    >
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">Date de fin</label>
                                    <input
                                        type="date"
                                        name="end_date"
                                        id="end_date"
                                        class="form-control"
                                        value="{{ old('end_date') }}"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        {{-- Motif --}}
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">📝 Motif (facultatif)</h6>

                            <textarea
                                name="reason"
                                id="reason"
                                class="form-control"
                                rows="4"
                                placeholder="Ex : congés payés, événement familial, repos..."
                            >{{ old('reason') }}</textarea>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>

                            <button class="btn btn-primary btn-lg px-4" type="submit">
                                Envoyer la demande
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
