
<x-guest-layout>
    <span class="eyebrow">Get started</span>
    <h2>Create your account</h2>
    <p class="subtitle">
        Join the Barangay Health portal to access
        convenient community healthcare services.
    </p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label for="name">Full Name</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Enter your full name"
                autocomplete="name"
                required
                autofocus
            >
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="email">Email Address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter your email address"
                autocomplete="username"
                required
            >
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                placeholder="Create a strong password"
                autocomplete="new-password"
                required
            >
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">
                Confirm Password
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                placeholder="Re-enter your password"
                autocomplete="new-password"
                required
            >
            @error('password_confirmation')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="submit-btn">
            Create Account →
        </button>
    </form>

    <p class="auth-switch">
        Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
    </p>
</x-guest-layout>


