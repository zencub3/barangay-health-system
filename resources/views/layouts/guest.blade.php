
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Barangay Health System') }}</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f2f7f3;
            color: #22372d;
        }

        .auth-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        .auth-brand {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 70px 12%;
            background: linear-gradient(145deg, #123f32, #247458);
            color: white;
            overflow: hidden;
        }

        .auth-brand::after {
            content: '';
            position: absolute;
            width: 440px;
            height: 440px;
            border: 70px solid rgba(255,255,255,.045);
            border-radius: 50%;
            bottom: -220px;
            right: -220px;
        }

        .brand-mark {
            width: 64px;
            height: 64px;
            background: #d7f0df;
            color: #176445;
            border-radius: 20px;
            display: grid;
            place-items: center;
            font-size: 35px;
            font-weight: bold;
            margin-bottom: 32px;
        }

        .auth-brand h1 {
            font-size: clamp(32px, 4vw, 54px);
            letter-spacing: -1.5px;
            line-height: 1.16;
            margin: 0 0 18px;
        }

        .auth-brand p {
            color: #d3eade;
            font-size: 16px;
            line-height: 1.8;
            max-width: 430px;
        }

        .brand-features {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 32px;
        }

        .brand-features span {
            padding: 10px 16px;
            border: 1px solid #ffffff35;
            border-radius: 30px;
            font-size: 12px;
            color: #e8f7ec;
        }

        .auth-content {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 430px;
        }

        .eyebrow {
            color: #277655;
            font-size: 12px;
            letter-spacing: 2px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .auth-card h2 {
            font-size: 32px;
            letter-spacing: -1px;
            margin: 12px 0 8px;
            color: #183e30;
        }

        .subtitle {
            color: #718078;
            margin-bottom: 32px;
            font-size: 14px;
            line-height: 1.7;
        }

        .field { margin-bottom: 20px; }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 650;
            margin-bottom: 9px;
        }

        .field input {
            display: block;
            width: 100%;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid #d9e5db;
            border-radius: 12px;
            font: inherit;
            font-size: 14px;
            outline: none;
            transition: .2s;
        }

        .field input:focus {
            border-color: #287c5b;
            box-shadow: 0 0 0 3px #287c5b1c;
        }

        .field input::placeholder { color: #9ca9a0; }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 8px 0 25px;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember input { accent-color: #226a4e; }

        a { color: #287453; text-decoration: none; font-weight: 650; }
        a:hover { text-decoration: underline; }

        .submit-btn {
            width: 100%;
            border: none;
            background: #216e4f;
            color: #fff;
            border-radius: 12px;
            padding: 15px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            transition: .2s;
        }

        .submit-btn:hover { background: #175239; }

        .auth-switch {
            text-align: center;
            margin-top: 27px;
            color: #718078;
            font-size: 13px;
        }

        .form-error {
            color: #ba3535;
            font-size: 12px;
            margin-top: 7px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #def3e4;
            color: #236b43;
        }

        .auth-footer {
            margin-top: 35px;
            color: #96a49a;
            font-size: 11px;
            text-align: center;
        }

        @media(max-width: 850px) {
            .auth-layout { grid-template-columns: 1fr; }
            .auth-brand { padding: 30px 25px; }
            .auth-brand h1 { font-size: 26px; }
            .auth-brand p { font-size: 13px; margin-bottom: 0; }
            .brand-mark { width: 45px; height: 45px; font-size: 25px; margin-bottom: 15px; }
            .brand-features { display: none; }
            .auth-content { padding: 35px 22px; }
        }
    </style>
</head>
<body>
    <main class="auth-layout">
        <section class="auth-brand">
            <div class="brand-mark">+</div>
            <h1>Better Health.<br>Stronger Community.</h1>
            <p>
                Welcome to the Barangay Health Management System.
                Access community healthcare services and manage
                consultations in one convenient place.
            </p>
            <div class="brand-features">
                <span>✓ Patient Care</span>
                <span>✓ Appointments</span>
                <span>✓ Health Services</span>
            </div>
        </section>

        <section class="auth-content">
            <div class="auth-card">
                {{ $slot }}

                <p class="auth-footer">
                    © {{ date('Y') }} Barangay Health Management System
                </p>
            </div>
        </section>
    </main>
</body>
</html>

