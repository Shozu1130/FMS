<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Attendance Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #28a745;
            --primary-dark: #1e7e34;
            --secondary: #6c757d;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #212529;
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
            color: white;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            color: white;
        }
        
        /* Main Content */
        .main-content {
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
        
        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            border-bottom: 1px solid #edf2f7;
            background: transparent;
            padding: 1.5rem 1.5rem 1rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: var(--transition);
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        /* Status badges */
        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
        }
        
        /* Tables */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--dark);
            background: var(--light);
        }
        
        /* Modals */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            border-bottom: 1px solid #edf2f7;
            border-radius: 12px 12px 0 0;
        }
        
        /* Camera container */
        .camera-container {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            background: var(--light);
        }
        
        .camera-container video {
            border-radius: 8px;
            max-width: 100%;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }
            
            .content-card {
                padding: 1rem;
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
            <a class="navbar-brand" href="{{ route('attendance.dashboard') }}">
                <i class="bi bi-clock me-2"></i>
                Attendance Monitoring System
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ Auth::guard('faculty')->user()->name ?? 'Faculty' }}
                    </span>
                    
                    <!-- Switch to Professor Portal Button -->
                    <a href="{{ route('attendance.switch_to_professor') }}" class="btn btn-outline-light btn-sm me-2"
                       onclick="return confirm('Switch to Professor Portal? You can always come back to Attendance Monitoring.')">
                        <i class="bi bi-person"></i> Professor Portal
                    </a>
                    
                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('attendance.logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn logout-btn"
                                onclick="return confirm('Are you sure you want to logout?')">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-card">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
