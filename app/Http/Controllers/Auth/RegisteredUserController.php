<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new patient.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);

        // Public registration always creates a patient.
        // Users cannot choose an elevated role.
        $user = new User();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);

        $user->role = 'patient';
        $user->status = 'pending';

        $user->save();

        event(new Registered($user));

        // Do not automatically log in pending users.
        return redirect()
            ->route('login')
            ->with(
                'account_notice',
                'Your account has been created successfully! '
                . 'It is now pending verification by the Barangay '
                . 'Health Worker. You will be able to log in '
                . 'after your account is approved.'
            )
            ->with('account_notice_type', 'pending');
    }
}
