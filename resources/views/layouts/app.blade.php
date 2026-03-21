<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ressource\'Brie RH')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-3 text-decoration-none" href="{{ auth()->check() ? route('leave-requests.index') : route('login') }}">
                <img src="{{ asset('logo-ressource-brie.png') }}" alt="Logo Ressource'Brie" width="58" height="58" class="rounded-circle shadow-sm">
                <span class="d-flex flex-column lh-sm">
                    <span class="fw-bold fs-4 text-info-emphasis">Ressource'Brie RH</span>
                    <span class="small text-success-emphasis">Gestion simple des congés</span>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Basculer la navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <div class="navbar-nav ms-auto align-items-lg-center gap-2">
                    @auth
                        <a class="nav-link px-3 {{ request()->routeIs('leave-requests.index') ? 'active fw-semibold text-info-emphasis' : 'text-secondary' }}" href="{{ route('leave-requests.index') }}">Tableau de bord</a>

                        @if (auth()->user()->hasStatus(\App\Models\User::STATUS_ADMIN, \App\Models\User::STATUS_EMPLOYEE))
                            <a class="nav-link px-3 {{ request()->routeIs('leave-requests.create') ? 'active fw-semibold text-info-emphasis' : 'text-secondary' }}" href="{{ route('leave-requests.create') }}">Nouvelle demande</a>
                        @endif

                        @if (auth()->user()->hasStatus(\App\Models\User::STATUS_ADMIN))
                            <a class="nav-link px-3 {{ request()->routeIs('payroll-managers.*') ? 'active fw-semibold text-info-emphasis' : 'text-secondary' }}" href="{{ route('payroll-managers.index') }}">Gestionnaires de paie</a>
                        @endif

                        <span class="badge rounded-pill text-bg-success-subtle text-success-emphasis border border-success-subtle text-uppercase px-3 py-2">{{ str_replace('_', ' ', auth()->user()->status) }}</span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-secondary" type="submit">Déconnexion</button>
                        </form>
                    @else
                        <a class="nav-link px-3 {{ request()->routeIs('login') ? 'active fw-semibold text-info-emphasis' : 'text-secondary' }}" href="{{ route('login') }}">Connexion</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-12 col-xl-10">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-body p-4 p-lg-5 bg-white">
                            <div class="row align-items-center g-4">
                                <div class="col-lg-7 order-2 order-lg-1">
                                    <h1 class="display-5 fw-bold text-dark mb-3">@yield('page_title', 'Une gestion des congés moderne et ergonomique')</h1>
                                    <p class="fs-5 text-secondary mb-4">@yield('page_intro', "Une interface claire, moderne et fluide, pensée pour les équipes de la Ressource'Brie.")</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-5 text-center order-1 order-lg-2">
                                    <div class="bg-info-subtle rounded-5 p-4 d-inline-block shadow-sm border border-info-subtle">
                                        <img src="{{ asset('logo-ressource-brie.png') }}" alt="Logo Ressource'Brie" width="190" height="190" class="img-fluid rounded-circle bg-white p-2 shadow-sm">
                                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
