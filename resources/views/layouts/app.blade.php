<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ressources Humaines')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GtvpGm0uCOwVtIAr72Xd1LSeX776BF3nf6/Dr7fP5AnbcW2CYwiVdc+GqORdzdrD" crossorigin="anonymous">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.08), transparent 30%),
                radial-gradient(circle at top right, rgba(249, 115, 22, 0.08), transparent 30%),
                linear-gradient(180deg, #f8fafc 0%, #eef4fb 100%);
            color: #1e293b;
            min-height: 100vh;
        }

        .app-navbar {
            background: linear-gradient(135deg, #0f4c81 0%, #2563eb 100%);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
            padding-top: 0.9rem;
            padding-bottom: 0.9rem;
        }

        .app-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: 0.02em;
            color: #fff !important;
        }

        .app-navbar .nav-link {
            color: rgba(255, 255, 255, 0.88) !important;
            font-weight: 600;
            border-radius: 0.7rem;
            padding: 0.55rem 0.9rem !important;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .app-navbar .nav-link:hover,
        .app-navbar .nav-link.active {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.14);
        }

        .page-shell {
            padding-bottom: 2.5rem;
        }

        .page-header-card {
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            padding: 1.25rem 1.4rem;
            margin-bottom: 1.5rem;
        }

        .page-header-card h1 {
            margin: 0;
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f172a;
        }

        .page-header-card p {
            margin: 0.35rem 0 0;
            color: #64748b;
            font-size: 0.95rem;
        }

        .content-wrapper {
            background: transparent;
        }

        .alert {
            border: 0;
            border-radius: 1rem;
            padding: 1rem 1.1rem;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        }

        .alert-success {
            background: linear-gradient(135deg, #dcfce7 0%, #ecfdf5 100%);
            color: #166534;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fff1f2 100%);
            color: #991b1b;
        }

        .alert ul {
            padding-left: 1.1rem;
        }

        .main-container {
            max-width: 1200px;
        }

        .footer-note {
            text-align: center;
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 2rem;
            padding-bottom: 1rem;
        }

        .status-pill {
            border-radius: 999px;
            background-color: rgba(255, 255, 255, 0.16);
            color: #fff;
            font-size: 0.78rem;
            padding: 0.4rem 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .view-card {
            border: 0;
            border-radius: 1.25rem;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .view-card-header-gradient {
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #0f4c81 0%, #2563eb 100%);
            color: #fff;
            padding: 1.25rem 1.5rem;
        }

        .view-card-header-gradient h1,
        .view-card-header-gradient h2,
        .view-card-header-gradient small {
            color: #fff;
        }

        .view-label {
            font-weight: 700;
            color: #334155;
        }

        .view-input,
        .view-select {
            border-radius: 0.75rem;
            border: 1px solid #cbd5e1;
            padding: 0.7rem 0.85rem;
            background: #fff;
        }

        .view-input:focus,
        .view-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .btn-view-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            border: none;
            border-radius: 0.8rem;
            font-weight: 700;
            box-shadow: 0 6px 16px rgba(249, 115, 22, 0.25);
        }

        .btn-view-primary:hover {
            background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);
        }

        .auth-card {
            max-width: 720px;
            margin: 0 auto;
        }

        @media (max-width: 991.98px) {
            .app-navbar .navbar-nav {
                margin-top: 0.75rem;
                gap: 0.35rem;
            }

            .app-navbar .nav-link {
                display: inline-block;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar mb-4">
        <div class="container main-container">

            <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
                <div class="navbar-nav align-items-lg-center gap-lg-2">
                    @auth
                        <a class="nav-link {{ request()->routeIs('leave-requests.index') ? 'active' : '' }}" href="{{ route('leave-requests.index') }}">
                            Synthèse
                        </a>

                        @if (auth()->user()->hasStatus(\App\Models\User::STATUS_ADMIN, \App\Models\User::STATUS_EMPLOYEE))
                            <a class="nav-link {{ request()->routeIs('leave-requests.create') ? 'active' : '' }}" href="{{ route('leave-requests.create') }}">
                                Nouvelle demande
                            </a>
                        @endif

                        <span class="status-pill">{{ str_replace('_', ' ', auth()->user()->status) }}</span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="nav-link border-0 bg-transparent" type="submit">Déconnexion</button>
                        </form>
                    @else
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Connexion</a>
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-container page-shell">
        <div class="page-header-card">
            <h1>Gestion des ressources humaines</h1>
            <p>Suivi des demandes, validation des congés et préparation des éléments utiles à la paie.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success mb-4">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <div class="fw-bold mb-2">Des éléments nécessitent une correction :</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="content-wrapper">
            @yield('content')
        </div>

        <div class="footer-note">
            Interface RH — suivi interne des demandes
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqk8aEAC5vlaQt1zG72Xd1LSeX776BF3nf6/Dr7fP5AnbcW2CYwiVdc+GqORdzdrD" crossorigin="anonymous"></script>
</body>
</html>
