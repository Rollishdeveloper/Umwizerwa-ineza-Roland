@extends('layouts.app')

@section('title', 'Verify Certificate')

@section('content')
<div class="fade-in">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <h4 class="fw-bold mb-4"><i class="bi bi-shield-check"></i> Verify Certificate</h4>
                    
                    <form method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="certificate_number" class="form-control" placeholder="Enter certificate number" value="{{ request('certificate_number') }}">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Verify</button>
                        </div>
                    </form>

                    @if(isset($certificate))
                        <div class="border rounded p-4">
                            <div class="text-success mb-3">
                                <i class="bi bi-check-circle-fill display-6"></i>
                            </div>
                            <h5 class="fw-bold text-success">Valid Certificate</h5>
                            <p class="mb-1"><strong>Student:</strong> {{ $certificate->student->name }}</p>
                            <p class="mb-1"><strong>Course:</strong> {{ $certificate->course->title }}</p>
                            <p class="mb-1"><strong>Issue Date:</strong> {{ $certificate->issue_date->format('F d, Y') }}</p>
                            <p><strong>Certificate #:</strong> <code>{{ $certificate->certificate_number }}</code></p>
                            <a href="{{ route('certificates.show', $certificate) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i> View Certificate
                            </a>
                        </div>
                    @elseif(request('certificate_number'))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> Invalid certificate number. Please check and try again.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
