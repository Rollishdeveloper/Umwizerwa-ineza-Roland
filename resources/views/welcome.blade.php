<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LMS') }} - Online Learning Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/premium-design.css') }}">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            position: relative; overflow: hidden;
        }
        .hero-section::before {
            content: ''; position: absolute; inset: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(79,70,229,0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(124,58,237,0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 80%, rgba(59,130,246,0.1) 0%, transparent 50%);
            animation: floatSlow 15s ease infinite;
        }
        .hero-content { position: relative; z-index: 1; }
        .floating-card {
            position: absolute;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 1rem;
            padding: 1rem;
        }
        .feature-card {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            border-color: rgba(79,70,229,0.3);
            background: rgba(79,70,229,0.05);
            box-shadow: 0 20px 50px rgba(79,70,229,0.1);
        }
        .category-card {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .category-card:hover {
            transform: translateY(-5px);
            background: rgba(79,70,229,0.1);
            border-color: rgba(79,70,229,0.3);
        }
        .section-gradient {
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 100%);
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .testimonial-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .testimonial-card:hover {
            border-color: rgba(79,70,229,0.3);
            box-shadow: 0 10px 30px rgba(79,70,229,0.1);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .courses-scroll {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            padding: 1rem 0;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        .courses-scroll::-webkit-scrollbar { height: 4px; }
        .courses-scroll::-webkit-scrollbar-thumb { background: rgba(79,70,229,0.3); border-radius: 2px; }
        .courses-scroll .course-card-mini {
            min-width: 300px;
            scroll-snap-align: start;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .courses-scroll .course-card-mini:hover {
            transform: translateY(-5px);
            border-color: rgba(79,70,229,0.3);
            box-shadow: 0 15px 40px rgba(79,70,229,0.15);
        }
        .courses-scroll .course-card-mini .card-bg {
            height: 140px;
            background: linear-gradient(135deg, rgba(79,70,229,0.2), rgba(124,58,237,0.2));
            position: relative;
            overflow: hidden;
        }
        .navbar-blur {
            background: rgba(15,15,26,0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
    </style>
</head>
<body>
    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-blur">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="/">
                <i class="bi bi-mortarboard-fill me-2" style="color: #4F46E5;"></i>
                <span style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">E</span>LMS
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categories">Categories</a></li>
                </ul>
                <div class="d-flex gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-premium btn-sm px-4">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-glass btn-sm px-4 text-white">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-premium btn-sm px-4">
                                <i class="bi bi-rocket-takeoff me-1"></i> Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <div class="hero-section d-flex align-items-center">
        <div class="container hero-content">
            <div class="row align-items-center g-5 min-vh-100 py-5">
                <div class="col-lg-7 animate-fade-in-up">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 mb-4 rounded-pill">
                        <i class="bi bi-star-fill me-1" style="color: #F59E0B;"></i> Powered by AI Learning
                    </span>
                    <h1 class="display-3 fw-bold text-white mb-4 lh-1">
                        Learn Without<br>
                        <span class="gradient-text">Limits</span>
                    </h1>
                    <p class="lead text-white-50 mb-4 fs-5" style="max-width: 540px;">
                        Master in-demand skills with expert-led courses. Interactive lessons, 
                        AI-powered quizzes, premium certificates, and a learning journey 
                        that adapts to <span class="text-white fw-medium">you</span>.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-5">
                        <a href="{{ route('courses.index') }}" class="btn btn-premium btn-lg px-5 py-3 fs-6">
                            <i class="bi bi-compass me-2"></i> Explore Courses
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-glass btn-lg px-4 py-3 fs-6 text-white">
                                Start Free <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        @endguest
                    </div>
                    <div class="d-flex flex-wrap gap-4 text-white-50">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-success bg-opacity-10 p-2">
                                <i class="bi bi-people-fill text-success"></i>
                            </div>
                            <span><strong class="text-white">500+</strong> Students</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                <i class="bi bi-book-fill" style="color: #4F46E5;"></i>
                            </div>
                            <span><strong class="text-white">50+</strong> Courses</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2">
                                <i class="bi bi-award-fill" style="color: #F59E0B;"></i>
                            </div>
                            <span><strong class="text-white">Premium</strong> Certificates</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 animate-fade-in-up delay-2">
                    <div class="position-relative" style="min-height: 400px;">
                        <!-- Floating icons -->
                        <div class="floating-card animate-float" style="top: 10%; right: 10%;">
                            <i class="bi bi-play-circle fs-1" style="color: #4F46E5;"></i>
                        </div>
                        <div class="floating-card animate-float" style="bottom: 20%; left: 5%; animation-delay: 1s;">
                            <i class="bi bi-trophy fs-1" style="color: #F59E0B;"></i>
                        </div>
                        <div class="floating-card animate-float" style="top: 50%; right: 0%; animation-delay: 2s;">
                            <i class="bi bi-mortarboard-fill fs-1" style="color: #06B6D4;"></i>
                        </div>
                        <!-- Center hero illustration -->
                        <div class="text-center" style="padding-top: 60px;">
                            <div class="d-inline-block p-5 rounded-4" style="background: linear-gradient(135deg, rgba(79,70,229,0.15), rgba(124,58,237,0.15)); border: 1px solid rgba(255,255,255,0.05);">
                                <i class="bi bi-mortarboard-fill text-white" style="font-size: 5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== STATISTICS ===== -->
    <div class="section-gradient py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6 animate-fade-in-up">
                    <div class="stat-number">500+</div>
                    <p class="text-white-50 mb-0">Active Students</p>
                </div>
                <div class="col-md-3 col-6 animate-fade-in-up delay-1">
                    <div class="stat-number">50+</div>
                    <p class="text-white-50 mb-0">Expert Courses</p>
                </div>
                <div class="col-md-3 col-6 animate-fade-in-up delay-2">
                    <div class="stat-number">95%</div>
                    <p class="text-white-50 mb-0">Success Rate</p>
                </div>
                <div class="col-md-3 col-6 animate-fade-in-up delay-3">
                    <div class="stat-number">10k+</div>
                    <p class="text-white-50 mb-0">Lessons Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FEATURES ===== -->
    <section id="features" class="section-gradient py-5">
        <div class="container py-4">
            <div class="text-center mb-5 animate-fade-in-up">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 mb-3 rounded-pill">
                    Why Choose Us
                </span>
                <h2 class="display-6 fw-bold text-white">A Premium Learning Experience</h2>
                <p class="text-white-50">Everything you need to master new skills</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 animate-fade-in-up">
                    <div class="feature-card text-center h-100">
                        <div class="display-5 mb-3" style="color: #4F46E5;"><i class="bi bi-laptop"></i></div>
                        <h5 class="text-white fw-bold">Interactive Learning</h5>
                        <p class="text-white-50 small mb-0">Video lessons, AI-powered quizzes, and hands-on projects for deep engagement.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fade-in-up delay-1">
                    <div class="feature-card text-center h-100">
                        <div class="display-5 mb-3" style="color: #06B6D4;"><i class="bi bi-graph-up-arrow"></i></div>
                        <h5 class="text-white fw-bold">Track Progress</h5>
                        <p class="text-white-50 small mb-0">XP points, learning streaks, and detailed analytics to monitor your growth.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fade-in-up delay-2">
                    <div class="feature-card text-center h-100">
                        <div class="display-5 mb-3" style="color: #F59E0B;"><i class="bi bi-award"></i></div>
                        <h5 class="text-white fw-bold">Get Certified</h5>
                        <p class="text-white-50 small mb-0">Earn premium, verifiable certificates upon successful course completion.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fade-in-up delay-3">
                    <div class="feature-card text-center h-100">
                        <div class="display-5 mb-3" style="color: #10B981;"><i class="bi bi-robot"></i></div>
                        <h5 class="text-white fw-bold">AI Assistant</h5>
                        <p class="text-white-50 small mb-0">Get instant help from our AI learning assistant 24/7.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fade-in-up delay-4">
                    <div class="feature-card text-center h-100">
                        <div class="display-5 mb-3" style="color: #EC4899;"><i class="bi bi-people"></i></div>
                        <h5 class="text-white fw-bold">Community</h5>
                        <p class="text-white-50 small mb-0">Connect with peers, share achievements, and learn together.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fade-in-up delay-5">
                    <div class="feature-card text-center h-100">
                        <div class="display-5 mb-3" style="color: #8B5CF6;"><i class="bi bi-phone"></i></div>
                        <h5 class="text-white fw-bold">Learn Anywhere</h5>
                        <p class="text-white-50 small mb-0">Fully responsive design that works on any device.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FEATURED COURSES (Netflix-style scroll) ===== -->
    <section id="courses" class="section-gradient py-5">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 animate-fade-in-up">
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 mb-2 rounded-pill">
                        Start Learning
                    </span>
                    <h2 class="display-6 fw-bold text-white">Featured Courses</h2>
                </div>
                <a href="{{ route('courses.index') }}" class="btn btn-glass text-white">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="courses-scroll animate-fade-in-up delay-1">
                @php
                    $featuredCourses = \App\Models\Course::where('status', 'published')
                        ->withCount('enrollments')
                        ->latest()
                        ->take(8)
                        ->get();
                @endphp
                @forelse($featuredCourses as $course)
                    <div class="course-card-mini">
                        <div class="card-bg d-flex align-items-center justify-content-center">
                            <i class="bi bi-play-circle fs-1 text-white-50"></i>
                            <span class="position-absolute bottom-0 start-0 m-2 badge rounded-pill" 
                                  style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); font-size: 0.65rem;">
                                {{ ucfirst($course->level ?? 'all') }}
                            </span>
                        </div>
                        <div class="p-3">
                            <h6 class="text-white fw-bold mb-1" style="font-size: 0.9rem;">{{ Str::limit($course->title, 40) }}</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-white-50">
                                    <i class="bi bi-people me-1"></i>{{ $course->enrollments_count ?? 0 }}
                                </small>
                                <div class="d-flex align-items-center gap-1">
                                    <i class="bi bi-star-fill" style="color: #F59E0B; font-size: 0.7rem;"></i>
                                    <small class="text-white-50">4.8</small>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('courses.show', $course) }}" class="stretched-link"></a>
                    </div>
                @empty
                    <div class="text-center text-white-50 py-5 w-100">
                        <i class="bi bi-book fs-1 d-block mb-2"></i>
                        <p>Courses coming soon...</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ===== CATEGORIES ===== -->
    <section id="categories" class="section-gradient py-5">
        <div class="container py-4">
            <div class="text-center mb-5 animate-fade-in-up">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 mb-3 rounded-pill">
                    Explore
                </span>
                <h2 class="display-6 fw-bold text-white">Browse by Category</h2>
                <p class="text-white-50">Find the perfect course for your goals</p>
            </div>
            <div class="row g-4">
                @php
                    $categories = \App\Models\Category::withCount('courses')->get();
                @endphp
                @forelse($categories as $category)
                    <div class="col-md-4 col-lg-2 col-6 animate-fade-in-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                        <div class="category-card h-100">
                            <div class="display-6 mb-2" style="color: {{ ['#4F46E5', '#06B6D4', '#10B981', '#F59E0B', '#EC4899', '#8B5CF6'][$loop->index % 6] }};">
                                <i class="bi bi-{{ ['code-slash', 'book', 'graph-up', 'camera', 'music-note', 'translate'][$loop->index % 6] }}"></i>
                            </div>
                            <h6 class="text-white fw-bold mb-1">{{ $category->name }}</h6>
                            <small class="text-white-50">{{ $category->courses_count ?? 0 }} courses</small>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-white-50">
                        <p>Categories coming soon...</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ===== TESTIMONIALS ===== -->
    <section class="section-gradient py-5">
        <div class="container py-4">
            <div class="text-center mb-5 animate-fade-in-up">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 mb-3 rounded-pill">
                    Testimonials
                </span>
                <h2 class="display-6 fw-bold text-white">What Our Students Say</h2>
            </div>
            <div class="row g-4">
                @php
                    $testimonials = [
                        ['name' => 'Sarah Johnson', 'role' => 'Web Developer', 'text' => 'The interactive quizzes and AI-powered feedback helped me learn twice as fast compared to traditional courses.', 'rating' => 5],
                        ['name' => 'Mark Rwanda', 'role' => 'Data Scientist', 'text' => 'The course structure with learning roadmaps made it easy to track my progress. Best learning platform I have used.', 'rating' => 5],
                        ['name' => 'Alice Mugisha', 'role' => 'UI/UX Designer', 'text' => 'The premium certificates and project-based learning approach gave me the confidence to apply for my dream job.', 'rating' => 5],
                    ];
                @endphp
                @foreach($testimonials as $index => $t)
                    <div class="col-md-4 animate-fade-in-up" style="animation-delay: {{ $index * 0.15 }}s;">
                        <div class="testimonial-card h-100">
                            <div class="mb-3">
                                @for($i = 0; $i < $t['rating']; $i++)
                                    <i class="bi bi-star-fill" style="color: #F59E0B; font-size: 0.85rem;"></i>
                                @endfor
                            </div>
                            <p class="text-white-50 mb-4" style="font-size: 0.9rem;">"{{ $t['text'] }}"</p>
                            <div class="d-flex align-items-center gap-3 mt-auto">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                     style="width: 40px; height: 40px; background: linear-gradient(135deg, #4F46E5, #7C3AED); font-size: 0.85rem;">
                                    {{ strtoupper(substr($t['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <p class="mb-0 text-white fw-medium small">{{ $t['name'] }}</p>
                                    <small class="text-white-50">{{ $t['role'] }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="section-gradient py-5">
        <div class="container py-4 text-center animate-scale-in">
            <div class="p-5 rounded-4" style="background: linear-gradient(135deg, rgba(79,70,229,0.1), rgba(124,58,237,0.1)); border: 1px solid rgba(255,255,255,0.05);">
                <h2 class="display-6 fw-bold text-white mb-3">Ready to Start Learning?</h2>
                <p class="text-white-50 mb-4 fs-5">Join thousands of students and transform your skills today.</p>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-premium btn-lg px-5 py-3 fs-6">
                        <i class="bi bi-rocket-takeoff me-2"></i> Get Started Free
                    </a>
                @else
                    <a href="{{ route('courses.index') }}" class="btn btn-premium btn-lg px-5 py-3 fs-6">
                        <i class="bi bi-compass me-2"></i> Explore Courses
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="section-gradient py-4 border-top border-white border-opacity-10">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-mortarboard-fill fs-4" style="color: #4F46E5;"></i>
                        <span class="text-white fw-bold fs-5">E<span class="gradient-text">LMS</span></span>
                    </div>
                    <p class="text-white-50 small">Premium online learning platform powered by AI.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-white fw-bold mb-3">Quick Links</h6>
                    <div class="d-flex flex-column gap-2">
                        <a href="#features" class="text-white-50 text-decoration-none small">Features</a>
                        <a href="#courses" class="text-white-50 text-decoration-none small">Courses</a>
                        <a href="#categories" class="text-white-50 text-decoration-none small">Categories</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="text-white fw-bold mb-3">Connect</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white-50"><i class="bi bi-twitter-x fs-5"></i></a>
                        <a href="#" class="text-white-50"><i class="bi bi-linkedin fs-5"></i></a>
                        <a href="#" class="text-white-50"><i class="bi bi-github fs-5"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-white border-opacity-10 my-3">
            <p class="text-white-50 small text-center mb-0">
                &copy; {{ date('Y') }} E-LMS. All rights reserved. Built with Laravel {{ Illuminate\Foundation\Application::VERSION }}
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Intersection Observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.animate-fade-in-up, .animate-scale-in').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
        // Also play on load
        window.addEventListener('load', () => {
            document.querySelectorAll('.animate-fade-in-up, .animate-scale-in').forEach(el => {
                el.style.animationPlayState = 'running';
            });
        });
    </script>
</body>
</html>
