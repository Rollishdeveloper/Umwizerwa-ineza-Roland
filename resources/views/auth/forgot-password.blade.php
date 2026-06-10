@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="fade-in-up">
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="text-center mb-4">
            <div class="display-6 mb-3">
                <i class="bi bi-shield-exclamation" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
            </div>
            <h5 class="text-white fw-bold mb-1">Forgot Password?</h5>
            <p class="text-white-50" style="font-size: 0.85rem;">
                No worries. Enter your email and we'll send you a reset link.
            </p>
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group-icon">
                <i class="bi bi-envelope-fill"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" 
                       class="form-control @error('email') is-invalid @enderror" 
                       placeholder="you@example.com" required autofocus>
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-auth mb-3">
            <i class="bi bi-send-fill me-2"></i> Send Reset Link
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">
                <i class="bi bi-arrow-left me-1"></i> Back to Sign In
            </a>
        </div>
    </form>
</div>
@endsection
