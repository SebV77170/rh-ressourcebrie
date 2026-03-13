@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h2 class="h4 mb-4">Créer un utilisateur</h2>

            <form method="POST" action="{{ route('register.store') }}" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label for="name" class="form-label">Nom complet</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirmation du mot de passe</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div class="col-12">
                    <label for="status" class="form-label">Statut utilisateur</label>
                    <select class="form-select" id="status" name="status" required>
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

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">J'ai déjà un compte</a>
                    <button type="submit" class="btn btn-primary">Créer le compte</button>
                </div>
            </form>
        </div>
    </div>
@endsection
