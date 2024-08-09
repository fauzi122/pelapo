<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login - Pelaporan Migas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Pelaporan Migas - Login" name="description" />
    <meta content="Themesbrand" name="author" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons CSS -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App CSS -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <style>
        body {
            background-image: url('{{ asset('assets/images/bg-4.jpg') }}');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-card h5 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .login-card .form-control {
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .login-card .btn-primary {
            border-radius: 5px;
            padding: 10px;
            width: 100%;
            font-size: 16px;
        }

        .login-card .form-footer {
            text-align: center;
            margin-top: 15px;
        }

        .login-card .form-footer a {
            color: #007bff;
            text-decoration: none;
        }

        .login-card .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <a href="#">
                <img src="{{ asset('assets/images/logo-esdm.png') }}" alt="Logo" height="40">
            </a>
        </div>

        <h5 class="text-center">Login to Your Account</h5>

        <!-- Form Login -->
        <form method="POST" action="{{ url('/login/post-login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="EMAIL_PERUSAHAAN" required placeholder="Enter your email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="PASSWORD" required placeholder="Enter your password">
            </div>
            <button class="btn btn-primary" type="submit">Login</button>
        </form>

        @if (session('statusLogin'))
            <div class="alert alert-danger mt-3" role="alert">
                <strong>{{ session('statusLogin') }}</strong>
            </div>
        @endif

        <div class="form-footer">
            <p>© 2024 Copyright<a href="#"> Aplikasi Pelaporan Migas</a></p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
