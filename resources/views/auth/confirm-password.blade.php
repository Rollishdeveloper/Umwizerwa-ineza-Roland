@extends('layouts.guest')

@section('title', 'Confirm Password')

@section('content')
<div class="fade-in-up">
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="text-center mb-4">
            <div class="display-6 mb-3">
                <i class="bi bi-shield-fill-check" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
            </div>
            <h5 class="text-white fw-bold mb-1">Confirm Password</h5>
            <p class="text-white-50" style="font-size: 0.85rem;">
                This is a secure area. Please confirm your password before continuing.
            </p>
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
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

        <!-- Submit -->
        <button type="submit" class="btn-auth mb-3">
            <i class="bi bi-check-lg me-2"></i> Confirm
        </button>
    </form>
</div>
@endsection
