<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Authenticate and check account approval.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'role' => [
                'required',
                'in:administrator,bhw,healthcare_personnel,patient',
            ],
        ]);

        // Check email and password first.
        $request->authenticate();

        $user = $request->user();

        $registeredRole = strtolower(
            trim($user->role ?? '')
        );

        $selectedRole = $request->input('role');

        // The selected role must match the database.
        if ($registeredRole !== $selectedRole) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'role' => 'The selected role does not match your account.',
            ]);
        }

        // Patient accounts require BHW verification.
        if ($registeredRole === 'patient') {

            $status = strtolower(
                trim($user->status ?? 'pending')
            );

            if ($status === 'pending') {

                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->with(
                        'account_notice',
                        'Your account is still pending. '
                        . 'A Barangay Health Worker must '
                        . 'verify your residency before '
                        . 'you can access your dashboard.'
                    )
                    ->with('account_notice_type', 'pending');
            }

            if ($status === 'rejected') {

                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->with(
                        'account_notice',
                        'Your account verification was rejected. '
                        . 'Please contact your barangay health office '
                        . 'for assistance.'
                    )
                    ->with('account_notice_type', 'rejected');
            }

            if ($status !== 'approved') {

                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Your account is not authorized for access.',
                ]);
            }
        }

        $request->session()->regenerate();

        // Redirect to the matching dashboard.
        if ($registeredRole === 'patient') {
            return redirect()
                ->route('patient.dashboard')
                ->with('success', 'Your account is approved. Welcome!');
        }

        if ($registeredRole === 'bhw') {
            return redirect()->route('bhw.dashboard');
        }

        if ($registeredRole === 'healthcare_personnel') {
            return redirect()->route('staff.dashboard');
        }

        // Administrator dashboard is not set up yet.
        if ($registeredRole === 'administrator') {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'role' => 'Administrator dashboard is not configured yet.',
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        throw ValidationException::withMessages([
            'role' => 'Invalid account role.',
        ]);
    }

    /**
     * Log out.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
