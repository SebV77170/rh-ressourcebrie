<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ressources Humaines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GtvpGm0uCOwVtIAr72Xd1LSeX776BF3nf6/Dr7fP5AnbcW2CYwiVdc+GqORdzdrD" crossorigin="anonymous">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('leave-requests.index') }}">Espace RH</a>
        <div class="navbar-nav">
            <a class="nav-link" href="{{ route('leave-requests.index') }}">Synthèse</a>
            <a class="nav-link" href="{{ route('leave-requests.create') }}">Nouvelle demande</a>
        </div>
    </div>
</nav>
<div class="container">
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqk8aEAC5vlaQt1zG72Xd1LSeX776BF3nf6/Dr7fP5AnbcW2CYwiVdc+GqORdzdrD" crossorigin="anonymous"></script>
</body>
</html>
