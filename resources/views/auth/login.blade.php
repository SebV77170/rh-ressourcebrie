@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <div class="card view-card auth-card">
        <div class="card-body p-4 p-lg-5">
            <h2 class="h4 mb-4">Connexion</h2>

            <form method="POST" action="{{ route('login.store') }}" class="row g-3">
                @csrf

                <div class="col-12">
                    <label for="email" class="form-label view-label">E-mail</label>
                    <input type="email" class="form-control view-input" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="col-12">
                    <label for="password" class="form-label view-label">Mot de passe</label>
                    <input type="password" class="form-control view-input" id="password" name="password" required>
                </div>

                <div class="col-12 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary">Créer un compte</a>
                    <button type="submit" class="btn btn-primary btn-view-primary">Se connecter</button>
                </div>
            </form>
        </div>
    </div>
@endsection
