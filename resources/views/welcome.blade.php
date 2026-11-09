
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Health Management System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f2f7f3;
            color: #1c4030;
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px 7%;
            background: white;
            border-bottom: 1px solid #e2ece5;
            box-shadow: 0 3px 18px rgba(20,70,45,.04);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #184e37;
            text-decoration: none;
        }

        .logo-icon {
            display: grid;
            place-items: center;
            width: 46px;
            height: 46px;
            background: #216e4f;
            color: white;
            border-radius: 13px;
            font-size: 28px;
            font-weight: bold;
        }

        .logo strong {
            display: block;
            font-size: 17px;
        }

        .logo small {
            display: block;
            color: #758b7b;
            margin-top: 3px;
            font-size: 11px;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 11px;
            font-size: 14px;
            font-weight: 650;
            text-decoration: none;
            transition: .2s ease;
        }

        .btn-outline {
            color: #216e4f;
            background: white;
            border: 1px solid #b5d1bd;
        }

        .btn-outline:hover {
            background: #edf7ef;
            border-color: #216e4f;
            transform: translateY(-2px);
        }

        .btn-primary {
            color: white;
            background: #216e4f;
            border: 1px solid #216e4f;
            box-shadow: 0 5px 12px rgba(33,110,79,.18);
        }

        .btn-primary:hover {
            background: #175239;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(33,110,79,.25);
        }

        .btn:focus-visible {
            outline: 3px solid #8bcaa4;
            outline-offset: 3px;
        }

        /* Welcome Section */
        .hero {
            min-height: calc(100vh - 90px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 7%;
            background:
                radial-gradient(circle at 80% 25%, #d7eddf 0%, transparent 36%),
                linear-gradient(140deg, #f2f7f3, #e8f4eb);
        }

        .hero-card {
            width: 100%;
            max-width: 1080px;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            background: white;
            border: 1px solid #dce9df;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(28,77,48,.08);
        }

        .hero-content {
            padding: 65px 55px;
        }

        .eyebrow {
            display: inline-block;
            background: #e6f4e9;
            color: #216e4f;
            padding: 9px 16px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 24px;
        }

        .hero h1 {
            font-size: clamp(32px, 4vw, 49px);
            letter-spacing: -1.5px;
            line-height: 1.17;
            color: #183e30;
            margin-bottom: 20px;
        }

        .hero h1 span {
            color: #278259;
        }

        .hero p {
            color: #708476;
            line-height: 1.85;
            font-size: 15px;
            max-width: 440px;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 32px;
        }

        .hero-buttons .btn {
            padding: 14px 25px;
        }

        /* Features */
        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 36px;
        }

        .features span {
            font-size: 12px;
            color: #53715d;
            background: #f3f9f4;
            padding: 9px 13px;
            border-radius: 9px;
        }

        /* Right Panel */
        .hero-visual {
            position: relative;
            background: linear-gradient(145deg, #123f32, #247458);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 430px;
            overflow: hidden;
        }

        .hero-visual::before {
            content: "";
            position: absolute;
            width: 400px;
            height: 400px;
            border: 65px solid rgba(255,255,255,.05);
            border-radius: 50%;
            top: -170px;
            right: -150px;
        }

        .hero-visual::after {
            content: "";
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(200,240,210,.06);
            border-radius: 50%;
            bottom: -140px;
            left: -90px;
        }

        .medical-icon {
            position: relative;
            z-index: 1;
            width: 155px;
            height: 155px;
            display: grid;
            place-items: center;
            background: #d7f0df;
            color: #216e4f;
            border-radius: 36px;
            font-size: 110px;
            font-weight: bold;
            box-shadow: 0 20px 40px rgba(0,0,0,.13);
            margin-bottom: 28px;
        }

        .visual-title {
            position: relative;
            z-index: 1;
            font-size: 23px;
            font-weight: 750;
            color: white;
            text-align: center;
        }

        .visual-text {
            position: relative;
            z-index: 1;
            color: #cde7d6;
            text-align: center;
            margin-top: 9px;
            font-size: 13px;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 25px;
            font-size: 12px;
            color: #7c9484;
        }

        /* Tablet Responsive */
        @media(max-width: 850px) {
            .hero-card {
                grid-template-columns: 1fr;
            }

            .hero-content {
                padding: 45px 30px;
            }

            .hero-visual {
                min-height: 280px;
                padding: 40px 20px;
            }

            .medical-icon {
                width: 100px;
                height: 100px;
                font-size: 75px;
                border-radius: 25px;
            }
        }

        /* Mobile Responsive */
        @media(max-width: 550px) {
            .navbar {
                padding: 16px 5%;
            }

            .logo strong {
                font-size: 14px;
            }

            .nav-actions {
                width: 100%;
            }

            .nav-actions .btn {
                flex: 1;
                padding: 11px 13px;
            }

            .hero {
                padding: 30px 5%;
            }

            .hero-content {
                padding: 34px 22px;
            }

            .hero h1 {
                font-size: 33px;
            }
        }
    </style>
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar">

        <a href="{{ url('/') }}" class="logo">
            <span class="logo-icon">+</span>
            <span>
                <strong>Barangay Health</strong>
                <small>Management System</small>
            </span>
        </a>

        <!-- Register Button Only -->
        <div class="nav-actions">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                    Dashboard →
                </a>
            @else
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Register →
                    </a>
                @endif
            @endauth
        </div>

    </nav>

    <!-- Hero Section -->
    <main class="hero">
        <div class="hero-card">

            <!-- Left Content -->
            <div class="hero-content">

                <span class="eyebrow">
                    COMMUNITY HEALTHCARE PORTAL
                </span>

                <h1>
                    Better Health.<br>
                    <span>Stronger Community.</span>
                </h1>

                <p>
                    Welcome to the Barangay Health Management
                    System. Access community healthcare services,
                    manage appointment requests, and connect
                    with healthcare personnel through one
                    convenient platform.
                </p>

                <div class="hero-buttons">

                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                            Go to Dashboard →
                        </a>
                    @else
                        @if(Route::has('login'))
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                Log In →
                            </a>
                        @endif

                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline">
                                Create Account
                            </a>
                        @endif
                    @endauth

                </div>

                
                <div class="features">
                    <span>✓ Patient Appointments</span>
                    <span>✓ Community Healthcare</span>
                    <span>✓ Secure Accounts</span>
                </div>

            </div>

            <!-- Right Panel -->
            <div class="hero-visual">
                <div class="medical-icon">+</div>

                <div class="visual-title">
                    Caring for Our Community
                </div>

                <div class="visual-text">
                    Your health, our priority.
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer>
        © {{ date('Y') }} Barangay Health Management System.
        All Rights Reserved.
    </footer>

</body>
</html>


