@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<div class="fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Courses</h4>
            <p class="text-muted mb-0">Explore our learning catalog</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->isInstructor())
                <a href="{{ route('courses.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Create Course
                </a>
            @endif
        </div>
    </div>

    <!-- Recommended Courses (for students) -->
    @if(isset($recommendedCourses) && $recommendedCourses->isNotEmpty())
    <div class="card stat-card mb-4 border-warning">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-star-fill text-warning me-2"></i>Recommended For You</h6>
            <div class="row g-3">
                @foreach($recommendedCourses as $rec)
                    <div class="col-md-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold small mb-1">{{ Str::limit($rec->title, 35) }}</h6>
                            <p class="small text-muted mb-1">{{ $rec->instructor->name ?? 'Unknown' }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark" style="font-size:0.65rem;">{{ $rec->category->category_name ?? 'Uncategorized' }}</span>
                                <a href="{{ route('courses.show', $rec) }}" class="btn btn-sm btn-outline-warning">View</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="card stat-card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search courses..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="level" class="form-select">
                        <option value="">Level</option>
                        <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="duration" class="form-select">
                        <option value="">Duration</option>
                        <option value="short" {{ request('duration') === 'short' ? 'selected' : '' }}>Short</option>
                        <option value="medium" {{ request('duration') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="long" {{ request('duration') === 'long' ? 'selected' : '' }}>Long</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="price" class="form-select">
                        <option value="">Price</option>
                        <option value="free" {{ request('price') === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="paid" {{ request('price') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="completion" {{ request('sort') === 'completion' ? 'selected' : '' }}>High Completion</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="status" class="form-select">
                        <option value="">Status</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Course Grid -->
    <div class="row g-4">
        @forelse($courses as $course)
            <div class="col-md-4 col-lg-3">
                <div class="card course-card h-100">
                    <div class="position-relative">
                        @if($course->thumbnail && !str_starts_with($course->thumbnail, 'http'))
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" class="card-img-top" alt="{{ $course->title }}">
                        @elseif($course->thumbnail && str_starts_with($course->thumbnail, 'http'))
                            <img src="{{ $course->thumbnail }}" class="card-img-top" alt="{{ $course->title }}">
                        @else
                            <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                                 style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                <i class="bi bi-book text-white" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <span class="position-absolute top-0 end-0 m-2 badge bg-{{ $course->level === 'beginner' ? 'success' : ($course->level === 'intermediate' ? 'warning' : 'danger') }}">
                            {{ ucfirst($course->level) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">{{ Str::limit($course->title, 40) }}</h6>
                        <p class="small text-muted mb-2">{{ $course->instructor->name ?? 'Unknown' }}</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-light text-dark">{{ $course->category->category_name ?? 'Uncategorized' }}</span>
                            @if($course->price > 0)
                                <span class="fw-bold text-primary">${{ number_format($course->price, 2) }}</span>
                            @else
                                <span class="badge bg-success">Free</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span><i class="bi bi-clock"></i> {{ $course->duration ?? 'N/A' }} min</span>
                            <span><i class="bi bi-people"></i> {{ $course->enrollments_count ?? $course->enrollments()->count() }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary btn-sm w-100">
                            View Course <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-book fs-1 d-block mb-3"></i>
                    <h5>No courses found</h5>
                    <p>Try adjusting your search or filter criteria.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $courses->withQueryString()->links() }}
    </div>
</div>
@endsection
