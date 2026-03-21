<?php

namespace App\Http\Controllers;

use App\Models\PayrollManager;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $managedUserIds = $payrollManagers
            ->pluck('uuid_user')
            ->filter()
            ->map(fn (mixed $uuid): string => (string) $uuid)
            ->values();

        $users = $this->availableUsers($managedUserIds);
        $usersByUuid = $this->usersByUuid($managedUserIds);

        return view('payroll_managers.index', [
            'users' => $users,
            'payrollManagers' => $payrollManagers->map(function (PayrollManager $manager) use ($usersByUuid): object {
                $user = $usersByUuid->get((string) $manager->uuid_user);

                return (object) [
                    'id' => $manager->id,
                    'uuid_user' => $manager->uuid_user,
                    'name' => $user?->name ?? 'Utilisateur introuvable',
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
            'uuid_user' => $validated['uuid_user'],
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

    protected function availableUsers(Collection $excludedUuids): Collection
    {
        $query = User::query();
        $keyColumn = $this->userKeyColumn();

        if ($excludedUuids->isNotEmpty()) {
            $query->whereNotIn($keyColumn, $excludedUuids->all());
        }

        $users = $query->get();

        return $users
            ->filter(fn (User $user): bool => filled($user->getAuthIdentifier()))
            ->map(fn (User $user): object => (object) [
                'uuid_user' => (string) $user->getAuthIdentifier(),
                'name' => $user->name !== '' ? $user->name : 'Utilisateur sans nom',
                'email' => $user->email,
                'status' => $user->status,
            ])
            ->sortBy([
                fn (object $user): string => mb_strtolower($user->name),
                fn (object $user): string => mb_strtolower((string) $user->email),
            ])
            ->values();
    }

    protected function usersByUuid(Collection $uuids): Collection
    {
        if ($uuids->isEmpty()) {
            return collect();
        }

        $query = User::query();
        $keyColumn = $this->userKeyColumn();

        return $query->whereIn($keyColumn, $uuids->all())
            ->get()
            ->keyBy(fn (User $user): string => (string) $user->getAuthIdentifier());
    }

    protected function findUserByUuid(string $uuid): ?User
    {
        $keyColumn = $this->userKeyColumn();

        return User::query()
            ->where($keyColumn, $uuid)
            ->first();
    }

    protected function userKeyColumn(): string
    {
        $user = new User();

        if (Schema::connection($user->getConnectionName())->hasColumn($user->getTable(), 'uuid_user')) {
            return 'uuid_user';
        }

        return $user->getKeyName();
    }
}
