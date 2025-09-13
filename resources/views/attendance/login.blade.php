<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Monitoring - Faculty Management System</title>
    <link rel="icon" href="{{ asset('images/logo300.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
       body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .main-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            width: 100%;
        }
        .login-form-container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            padding: 2rem;
            order: 1;
        }
        .login-form-wrapper {
            width: 100%;
            max-width: 380px;
        }
        .branding-container {
            display: none;
        }
        .btn-custom-login {
            background-color: #28a745;
            border: none;
            color: white;
            padding: 0.75rem;
            width: 100%;
            font-size: 1rem;
            border-radius: 2rem;
            transition: background-color 0.3s;
            font-weight: 700;
        }
        .btn-custom-login:hover {
            background-color: #218838;
        }
        .form-control {
            border-radius: 0.5rem;
            width: 100%;
            box-sizing: border-box;
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
        .invalid-feedback {
            font-size: .875em;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
        .fw-bold {
             font-weight: 700 !important;
        }
        .password-wrapper {
            position: relative;
            width: 100%;
        }
        .password-wrapper .form-control {
            padding-right: 2.5rem;
        }
        #togglePassword {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            font-size: 1.1rem;
            z-index: 10;
            background: none;
            border: none;
            padding: 0;
        }
        .attendance-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }

        @media (min-width: 992px) {
            .main-container {
                flex-direction: row;
            }
            .login-form-container {
                width: 50%;
                justify-content: flex-end;
                padding-right: 4rem;
                order: 1;
            }
            .branding-container {
                width: 50%;
                color: white;
                display: flex;
                align-items: flex-start;
                justify-content: center;
                flex-direction: column;
                text-align: left;
                position: relative;
                background-image: url("{{ asset('images/branding.png') }}");
                background-size: cover;
                background-position: center;
                order: 2;
                padding-left: 4rem;
            }
            .branding-container h1 {
                font-size: 3.5rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
            }
            .branding-container p {
                margin-top: 0.5rem;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="login-form-container">
            <div class="login-form-wrapper">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo300.jpg') }}" alt="Logo" style="width: 200px;">
                    <div class="attendance-badge">
                        <i class="bi bi-clock"></i> Attendance Monitoring
                    </div>
                    <h1 class="mt-3 fw-bold">Professor Login</h1>
                    <p class="text-muted">Log in to record your attendance</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        @if(is_array(session('error')))
                            <ul class="mb-0">
                                @foreach(session('error') as $key => $error)
                                    @if(is_array($error))
                                        @foreach($error as $subError)
                                            <li>{{ $subError }}</li>
                                        @endforeach
                                    @else
                                        <li>{{ $error }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            {{ session('error') }}
                        @endif
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @php
                    $sessionKeys = array_keys(session()->all());
                    $messageKeys = array_filter($sessionKeys, function($key) {
                        return strpos($key, 'errors') !== false || 
                               strpos($key, 'message') !== false ||
                               strpos($key, 'warning') !== false ||
                               strpos($key, 'info') !== false ||
                               strpos($key, 'success') !== false;
                    });
                @endphp

                @foreach($messageKeys as $key)
                    @if(session($key) && is_array(session($key)))
                        <div class="alert alert-{{ strpos($key, 'error') !== false ? 'danger' : (strpos($key, 'success') !== false ? 'success' : (strpos($key, 'warning') !== false ? 'warning' : 'info')) }} mb-4">
                            <ul class="mb-0">
                                @foreach(session($key) as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach

                <form method="POST" action="{{ route('attendance.login') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            <i class="bi bi-eye-slash" id="togglePassword"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-custom-login w-100 py-2">
                        <i class="bi bi-clock"></i> Login to Attendance
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-muted">
                        <i class="bi bi-arrow-left"></i> Back to Main Login
                    </a>
                </div>
            </div>
        </div>
        <div class="branding-container">
            <h1>Faculty Attendance<br>Monitoring</h1>
            <p>Track your daily attendance with photo verification</p>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
        });
    </script>
</body>
</html>
