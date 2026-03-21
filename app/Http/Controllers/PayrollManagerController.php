<?php

namespace App\Http\Controllers;

use App\Models\PayrollManager;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            'uuid_user' => $this->resolveUserUuid($user),
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
        return User::query()
            ->get()
            ->map(function (User $user): ?object {
                $uuid = $this->resolveUserUuid($user);

                if ($uuid === null || $uuid === '') {
                    return null;
                }

                return (object) [
                    'uuid_user' => $uuid,
                    'name' => $user->name !== '' ? $user->name : 'Utilisateur sans nom',
                    'email' => $user->email,
                    'status' => $user->status,
                ];
            })
            ->filter()
            ->sortBy([
                fn (object $user): string => mb_strtolower($user->name),
                fn (object $user): string => mb_strtolower((string) $user->email),
            ])
            ->values();
    }

    protected function findUserByUuid(string $uuid): ?User
    {
        return User::query()
            ->get()
            ->first(fn (User $user): bool => $this->resolveUserUuid($user) === $uuid);
    }

    protected function resolveUserUuid(User $user): ?string
    {
        $uuid = $user->getAttribute('uuid_user')
            ?? $user->getRawOriginal('uuid_user')
            ?? $user->getOriginal('uuid_user');

        if (filled($uuid)) {
            return (string) $uuid;
        }

        $identifier = $user->getAuthIdentifier();

        return filled($identifier) ? (string) $identifier : null;
    }
}
