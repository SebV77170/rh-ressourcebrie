@extends('layouts.app')

@section('title', 'Créer une demande de congé')

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

    .leave-create-page .form-control.form-control-lg {
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

                            <div class="mb-3">
                                <label for="employee_name" class="form-label view-label">Nom et prénom</label>
                                <input
                                    type="text"
                                    name="employee_name"
                                    id="employee_name"
                                    class="form-control form-control-lg view-input"
                                    placeholder="Ex : Jean Dupont"
                                    value="{{ old('employee_name') }}"
                                    required
                                >
                                <div class="field-hint">Indiquez l’identité de la personne concernée par la demande.</div>
                            </div>

                            <div class="mb-0">
                                <label for="employee_email" class="form-label view-label">Adresse email</label>
                                <input
                                    type="email"
                                    name="employee_email"
                                    id="employee_email"
                                    class="form-control view-input"
                                    placeholder="exemple@email.fr"
                                    value="{{ old('employee_email') }}"
                                    required
                                >
                                <div class="field-hint">Cette adresse permettra d’identifier clairement le salarié.</div>
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
@endsection
