
<x-guest-layout>
    <span class="eyebrow">Barangay Health Portal</span>

    <h2>Welcome Back!</h2>
    <p class="subtitle">
        Choose your account role and enter your
        credentials to access your dashboard.
    </p>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert" style="background:#fff0f0;color:#b42318;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Role Selection --}}
        <div class="field">
            <label>Login As</label>

            <div class="role-grid">

                <label class="role-card">
                    <input type="radio" name="role"
                        value="administrator"
                        {{ old('role') === 'administrator' ? 'checked' : '' }}
                        required>
                    <span class="role-content">
                        <span class="role-icon">⚙</span>
                        <strong>Administrator</strong>
                    </span>
                </label>

                <label class="role-card">
                    <input type="radio" name="role"
                        value="barangay_health_worker"
                        {{ old('role') === 'barangay_health_worker' ? 'checked' : '' }}>
                    <span class="role-content">
                        <span class="role-icon">✚</span>
                        <strong>Barangay Health Worker</strong>
                    </span>
                </label>

                <label class="role-card">
                    <input type="radio" name="role"
                        value="healthcare_personnel"
                        {{ old('role') === 'healthcare_personnel' ? 'checked' : '' }}>
                    <span class="role-content">
                        <span class="role-icon">♡</span>
                        <strong>Healthcare Personnel</strong>
                    </span>
                </label>

                <label class="role-card">
                    <input type="radio" name="role"
                        value="patient"
                        {{ old('role', 'patient') === 'patient' ? 'checked' : '' }}>
                    <span class="role-content">
                        <span class="role-icon">♙</span>
                        <strong>Patient</strong>
                    </span>
                </label>

            </div>

            @error('role')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="field">
            <label for="email">Email Address</label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter your email"
                autocomplete="username"
                required
                autofocus
            >

            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="field">
            <label for="password">Password</label>

            <input
                id="password"
                type="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
            >

            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember / Forgot --}}
        <div class="form-options">
            <label class="remember">
                <input type="checkbox"
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>

            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    Forgot Password?
                </a>
            @endif
        </div>

        <button type="submit" class="submit-btn">
            Sign In to Dashboard →
        </button>
    </form>

    @if(Route::has('register'))
        <p class="auth-switch">
            Don't have an account?
            <a href="{{ route('register') }}">
                Create Account
            </a>
        </p>
    @endif

    <style>
        .role-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .role-card {
            cursor: pointer;
            display: block;
            position: relative;
        }

        .role-card input {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
        }

        .role-content {
            min-height: 112px;
            padding: 16px 10px;
            background: #ffffff;
            border: 1.5px solid #dce8df;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 9px;
            text-align: center;
            transition: .2s ease;
        }

        .role-icon {
            width: 39px;
            height: 39px;
            border-radius: 12px;
            background: #eaf5ed;
            color: #1d6748;
            font-size: 23px;
            display: grid;
            place-items: center;
        }

        .role-content strong {
            font-size: 12px;
            line-height: 1.5;
            font-weight: 650;
            color: #34483b;
        }

        .role-card:hover .role-content {
            border-color: #5ba17b;
            background: #f5fbf6;
        }

        .role-card input:checked + .role-content {
            border-color: #23744f;
            background: #e8f5ec;
            box-shadow: 0 0 0 1px #23744f;
        }

        .role-card input:checked + .role-content strong {
            color: #155c3b;
        }

        .role-card input:focus-visible + .role-content {
            outline: 3px solid #82c9a0;
            outline-offset: 2px;
        }

        @media(max-width: 400px) {
            .role-grid { gap: 8px; }
            .role-content { padding: 12px 6px; }
            .role-content strong { font-size: 11px; }
        }
    </style>
</x-guest-layout>
