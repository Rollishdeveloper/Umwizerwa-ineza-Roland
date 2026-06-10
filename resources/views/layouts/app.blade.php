<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'LMS')) - Learning Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/premium-design.css') }}">

    <style>
        :root { --sidebar-width: 270px; }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--surface-secondary); min-height: 100vh; }
        [data-bs-theme="dark"] body { background: #0f0f1a; }
        .sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-width); height: 100vh;
            background: linear-gradient(180deg, #0f0f1a 0%, #1a1a2e 100%);
            z-index: 1000; transition: transform 0.3s ease; overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.6); padding: 0.7rem 1.25rem; border-radius: 0.75rem;
            margin: 0.15rem 0.75rem; transition: all 0.3s cubic-bezier(0.4,0,0.2,1); font-size: 0.9rem;
            position: relative;
        }
        .sidebar .nav-link:hover { color: #fff; background: rgba(79,70,229,0.15); transform: translateX(6px); }
        .sidebar .nav-link.active { 
            color: #fff; 
            background: linear-gradient(135deg, rgba(79,70,229,0.3), rgba(124,58,237,0.2));
            box-shadow: 0 4px 15px rgba(79,70,229,0.2);
            border-left: 3px solid #4F46E5;
        }
        .sidebar .nav-link i { margin-right: 0.75rem; font-size: 1.1rem; width: 20px; text-align: center; }
        .sidebar .logo { 
            padding: 1.25rem; 
            background: linear-gradient(135deg, rgba(79,70,229,0.1), transparent);
            margin-bottom: 0.5rem;
        }
        .sidebar .logo h4 { color: #fff; font-weight: 800; margin: 0; }
        .sidebar .logo span { background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; padding: 0; }
        .top-navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 0.75rem 1.5rem; position: sticky; top: 0; z-index: 999;
        }
        [data-bs-theme="dark"] .top-navbar { 
            background: rgba(15,15,26,0.8); 
            border-color: rgba(255,255,255,0.05); 
        }
        .sidebar-toggle { display: none; background: none; border: none; color: var(--text-primary); font-size: 1.5rem; cursor: pointer; }
        [data-bs-theme="dark"] .sidebar-toggle { color: #e0e0e0; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: block; }
        }
        .footer { 
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            border-top: 1px solid var(--border-color);
            padding: 1rem 1.5rem; text-align: center; font-size: 0.85rem; 
            color: var(--text-muted);
        }
        [data-bs-theme="dark"] .footer { 
            background: rgba(15,15,26,0.5);
            border-color: rgba(255,255,255,0.05);
        }
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .slide-in { animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 9999; }
        .badge-pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(79,70,229,0.4); } 70% { box-shadow: 0 0 0 10px rgba(79,70,229,0); } 100% { box-shadow: 0 0 0 0 rgba(79,70,229,0); } }
        .gamification-sidebar-link { position: relative; }
        .gamification-sidebar-link .xp-badge {
            position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%);
            font-size: 0.65rem; padding: 0.15rem 0.4rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="sidebar" id="sidebar">
        <div class="logo d-flex align-items-center">
            <i class="bi bi-mortarboard-fill fs-3 text-primary me-2"></i>
            <h4>E<span>LMS</span></h4>
        </div>
        <div class="mt-3">
            <div class="px-3 mb-2">
                <small class="text-white-50 text-uppercase fw-bold">Main Menu</small>
            </div>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Students
                    </a>
                    <a href="{{ route('admin.instructors.index') }}" class="nav-link {{ request()->routeIs('admin.instructors.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i> Instructors
                    </a>
                    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> All Users
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                    <a href="{{ route('courses.index') }}" class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i> Courses
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Reports
                    </a>
                    <a href="{{ route('admin.database.export') }}" class="nav-link {{ request()->routeIs('admin.database.*') ? 'active' : '' }}">
                        <i class="bi bi-database-gear"></i> Database Tools
                    </a>
                @elseif(auth()->user()->isInstructor())
                    <a href="{{ route('instructor.dashboard') }}" class="nav-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('courses.index') }}" class="nav-link {{ request()->routeIs('courses.index') ? 'active' : '' }}">
                        <i class="bi bi-book"></i> My Courses
                    </a>
                    <a href="{{ route('courses.create') }}" class="nav-link {{ request()->routeIs('courses.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i> Create Course
                    </a>
                    <a href="{{ route('instructor.enrollments') }}" class="nav-link {{ request()->routeIs('instructor.enrollments*') ? 'active' : '' }}">
                        <i class="bi bi-journal-check"></i> Enrollments
                    </a>
                @else
                    <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.*') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('courses.index') }}" class="nav-link {{ request()->routeIs('courses.index') ? 'active' : '' }}">
                        <i class="bi bi-compass"></i> Browse Courses
                    </a>
                    <a href="{{ route('enrollments.index') }}" class="nav-link {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-check"></i> My Enrollments
                    </a>
                    <a href="{{ route('gamification.index') }}" class="nav-link gamification-sidebar-link {{ request()->routeIs('gamification.*') ? 'active' : '' }}">
                        <i class="bi bi-trophy-fill text-warning"></i> Rewards
                        @php
                            $student = auth()->user()->student;
                            if ($student && $student->badges()->where('student_badges.awarded_at', '>=', now()->subDays(7))->exists()) {
                                echo '<span class="badge bg-danger rounded-pill xp-badge badge-pulse">NEW</span>';
                            }
                        @endphp
                    </a>
                    <a href="{{ route('certificates.index') }}" class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
                        <i class="bi bi-award"></i> Certificates
                    </a>
                @endif

                <div class="px-3 mt-4 mb-2">
                    <small class="text-white-50 text-uppercase fw-bold">Account</small>
                </div>
                <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-bell"></i> Notifications
                    @php
                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                            ->where('status', 'unread')->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill ms-2">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person"></i> Profile
                </a>
            @endauth
        </div>
    </nav>

    <div class="main-content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div class="input-group" style="max-width: 300px;">
                    <input type="text" class="form-control form-control-sm" id="globalSearch" 
                           placeholder="Search everywhere..." onkeyup="if(event.key==='Enter') globalSearch()">
                    <button class="btn btn-outline-secondary btn-sm" onclick="globalSearch()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary" onclick="toggleTheme()" id="themeToggle">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>

                @auth
                    <a href="{{ route('notifications.index') }}" class="position-relative text-decoration-none text-dark">
                        <i class="bi bi-bell fs-5"></i>
                        @php
                            $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                ->where('status', 'unread')->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 32px; height: 32px; font-size: 0.85rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted small">{{ ucfirst(auth()->user()->role) }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i> Notifications</a></li>
                            <li><a class="dropdown-item" href="{{ route('gamification.index') }}"><i class="bi bi-trophy me-2 text-warning"></i> Rewards</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-person-plus"></i> Register
                    </a>
                @endauth
            </div>
        </nav>

        <div class="p-4" style="min-height: calc(100vh - 120px);">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} E-LMS. All rights reserved. | Powered by Laravel {{ Illuminate\Foundation\Application::VERSION }}
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    @auth
    <nav class="bottom-nav" id="bottomNav">
        @if(auth()->user()->isStudent())
            @php $homeActive = request()->routeIs('student.*') && !request()->routeIs('enrollments.*') && !request()->routeIs('courses.*') && !request()->routeIs('gamification.*') && !request()->routeIs('profile.*') && !request()->routeIs('certificates.*'); @endphp
            <div class="nav-item">
                <a href="{{ route('student.dashboard') }}" class="nav-link-bottom {{ $homeActive ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i>
                    <span>Home</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('courses.index') }}" class="nav-link-bottom {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                    <i class="bi bi-search"></i>
                    <span>Search</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('enrollments.index') }}" class="nav-link-bottom {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                    <i class="bi bi-collection-play-fill"></i>
                    <span>Courses</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('gamification.index') }}" class="nav-link-bottom {{ request()->routeIs('gamification.*') ? 'active' : '' }}">
                    <i class="bi bi-trophy-fill"></i>
                    <span>Rewards</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('profile.edit') }}" class="nav-link-bottom {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-fill"></i>
                    <span>Profile</span>
                </a>
            </div>
        @elseif(auth()->user()->isInstructor())
            @php $homeActive = request()->routeIs('instructor.*') && !request()->routeIs('instructor.*enrollment*'); @endphp
            <div class="nav-item">
                <a href="{{ route('instructor.dashboard') }}" class="nav-link-bottom {{ $homeActive ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i>
                    <span>Home</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('courses.index') }}" class="nav-link-bottom {{ request()->routeIs('courses.*') && !request()->routeIs('courses.create') ? 'active' : '' }}">
                    <i class="bi bi-book-fill"></i>
                    <span>Courses</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('courses.create') }}" class="nav-link-bottom {{ request()->routeIs('courses.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Create</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('instructor.enrollments') }}" class="nav-link-bottom {{ request()->routeIs('instructor.*enrollment*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Students</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('profile.edit') }}" class="nav-link-bottom {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-fill"></i>
                    <span>Profile</span>
                </a>
            </div>
        @elseif(auth()->user()->isAdmin())
            @php $homeActive = request()->routeIs('admin.*') && !request()->routeIs('admin.students.*') && !request()->routeIs('reports.*'); @endphp
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link-bottom {{ $homeActive ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i>
                    <span>Home</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.students.index') }}" class="nav-link-bottom {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Students</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('courses.index') }}" class="nav-link-bottom {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                    <i class="bi bi-book-fill"></i>
                    <span>Courses</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link-bottom {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Reports</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('profile.edit') }}" class="nav-link-bottom {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-fill"></i>
                    <span>Profile</span>
                </a>
            </div>
        @endif
    </nav>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('show'); }
        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-bs-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            document.getElementById('themeIcon').className = next === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        }
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
        if (savedTheme === 'dark') { document.getElementById('themeIcon').className = 'bi bi-sun-fill'; }
        function globalSearch() {
            const query = document.getElementById('globalSearch').value;
            if (query.length > 0) { window.location.href = "{{ route('courses.index') }}?search=" + encodeURIComponent(query); }
        }
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            setTimeout(() => { alert.classList.remove('show'); setTimeout(() => alert.remove(), 300); }, 5000);
        });
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-bg-' + type + ' border-0 show fade-in';
            toast.setAttribute('role', 'alert');
            toast.innerHTML = '<div class="d-flex"><div class="toast-body">' + message + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
            document.getElementById('toast-container')?.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }
    </script>
    @stack('scripts')
</body>
</html>
