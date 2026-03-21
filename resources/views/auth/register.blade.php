@extends('layouts.app')

@section('title', 'Inscription')
@section('page_title', 'Créer un compte utilisateur')
@section('page_intro', "Ajoutez facilement un profil avec une présentation épurée, cohérente avec l'identité visuelle de la Ressource'Brie.")

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <h2 class="h3 mb-1 text-info-emphasis">Créer un utilisateur</h2>
                            <p class="text-secondary mb-0">Renseignez les informations essentielles puis choisissez le rôle adapté.</p>
                        </div>
                        <span class="badge text-bg-info">Nouvel accès</span>
                    </div>

                    <form method="POST" action="{{ route('register.store') }}" class="row g-3">
                        @csrf

                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nom complet</label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">E-mail</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Mot de passe</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmation du mot de passe</label>
                            <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="col-12">
                            <label for="status" class="form-label fw-semibold">Statut utilisateur</label>
                            <select class="form-select form-select-lg" id="status" name="status" required>
                                <option value="" disabled {{ old('status') ? '' : 'selected' }}>Choisir un statut</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('status') === $status)>
                                        {{ match($status) {
                                            \App\Models\User::STATUS_ADMIN => 'Administrateur',
                                            \App\Models\User::STATUS_EMPLOYEE => 'Employé',
                                            \App\Models\User::STATUS_PAYROLL_MANAGER => 'Gestionnaire de paie',
                                            default => $status,
                                        } }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">J’ai déjà un compte</a>
                            <button type="submit" class="btn btn-success">Créer le compte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
