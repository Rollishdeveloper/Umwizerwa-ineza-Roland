<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'LMS')) - Learning Management System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            position: relative;
            overflow-x: hidden;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        /* Animated background circles */
        .auth-container::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(102,126,234,0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: floatBg 15s ease infinite;
        }
        .auth-container::after {
            content: '';
            position: absolute;
            bottom: -15%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(118,75,162,0.12) 0%, transparent 70%);
            border-radius: 50%;
            animation: floatBg 20s ease infinite reverse;
        }

        @keyframes floatBg {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            padding: 2.5rem;
            position: relative;
            z-index: 1;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .auth-card-wide {
            max-width: 520px;
        }

        .auth-card .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-card .logo i {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .auth-card .logo h4 {
            color: #fff;
            font-weight: 800;
            margin-top: 0.5rem;
        }
        .auth-card .logo h4 span {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .auth-card .logo p {
            color: rgba(255,255,255,0.5);
            font-size: 0.85rem;
            margin-bottom: 0;
        }

        .auth-card .form-label {
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
        }

        .auth-card .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 0.75rem;
            color: #fff;
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .auth-card .form-control:focus {
            background: rgba(255,255,255,0.1);
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
            color: #fff;
        }
        .auth-card .form-control::placeholder {
            color: rgba(255,255,255,0.35);
        }
        .auth-card .form-control.is-invalid {
            border-color: #dc3545;
            background: rgba(220,53,69,0.1);
        }
        .auth-card .invalid-feedback {
            color: #ff6b6b;
            font-size: 0.8rem;
        }

        .auth-card .form-check-label {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
        }
        .auth-card .form-check-input {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
        }
        .auth-card .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .btn-auth {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102,126,234,0.4);
            color: #fff;
        }
        .btn-auth:active {
            transform: translateY(0);
        }

        .auth-card .auth-link {
            color: rgba(255,255,255,0.5);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
        }
        .auth-card .auth-link:hover {
            color: #667eea;
        }

        .auth-card .divider {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.3);
            font-size: 0.8rem;
            margin: 1.5rem 0;
        }
        .auth-card .divider::before,
        .auth-card .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.1);
        }
        .auth-card .divider::before { margin-right: 1rem; }
        .auth-card .divider::after { margin-left: 1rem; }

        /* Credentials card */
        .credentials-card {
            background: rgba(255, 215, 0, 0.08);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            margin-top: 1.5rem;
        }
        .credentials-card h6 {
            color: #ffc107;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75rem;
        }
        .credentials-card .cred-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.35rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 0.8rem;
        }
        .credentials-card .cred-item:last-child {
            border-bottom: none;
        }
        .credentials-card .cred-role {
            color: rgba(255,255,255,0.6);
        }
        .credentials-card .cred-email {
            color: #fff;
            font-weight: 500;
        }
        .credentials-card .cred-pass {
            color: #667eea;
            font-weight: 600;
        }
        .credentials-card .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.5rem;
        }

        /* Dark mode support */
        [data-bs-theme="dark"] .auth-card {
            background: rgba(0,0,0,0.3);
        }

        /* Alert styling */
        .auth-card .alert {
            border-radius: 0.75rem;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
        }

        /* Input groups with icons */
        .input-group-icon {
            position: relative;
        }
        .input-group-icon .form-control {
            padding-left: 2.5rem;
        }
        .input-group-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            z-index: 10;
        }

        /* Fade in animation */
        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        /* Mobile */
        @media (max-width: 576px) {
            .auth-card {
                padding: 1.5rem;
                border-radius: 1rem;
            }
            .auth-container {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-container">
        <div class="auth-card @yield('card-width', '') fade-in-up">
            <div class="logo">
                <i class="bi bi-mortarboard-fill"></i>
                <h4>E<span>LMS</span></h4>
                <p>Learning Management System</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

    

            @yield('content')

            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="auth-link">
                    <i class="bi bi-arrow-left me-1"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
