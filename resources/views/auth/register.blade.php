@extends('layouts.guest')

@section('title', 'Create Account')

@section('card-width', 'auth-card-wide')

@section('content')
<div class="fade-in-up">
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="text-center mb-4">
            <h5 class="text-white fw-bold mb-1">Create Account</h5>
            <p class="text-white-50" style="font-size: 0.85rem;">Join our learning community</p>
        </div>

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <div class="input-group-icon">
                <i class="bi bi-person-fill"></i>
                <input id="name" type="text" name="name" value="{{ old('name') }}" 
                       class="form-control @error('name') is-invalid @enderror" 
                       placeholder="John Doe" required autofocus autocomplete="name">
            </div>
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group-icon">
                <i class="bi bi-envelope-fill"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" 
                       class="form-control @error('email') is-invalid @enderror" 
                       placeholder="you@example.com" required autocomplete="username">
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group-icon">
                <i class="bi bi-lock-fill"></i>
                <input id="password" type="password" name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Min. 8 characters" required autocomplete="new-password">
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group-icon">
                <i class="bi bi-shield-lock-fill"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" 
                       class="form-control" placeholder="Repeat your password" required autocomplete="new-password">
            </div>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-auth mb-3">
            <i class="bi bi-person-plus-fill me-2"></i> Create Account
        </button>

        <!-- Login link -->
        <div class="text-center">
            <span class="auth-link">Already have an account?</span>
            <a href="{{ route('login') }}" class="auth-link fw-medium" style="color: #667eea;">
                Sign in <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </form>
</div>
@endsection
