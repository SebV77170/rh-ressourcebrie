<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ressource\'Brie RH')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GtvpGm0uCOwVtIAr72Xd1LSeX776BF3nf6/Dr7fP5AnbcW2CYwiVdc+GqORdzdrD" crossorigin="anonymous">
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg bg-info-subtle border-bottom sticky-top">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-3 fw-bold text-info-emphasis" href="{{ route('leave-requests.index') }}">
                <img src="{{ asset('logo-ressource-brie.svg') }}" alt="Logo Ressource'Brie" width="56" height="56" class="rounded-circle border border-2 border-white shadow-sm">
                <span>
                    Ressource'Brie RH
                    <span class="d-block fs-6 fw-normal text-success-emphasis">Gestion simple des congés</span>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Basculer la navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <div class="navbar-nav ms-auto align-items-lg-center gap-2">
                    @auth
                        <a class="nav-link {{ request()->routeIs('leave-requests.index') ? 'active fw-semibold text-success-emphasis' : '' }}" href="{{ route('leave-requests.index') }}">Tableau de bord</a>

                        @if (auth()->user()->hasStatus(\App\Models\User::STATUS_ADMIN, \App\Models\User::STATUS_EMPLOYEE))
                            <a class="nav-link {{ request()->routeIs('leave-requests.create') ? 'active fw-semibold text-success-emphasis' : '' }}" href="{{ route('leave-requests.create') }}">Nouvelle demande</a>
                        @endif

                        <span class="badge rounded-pill text-bg-success text-uppercase">{{ str_replace('_', ' ', auth()->user()->status) }}</span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-info" type="submit">Déconnexion</button>
                        </form>
                    @else
                        <a class="nav-link {{ request()->routeIs('login') ? 'active fw-semibold text-success-emphasis' : '' }}" href="{{ route('login') }}">Connexion</a>
                        <a class="btn btn-success" href="{{ route('register') }}">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-12 col-xl-10">
                    <div class="card border-0 shadow-sm bg-white">
                        <div class="card-body p-4 p-lg-5">
                            <div class="row align-items-center g-4">
                                <div class="col-md-auto text-center">
                                    <img src="{{ asset('logo-ressource-brie.svg') }}" alt="Logo Ressource'Brie" width="120" height="120" class="img-fluid rounded-circle shadow-sm">
                                </div>
                                <div class="col">
                                    <span class="badge text-bg-info mb-3">Interface Bootstrap</span>
                                    <h1 class="display-6 fw-bold text-info-emphasis mb-2">@yield('page_title', 'Une gestion des congés moderne et ergonomique')</h1>
                                    <p class="lead text-secondary mb-0">@yield('page_intro', "Une interface claire, rapide à prendre en main et pensée pour les équipes de la Ressource'Brie.")</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    @if (session('success'))
                        <div class="alert alert-success shadow-sm" role="alert">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm" role="alert">
                            <div class="fw-semibold mb-2">Merci de corriger les éléments suivants :</div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
