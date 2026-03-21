@extends('layouts.app')

@section('title', 'Connexion')
@section('page_title', 'Connexion à l’espace RH')
@section('page_intro', "Accédez rapidement au suivi des congés avec une interface lisible, pensée pour la Ressource'Brie.")

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <h2 class="h3 mb-1 text-info-emphasis">Connexion</h2>
                            <p class="text-secondary mb-0">Retrouvez vos demandes, validations et suivis en un coup d’œil.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label for="email" class="form-label fw-semibold">E-mail</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="col-12">
                            <label for="password" class="form-label fw-semibold">Mot de passe</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                        </div>

                        <div class="col-12 form-check ms-1">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Se souvenir de moi</label>
                        </div>

                        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                            <a href="{{ route('register') }}" class="btn btn-outline-secondary">Créer un compte</a>
                            <button type="submit" class="btn btn-info text-white">Se connecter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
