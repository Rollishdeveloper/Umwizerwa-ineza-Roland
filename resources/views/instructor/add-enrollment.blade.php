@extends('layouts.app')

@section('title', 'Add Enrollment')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-person-plus me-2"></i>Add Enrollment</h4>
            <p class="text-muted mb-0">Manually enroll a student in one of your courses</p>
        </div>
        <a href="{{ route('instructor.enrollments') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Enrollments
        </a>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <form method="POST" action="{{ route('instructor.store-enrollment') }}">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Select Course</label>
                            <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">-- Choose a course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->course_id }}" {{ old('course_id') == $course->course_id ? 'selected' : '' }}>
                                        {{ $course->title }} ({{ $course->level }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Select Student</label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">-- Choose a student --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->student_id }}" {{ old('student_id') == $student->student_id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    The student will be enrolled with <strong>Active</strong> status and will receive a notification.
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
