<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'pseudo' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $credentials['pseudo'] = trim($credentials['pseudo']);

        $userModel = new User();
        $loginColumn = $userModel->loginIdentifierColumn();
        $matchingUsers = $userModel->newQuery()
            ->where($loginColumn, $credentials['pseudo'])
            ->get();

        $this->logAuthenticationDebug(
            'Authentication lookup started.',
            $matchingUsers,
            [
                'login_column' => $loginColumn,
                'matching_users_count' => $matchingUsers->count(),
                'pseudo' => $credentials['pseudo'],
            ]
        );

        /** @var User|null $user */
        $user = $matchingUsers->first(function (User $candidate) use ($credentials): bool {
            $matches = Hash::check($credentials['password'], $candidate->getAuthPassword());

            $this->logAuthenticationDebug(
                'Authentication candidate evaluated.',
                collect([$candidate]),
                [
                    'password_matches' => $matches,
                    'pseudo' => $credentials['pseudo'],
                ]
            );

            return $matches;
        });

        if (! $user) {
            $this->logAuthenticationDebug(
                'Authentication failed: no candidate matched the provided password.',
                $matchingUsers,
                [
                    'login_column' => $loginColumn,
                    'pseudo' => $credentials['pseudo'],
                ]
            );

            throw ValidationException::withMessages([
                'pseudo' => 'Les informations fournies sont invalides.',
            ]);
        }

        $this->logAuthenticationDebug(
            'Authentication succeeded.',
            collect([$user]),
            [
                'login_column' => $loginColumn,
                'pseudo' => $credentials['pseudo'],
            ]
        );

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('leave-requests.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function logAuthenticationDebug(string $message, $users, array $context = []): void
    {
        if (! env('AUTH_DEBUG', false)) {
            return;
        }

        Log::debug($message, array_merge($context, [
            'auth_connection' => config('database.auth_connection'),
            'default_connection' => config('database.default'),
            'users' => $users->map(fn (User $user): array => [
                'auth_identifier' => $user->getAuthIdentifier(),
                'email' => $user->email,
                'password_algo' => password_get_info($user->getAuthPassword())['algoName'] ?? 'unknown',
                'password_length' => strlen((string) $user->getAuthPassword()),
                'status' => $user->status,
            ])->values()->all(),
        ]));
    }
}
