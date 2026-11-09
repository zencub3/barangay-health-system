<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        // Strict role validation checking if database role matches the selected dropdown
        if (!$user->hasRole($request->input('role'))) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw \Illuminate\Validation\ValidationException::withMessages([
                'role' => 'The selected role does not match your registered account.',
            ]);
        }

        $request->session()->regenerate();

        // Redirect based on validated user role
        $role = strtolower(trim($user->role ?? ''));

        if ($role === 'patient') {
            return redirect()->route('patient.dashboard');
        } elseif ($role === 'bhw') {
            return redirect()->route('bhw.dashboard');
        } elseif ($role === 'healthcare_personnel') {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}