@extends('layouts.app')

@section('title', 'Gestionnaires de paie')
@section('page_title', 'Administration des gestionnaires de paie')
@section('page_intro', 'Choisissez un utilisateur issu de la base objets, ajoutez-le comme gestionnaire de paie et révoquez-le si nécessaire.')

@section('content')
<div class="row g-4">
    <div class="col-12 col-xl-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white py-3">
                <h2 class="h4 mb-1">Ajouter un gestionnaire</h2>
                <p class="mb-0 opacity-75">La liste ci-dessous provient de la base d’authentification.</p>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('payroll-managers.store') }}" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label for="uuid_user" class="form-label fw-semibold">Utilisateur</label>
                        <select name="uuid_user" id="uuid_user" class="form-select form-select-lg" required>
                            <option value="">Sélectionnez un utilisateur</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->uuid_user }}" @selected(old('uuid_user') === $user->uuid_user)>
                                    {{ $user->name }}@if($user->email) — {{ $user->email }} @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Les utilisateurs déjà désignés comme gestionnaires de paie n’apparaissent plus dans la liste.</div>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            <div class="fw-semibold mb-2">Rappel</div>
                            <ul class="mb-0 ps-3">
                                <li>Seuls les administrateurs accèdent à cet écran.</li>
                                <li>L’ajout crée une ligne dans la table <code>payroll_manager</code>.</li>
                                <li>La révocation supprime immédiatement ce droit.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12 d-grid d-sm-flex justify-content-sm-end">
                        <button type="submit" class="btn btn-success btn-lg">Ajouter le gestionnaire</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-info text-white py-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    <h2 class="h4 mb-1">Gestionnaires de paie actifs</h2>
                    <p class="mb-0 opacity-75">Vue récapitulative des personnes habilitées.</p>
                </div>
                <span class="badge rounded-pill text-bg-light text-info-emphasis px-3 py-2">{{ $payrollManagers->count() }} actif(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>UUID utilisateur</th>
                                <th>Ajouté le</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payrollManagers as $manager)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $manager->name }}</div>
                                    </td>
                                    <td class="text-secondary">{{ $manager->email ?? '—' }}</td>
                                    <td><code>{{ $manager->uuid_user }}</code></td>
                                    <td>{{ optional($manager->created_at)?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('payroll-managers.destroy', $manager->id) }}" onsubmit="return confirm('Révoquer ce gestionnaire de paie ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Révoquer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-secondary">Aucun gestionnaire de paie n’est actuellement défini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
