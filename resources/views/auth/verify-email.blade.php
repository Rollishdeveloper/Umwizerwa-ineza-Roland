@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
<div class="fade-in-up">
    <div class="text-center mb-4">
        <div class="display-6 mb-3">
            <i class="bi bi-envelope-check-fill" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
        </div>
        <h5 class="text-white fw-bold mb-1">Verify Your Email</h5>
        <p class="text-white-50" style="font-size: 0.85rem;">
            Thanks for signing up! Before getting started, could you verify your email address by clicking the link we just emailed to you?
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle me-1"></i> A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-grow-1">
            @csrf
            <button type="submit" class="btn-auth">
                <i class="bi bi-send-fill me-2"></i> Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-auth" 
                    style="background: transparent; width: auto; padding: 0.75rem 1rem;">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</div>
@endsection
