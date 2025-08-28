<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" />
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
            width: var(--sidebar-width);
            background: white;
            position: fixed;
            top: var(--header-height);
            left: 0;
            height: calc(100vh - var(--header-height));
            padding: 1.5rem 0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            z-index: 900;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid #edf2f7;
            margin-bottom: 1rem;
        }
        
        .sidebar-header h4 {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.2rem;
        }
        
        .nav-item {
            margin: 0.3rem 0.8rem;
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .nav-item:hover {
            background-color: #f0f5ff;
        }
        
        .nav-link {
            color: var(--secondary);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .nav-link.active {
            background: linear-gradient(120deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.25);
        }
        
        .nav-link:hover:not(.active) {
            color: var(--primary);
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
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .navbar-toggler {
                display: block;
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
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.faculty.*') ? 'active' : '' }}" href="{{ route('admin.faculty.index') }}">
                    <i class="bi bi-people-fill"></i> Faculty Profiles
                </a>
            </li>

            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.salary_grades.*') ? 'active' : '' }}" href="{{ route('admin.salary_grades.index') }}">
                    <i class="bi bi-currency-dollar"></i> Salary Grades & Pay
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}" href="{{ route('admin.leave.index') }}">
                    <i class="bi bi-calendar-event"></i> Leave Requests
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.teaching_history.*') ? 'active' : '' }}" href="{{ route('admin.teaching_history.index') }}">
                    <i class="bi bi-book"></i> Teaching History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-bookmarks"></i> Subject Load Tracker
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-calendar-check"></i> Schedule Assignment
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.clearance.*') ? 'active' : '' }}" href="{{ route('admin.clearance.index') }}">
                    <i class="bi bi-shield-lock"></i> Clearance System
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.evaluation.*') ? 'active' : '' }}" href="{{ route('admin.evaluation.index') }}">
                    <i class="bi bi-file-earmark-text"></i> Evaluation Summary
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.directory.*') ? 'active' : '' }}" href="{{ route('admin.directory.index') }}">
                    <i class="bi bi-directory"></i> Faculty Directory
                </a>
            </li>
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
                    &copy; 2023 Faculty Management System. All rights reserved.
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="version">v1.2.0</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
