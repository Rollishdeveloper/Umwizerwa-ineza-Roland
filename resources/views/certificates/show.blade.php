@extends('layouts.app')

@section('title', 'Certificate')

@section('content')
<div class="fade-in">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <!-- Certificate Design -->
                    <div class="border border-4 border-primary rounded p-5 text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);">
                        <div class="mb-4">
                            <i class="bi bi-award-fill text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h1 class="display-6 fw-bold text-primary mb-2">Certificate of Completion</h1>
                        <div class="mb-2">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='3' viewBox='0 0 120 3'%3E%3Cpath d='M0,1.5 L120,1.5' stroke='%23667eea' stroke-width='2' stroke-dasharray='5,5'/%3E%3C/svg%3E" style="width: 120px;">
                        </div>
                        <p class="text-muted mb-4">This is to certify that</p>
                        <h2 class="display-5 fw-bold mb-3" style="font-family: 'Georgia', serif;">{{ $certificate->student->name }}</h2>
                        <p class="text-muted mb-4">has successfully completed the course</p>
                        <h4 class="fw-bold mb-4">{{ $certificate->course->title }}</h4>
                        <div class="mb-4">
                            <span class="badge bg-primary fs-6 p-2 px-3">{{ $certificate->certificate_number }}</span>
                        </div>
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6">
                                <div class="border-top pt-3">
                                    <p class="mb-0 fw-bold">Issue Date</p>
                                    <p class="text-muted">{{ $certificate->issue_date->format('F d, Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border-top pt-3">
                                    <p class="mb-0 fw-bold">Certificate ID</p>
                                    <p class="text-muted">{{ $certificate->certificate_number }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top">
                            <div class="row">
                                <div class="col-6 text-start">
                                    <p class="fw-bold mb-0">E-LMS Platform</p>
                                    <p class="text-muted small">Accredited Learning Provider</p>
                                </div>
                                <div class="col-6 text-end">
                                    <div class="border-0 rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-mortarboard-fill text-white fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <a href="{{ route('certificates.verify') }}?certificate_number={{ $certificate->certificate_number }}" class="btn btn-outline-primary">
                            <i class="bi bi-shield-check"></i> Verify Certificate
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
