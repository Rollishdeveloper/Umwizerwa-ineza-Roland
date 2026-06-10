@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-person-circle me-2 text-primary"></i>My Profile</h4>
            <p class="text-muted mb-0">Manage your account information and preferences</p>
        </div>
        <div>
            <span class="badge bg-{{ auth()->user()->isAdmin() ? 'danger' : (auth()->user()->isInstructor() ? 'primary' : 'success') }} fs-6 px-3 py-2">
                <i class="bi bi-shield-fill-check me-1"></i> {{ ucfirst(auth()->user()->role) }}
            </span>
        </div>
    </div>

    @if(session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> Profile updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> Password updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Column: Profile Photo & Summary --}}
        <div class="col-lg-4">
            <div class="card stat-card text-center mb-4">
                <div class="card-body">

                    <div class="position-relative d-inline-block mb-3">
                        <div class="rounded-circle overflow-hidden border border-3 border-primary d-flex align-items-center justify-content-center mx-auto"
                             style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea20, #764ba220);">
                            @php
                                $photoUrl = null;
                                if (auth()->user()->profile_photo) {
                                    $photoUrl = Storage::url(auth()->user()->profile_photo);
                                } elseif (auth()->user()->isStudent() && auth()->user()->student && auth()->user()->student->profile_photo) {
                                    $photoUrl = Storage::url(auth()->user()->student->profile_photo);
                                } elseif (auth()->user()->isInstructor() && auth()->user()->instructor && auth()->user()->instructor->profile_photo) {
                                    $photoUrl = Storage::url(auth()->user()->instructor->profile_photo);
                                }
                            @endphp
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="Profile" class="w-100 h-100" style="object-fit: cover;" id="profilePreview">
                            @else
                                <span class="display-5 fw-bold text-primary" id="profileInitial">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <label for="profile_photo" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle p-1"
                               style="width: 32px; height: 32px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>

                    <h5 class="fw-bold mb-0">{{ auth()->user()->name }}</h5>
                    <p class="text-muted small">{{ auth()->user()->email }}</p>

                    @if(auth()->user()->isStudent() && auth()->user()->student)
                        <div class="d-flex justify-content-center gap-3 mt-2">
                            <div>
                                <small class="text-muted d-block">Level</small>
                                <span class="fw-bold">{{ auth()->user()->student->level ?? 1 }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Points</small>
                                <span class="fw-bold text-warning">{{ number_format(auth()->user()->student->points ?? 0) }}</span>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user()->isInstructor() && auth()->user()->instructor)
                        <div class="mt-2">
                            <small class="text-muted d-block">Specialization</small>
                            <span class="fw-medium">{{ auth()->user()->instructor->specialization ?? 'Not set' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Account Info Card --}}
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-info"></i>Account Info</h6>
                    <div class="mb-2">
                        <small class="text-muted d-block">Member Since</small>
                        <span>{{ auth()->user()->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Email Verified</small>
                        @if(auth()->user()->email_verified_at)
                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> {{ auth()->user()->email_verified_at->format('M d, Y') }}</span>
                        @else
                            <span class="text-warning"><i class="bi bi-exclamation-circle"></i> Not verified</span>
                        @endif
                    </div>
                    <div>
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-{{ auth()->user()->status === 'active' ? 'success' : 'warning' }}">
                            {{ ucfirst(auth()->user()->status ?? 'active') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Edit Forms --}}
        <div class="col-lg-8">
            {{-- Personal Information --}}
            <div class="card stat-card mb-4">
                <div class="card-header bg-transparent border-0 d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square text-primary fs-5"></i>
                    <h6 class="fw-bold mb-0">Personal Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        {{-- Hidden photo input inside main form --}}
                        <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/*">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium small">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium small">Username</label>
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                       value="{{ old('username', auth()->user()->username ?? '') }}">
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium small">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                                    <small class="text-warning">
                                        <i class="bi bi-exclamation-circle"></i> Email not verified.
                                        <a href="{{ route('verification.send') }}" class="text-decoration-underline"
                                           onclick="event.preventDefault(); document.getElementById('send-verification').submit();">
                                            Resend verification
                                        </a>
                                    </small>
                                @endif
                            </div>

                            {{-- Student-specific fields --}}
                            @if(auth()->user()->isStudent() && auth()->user()->student)
                                @php $student = auth()->user()->student; @endphp
                                <div class="col-md-6">
                                    <label class="form-label fw-medium small">Phone Number</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $student->phone ?? '') }}">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium small">Gender</label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $student->gender ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $student->gender ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium small">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                                           value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}">
                                    @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-medium small">Address</label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                              rows="2">{{ old('address', $student->address ?? '') }}</textarea>
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endif

                            {{-- Instructor-specific fields --}}
                            @if(auth()->user()->isInstructor() && auth()->user()->instructor)
                                @php $instructor = auth()->user()->instructor; @endphp
                                <div class="col-md-6">
                                    <label class="form-label fw-medium small">Specialization</label>
                                    <input type="text" name="specialization" class="form-control @error('specialization') is-invalid @enderror"
                                           value="{{ old('specialization', $instructor->specialization ?? '') }}">
                                    @error('specialization') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small">Biography</label>
                                    <textarea name="biography" class="form-control @error('biography') is-invalid @enderror"
                                              rows="3" placeholder="Tell us about yourself...">{{ old('biography', $instructor->biography ?? '') }}</textarea>
                                    @error('biography') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card stat-card mb-4">
                <div class="card-header bg-transparent border-0 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock text-warning fs-5"></i>
                    <h6 class="fw-bold mb-0">Change Password</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">Current Password</label>
                                <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required>
                                @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">New Password</label>
                                <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required>
                                @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium small">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-shield-check me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Danger Zone: Delete Account --}}
            <div class="card border border-danger border-opacity-25">
                <div class="card-header bg-transparent border-0 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                    <h6 class="fw-bold mb-0 text-danger">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">Delete Your Account</h6>
                            <p class="small text-muted mb-0">Once deleted, all data will be permanently removed. This action cannot be undone.</p>
                        </div>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash3 me-1"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="fw-bold"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Account Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-body">
                    <p>This will permanently delete your account and all associated data including:</p>
                    <ul class="small">
                        <li>Your profile and personal information</li>
                        @if(auth()->user()->isStudent())
                            <li>Enrollments, progress, and certificates</li>
                            <li>Quiz results and achievements</li>
                        @endif
                        @if(auth()->user()->isInstructor())
                            <li>Courses you have created</li>
                        @endif
                    </ul>
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Enter your password to confirm</label>
                        <input type="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" required placeholder="Your password">
                        @error('password', 'userDeletion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i> Permanently Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-none">@csrf</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile photo preview on selection (no auto-submit)
    const photoInput = document.getElementById('profile_photo');
    const photoLabel = document.querySelector('label[for="profile_photo"]');
    if (photoInput && photoLabel) {
        photoLabel.addEventListener('click', function(e) {
            photoInput.click();
        });
        photoInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const preview = document.getElementById('profilePreview');
                    const initial = document.getElementById('profileInitial');
                    const container = preview || initial?.parentElement;
                    if (preview) {
                        preview.src = ev.target.result;
                    } else if (initial) {
                        initial.style.display = 'none';
                        const img = document.createElement('img');
                        img.id = 'profilePreview';
                        img.src = ev.target.result;
                        img.alt = 'Profile';
                        img.className = 'w-100 h-100';
                        img.style.objectFit = 'cover';
                        container?.appendChild(img);
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Auto-hide alerts
    document.querySelectorAll('.alert-dismissible').forEach(function(el) {
        setTimeout(function() {
            el.classList.remove('show');
            setTimeout(function() { el.remove(); }, 300);
        }, 5000);
    });
});
</script>
@endpush
