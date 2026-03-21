<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $matchingUsers = $userModel->newQuery()
            ->where($userModel->loginIdentifierColumn(), $credentials['pseudo'])
            ->get();

        /** @var User|null $user */
        $user = $matchingUsers->first(fn (User $candidate): bool => Hash::check($credentials['password'], $candidate->getAuthPassword()));

        if (! $user) {
            throw ValidationException::withMessages([
                'pseudo' => 'Les informations fournies sont invalides.',
            ]);
        }

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
}
