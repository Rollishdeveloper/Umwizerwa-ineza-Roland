@extends('layouts.app')
@section('content')
<div class="fade-in">
    <div class="alert alert-info"><i class="bi bi-check2-circle me-2"></i>Redirecting to approval dashboard...</div>
    <meta http-equiv="refresh" content="0;url={{ route('approval.dashboard') }}">
</div>
@endsection
