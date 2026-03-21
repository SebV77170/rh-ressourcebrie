<?php

namespace App\Http\Controllers;

use App\Models\PayrollManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PayrollManagerController extends Controller
{
    public function index(): View
    {
        $payrollManagers = PayrollManager::query()
            ->orderByDesc('created_at')
            ->get();

        $authUsers = $this->authUsers();
        $usersByUuid = $authUsers->keyBy('uuid_user');
        $managedUserIds = $payrollManagers
            ->pluck('uuid_user')
            ->filter()
            ->map(fn (mixed $uuid): string => (string) $uuid)
            ->values();

        return view('payroll_managers.index', [
            'users' => $authUsers
                ->reject(fn (object $user): bool => $managedUserIds->contains($user->uuid_user))
                ->values(),
            'payrollManagers' => $payrollManagers->map(function (PayrollManager $manager) use ($usersByUuid): object {
                $user = $usersByUuid->get((string) $manager->uuid_user);

                return (object) [
                    'id' => $manager->id,
                    'uuid_user' => $manager->uuid_user,
                    'name' => $user?->name ?? 'Utilisateur supprimé ou introuvable',
                    'email' => $user?->email,
                    'created_at' => $manager->created_at,
                ];
            }),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'uuid_user' => [
                'required',
                'string',
                Rule::unique('payroll_manager', 'uuid_user'),
            ],
        ]);

        $user = $this->findUserByUuid($validated['uuid_user']);

        if (! $user) {
            return back()
                ->withInput()
                ->withErrors(['uuid_user' => 'L’utilisateur sélectionné est introuvable dans la base d’authentification.']);
        }

        PayrollManager::create([
            'uuid_user' => $user->uuid_user,
        ]);

        return redirect()
            ->route('payroll-managers.index')
            ->with('success', 'Le gestionnaire de paie a bien été ajouté.');
    }

    public function destroy(PayrollManager $payrollManager): RedirectResponse
    {
        $payrollManager->delete();

        return redirect()
            ->route('payroll-managers.index')
            ->with('success', 'Le gestionnaire de paie a bien été révoqué.');
    }

    protected function authUsers(): Collection
    {
        $connection = config('database.auth_connection');
        $query = DB::connection($connection)->table('users');

        if (! Schema::connection($connection)->hasColumn('users', 'uuid_user')) {
            return collect();
        }

        return $query
            ->select($this->authUserSelectColumns())
            ->get()
            ->map(function (object $user): ?object {
                $uuid = isset($user->uuid_user) ? trim((string) $user->uuid_user) : '';

                if ($uuid === '') {
                    return null;
                }

                return (object) [
                    'uuid_user' => $uuid,
                    'name' => $this->formatAuthUserName($user),
                    'email' => $this->extractAuthUserEmail($user),
                ];
            })
            ->filter()
            ->sortBy([
                fn (object $user): string => mb_strtolower($user->name),
                fn (object $user): string => mb_strtolower((string) $user->email),
            ])
            ->values();
    }

    protected function findUserByUuid(string $uuid): ?object
    {
        return $this->authUsers()
            ->first(fn (object $user): bool => $user->uuid_user === $uuid);
    }

    protected function authUserSelectColumns(): array
    {
        $connection = config('database.auth_connection');
        $columns = ['uuid_user'];

        foreach (['name', 'prenom', 'nom', 'email', 'mail'] as $column) {
            if (Schema::connection($connection)->hasColumn('users', $column)) {
                $columns[] = $column;
            }
        }

        return array_values(array_unique($columns));
    }

    protected function formatAuthUserName(object $user): string
    {
        $fullName = trim((string) ($user->name ?? ''));

        if ($fullName !== '') {
            return $fullName;
        }

        $legacyName = trim(implode(' ', array_filter([
            $user->prenom ?? null,
            $user->nom ?? null,
        ])));

        return $legacyName !== '' ? $legacyName : 'Utilisateur sans nom';
    }

    protected function extractAuthUserEmail(object $user): ?string
    {
        $email = trim((string) ($user->email ?? ''));

        if ($email !== '') {
            return $email;
        }

        $legacyEmail = trim((string) ($user->mail ?? ''));

        return $legacyEmail !== '' ? $legacyEmail : null;
    }
}
