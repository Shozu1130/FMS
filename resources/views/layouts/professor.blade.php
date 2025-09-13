    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .btn-purple { background-color: #5044e4; color: white; }
        .btn-purple:hover { background-color: #3f38b7; color: white; }
        .bg-purple { background-color: #5044e4; }
        .sidebar { 
            background-color: #f8f9fa; 
            min-height: calc(100vh - 56px); 
            overflow-y: auto;
            max-height: calc(100vh - 56px);
        }
        .sidebar .nav-link.active { font-weight: 600; color: #5044e4; }
        .sidebar .nav-link { 
            padding: 0.75rem 1rem; 
            margin-bottom: 0.25rem;
            border-radius: 0.375rem;
        }
        .sidebar .nav-link:hover { 
            background-color: #e9ecef; 
            color: #5044e4;
        }
    </style>
    @stack('head')
    @yield('head')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-purple">
        <div class="container">
            <a class="navbar-brand" href="{{ route('professor.dashboard') }}">
                <img src="{{ asset('images/logo300.jpg') }}" width="30" height="30" class="d-inline-block align-top" alt="">
                Professor Portal
            </a>
            <div class="navbar-nav ms-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 sidebar p-3">
                <h5 class="mb-3">My Menu</h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.dashboard') ? 'active' : '' }}" href="{{ route('professor.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.profile.*') ? 'active' : '' }}" href="{{ route('professor.profile.edit') }}">
                            <i class="bi bi-person-circle"></i> My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.leave.*') ? 'active' : '' }}" href="{{ route('professor.leave.index') }}">
                            <i class="bi bi-calendar-check"></i> Leave Applications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.schedule.*') ? 'active' : '' }}" href="{{ route('professor.schedule.index') }}">
                            <i class="bi bi-calendar-week"></i> My Schedule
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.teaching_history.*') ? 'active' : '' }}" href="{{ route('professor.teaching_history.index') }}">
                            <i class="bi bi-journal-text"></i> Teaching History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.attendance.*') ? 'active' : '' }}" href="{{ route('professor.attendance.history') }}">
                            <i class="bi bi-clock-history"></i> My Attendance History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.pay.*') ? 'active' : '' }}" href="{{ route('professor.pay.index') }}">
                            <i class="bi bi-currency-dollar"></i> My Pay
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.clearance-requests.*') ? 'active' : '' }}" href="{{ route('professor.clearance-requests.index') }}">
                            <i class="bi bi-file-earmark-check"></i> My Clearance Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.evaluation.*') ? 'active' : '' }}" href="{{ route('professor.evaluation.index') }}">
                            <i class="bi bi-star"></i> Evaluations
                        </a>
                    </li>
                </ul>
            </aside>
            <main class="col-md-9 p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
