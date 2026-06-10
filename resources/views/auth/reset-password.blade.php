@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="fade-in-up">
    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="text-center mb-4">
            <div class="display-6 mb-3">
                <i class="bi bi-key-fill" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
            </div>
            <h5 class="text-white fw-bold mb-1">Reset Password</h5>
            <p class="text-white-50" style="font-size: 0.85rem;">Choose a new secure password</p>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group-icon">
                <i class="bi bi-envelope-fill"></i>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" 
                       class="form-control @error('email') is-invalid @enderror" 
                       placeholder="you@example.com" required autofocus autocomplete="username">
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
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
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <div class="input-group-icon">
                <i class="bi bi-shield-lock-fill"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" 
                       class="form-control" placeholder="Repeat your password" required autocomplete="new-password">
            </div>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-auth mb-3">
            <i class="bi bi-check-lg me-2"></i> Reset Password
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">
                <i class="bi bi-arrow-left me-1"></i> Back to Sign In
            </a>
        </div>
    </form>
</div>
@endsection
