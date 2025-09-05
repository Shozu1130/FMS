<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/schedule-assignment.css') }}" rel="stylesheet"> />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4a6cf7;
            --primary-dark: #3a56d5;
            --secondary: #6c757d;
            --light: #f8f9fa;
            --dark: #212529;
            --sidebar-width: 260px;
            --header-height: 70px;
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: #4a5568;
            overflow-x: hidden;
        }
        
        /* Navbar */
        .navbar {
            height: var(--header-height);
            background: linear-gradient(120deg, var(--primary), var(--primary-dark));
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            color: white !important;
        }
        
        .navbar-brand img {
            margin-right: 12px;
            border-radius: 8px;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            padding: 0.4rem 1rem;
            transition: var(--transition);
            font-weight: 500;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            border-right: 1px solid #e3e6f0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            overflow-y: auto;
            z-index: 999;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e3e6f0;
            background: rgba(74, 108, 247, 0.05);
        }
        
        .sidebar-header h4 {
            color: var(--primary);
            font-weight: 600;
            margin: 0;
            font-size: 1.1rem;
        }
        
        .sidebar .nav {
            padding: 1rem 0;
        }
        
        .sidebar .nav-item {
            margin: 0 0.75rem 0.25rem 0.75rem;
        }
        
        .sidebar .nav-link {
            padding: 0.875rem 1rem;
            color: #5a6c7d;
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--primary);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.875rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(74, 108, 247, 0.08);
            color: var(--primary);
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(74, 108, 247, 0.15);
        }
        
        .sidebar .nav-link:hover::before {
            transform: scaleY(1);
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(74, 108, 247, 0.4);
            transform: translateX(2px);
        }
        
        .sidebar .nav-link.active::before {
            transform: scaleY(1);
            background: rgba(255, 255, 255, 0.3);
        }
        
        .sidebar .nav-link.active i {
            color: white;
        }
        
        /* Sidebar Groups */
        .sidebar-group {
            margin: 1.5rem 0.75rem 0.5rem 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid #e3e6f0;
        }
        
        .sidebar-group:first-child {
            margin-top: 0;
            padding-top: 0;
            border-top: none;
        }
        
        .sidebar-group-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #8492a6;
            margin-bottom: 0.5rem;
            padding: 0 1rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            transition: var(--transition);
        }
        
        .content-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .sidebar .nav-item {
                margin: 0 0.5rem 0.25rem 0.5rem;
            }
            
            .sidebar .nav-link {
                padding: 0.75rem 0.875rem;
                font-size: 0.85rem;
            }
            
            .sidebar .nav-link i {
                margin-right: 0.75rem;
                font-size: 1rem;
            }
            
            .sidebar-group-title {
                font-size: 0.7rem;
                padding: 0 0.875rem;
            }
        }
        
        @media (max-width: 576px) {
            .sidebar {
                width: 100vw;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .sidebar .nav-link {
                padding: 0.875rem 1rem;
                font-size: 0.9rem;
            }
        }
        
        /* Toggle Button */
        .sidebar-toggle {
            display: none;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 6px;
            padding: 0.4rem 0.8rem;
            margin-right: 1rem;
        }
        
        @media (max-width: 992px) {
            .sidebar-toggle {
                display: block;
            }
        }
        
        /* Footer */
        .app-footer {
            background: white;
            padding: 1.5rem 2rem;
            margin-left: var(--sidebar-width);
            border-top: 1px solid #edf2f7;
            color: var(--secondary);
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        @media (max-width: 992px) {
            .app-footer {
                margin-left: 0;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .content-card {
            animation: fadeIn 0.5s ease;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="sidebar-toggle" type="button">
                <i class="bi bi-list"></i>
            </button>
            
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/logo300.jpg') }}" width="30" height="30" class="d-inline-block align-top" alt="" />
                Faculty Management System
            </a>
            
            <div class="navbar-nav ms-auto">
                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn logout-btn">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>Faculty Management</h4>
        </div>
        
        <ul class="nav flex-column">
            @if(auth()->user()->isMasterAdmin())
            <!-- Master Admin Section - Only Admin Management -->
            <div class="sidebar-group">
                <div class="sidebar-group-title">Master Admin</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('master_admin.admin_management.*') ? 'active' : '' }}" href="{{ route('master_admin.admin_management.index') }}">
                        <i class="fas fa-user-shield"></i> Admin Management
                    </a>
                </li>
            </div>
            @else
            <!-- Regular Admin Sections - Full Access -->
            <!-- Core Management -->
            <div class="sidebar-group">
                <div class="sidebar-group-title">Core Management</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.faculty.*') ? 'active' : '' }}" href="{{ route('admin.faculty.index') }}">
                        <i class="fas fa-users"></i> Faculty Profiles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.directory.*') ? 'active' : '' }}" href="{{ route('admin.directory.index') }}">
                        <i class="fas fa-address-book"></i> Faculty Directory
                    </a>
                </li>
            </div>

            <!-- Academic Management -->
            <div class="sidebar-group">
                <div class="sidebar-group-title">Academic Management</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.subject-loads.*') ? 'active' : '' }}" href="{{ route('admin.subject-loads.index') }}">
                        <i class="fas fa-chalkboard-teacher"></i> Subject Load Tracker
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.schedule-assignment.*') ? 'active' : '' }}" href="{{ route('admin.schedule-assignment.dashboard') }}">
                        <i class="fas fa-calendar-check"></i> Schedule Assignment
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.teaching_history.*') ? 'active' : '' }}" href="{{ route('admin.teaching_history.index') }}">
                        <i class="fas fa-history"></i> Teaching History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.evaluation.*') ? 'active' : '' }}" href="{{ route('admin.evaluation.faculty_summary') }}">
                        <i class="fas fa-star"></i> Evaluation Summary
                    </a>
                </li>
            </div>

            <!-- HR & Payroll -->
            <div class="sidebar-group">
                <div class="sidebar-group-title">HR & Payroll</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.salary-grades.*') || request()->routeIs('admin.payslips.*') ? 'active' : '' }}" href="{{ route('admin.salary-grades.index') }}">
                        <i class="fas fa-dollar-sign"></i> Salary Grades & Pay
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                        <i class="fas fa-clock"></i> Attendance Monitoring
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}" href="{{ route('admin.leave.index') }}">
                        <i class="fas fa-calendar-times"></i> Leave Requests
                    </a>
                </li>
            </div>

            <!-- Clearance System -->
            <div class="sidebar-group">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.clearance-requests.*') ? 'active' : '' }}" href="{{ route('admin.clearance-requests.index') }}">
                        <i class="fas fa-clipboard-check"></i> Clearance Requests
                    </a>
                </li>
            </div>
            @endif
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-card">
            <h2 class="mb-4">Admin Dashboard</h2>
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="app-footer">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    &copy; 2025 Faculty Management System. All rights reserved.
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="version">v1.2.0</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth < 992 && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>