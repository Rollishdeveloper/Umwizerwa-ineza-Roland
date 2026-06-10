@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')
<div class="fade-in-up">
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="text-center mb-4">
            <h5 class="text-white fw-bold mb-1">Welcome Back</h5>
            <p class="text-white-50" style="font-size: 0.85rem;">Sign in to access your dashboard</p>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group-icon">
                <i class="bi bi-envelope-fill"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" 
                       class="form-control @error('email') is-invalid @enderror" 
                       placeholder="you@example.com" required autofocus autocomplete="username">
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link" style="font-size: 0.8rem;">
                        Forgot?
                    </a>
                @endif
            </div>
            <div class="input-group-icon">
                <i class="bi bi-lock-fill"></i>
                <input id="password" type="password" name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Enter your password" required autocomplete="current-password">
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                <label class="form-check-label" for="remember_me">
                    Keep me signed in
                </label>
            </div>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-auth mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
        </button>

        <!-- Register link -->
        @if (Route::has('register'))
            <div class="text-center">
                <span class="auth-link">Don't have an account?</span>
                <a href="{{ route('register') }}" class="auth-link fw-medium" style="color: #667eea;">
                    Create one <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @endif
    </form>

</div>
@endsection
