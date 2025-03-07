@extends('layouts.auth')

@section('login')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            background-image: url('/img/background-new.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
        }

        /* Animasi untuk elemen login */
        .login-box {
            width: 400px;
            margin: 7% auto;
            padding: 30px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            animation: fadeIn 1s ease-out;
        }

        .login-box-body {
            text-align: center;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .login-logo img {
            max-width: 100px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-control:focus {
            border-color: #4caf50;
        }

        .form-group .help-block {
            color: red;
            font-size: 12px;
        }

        #togglePassword {
            cursor: pointer;
        }

        /* SVG untuk eye icon */
        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #0056b3;
        }

        .checkbox label {
            font-size: 14px;
        }

        .alert {
            background-color: #f44336;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body style="background-image: url('/img/background-new.jpg'); background-size: cover; background-repeat: no-repeat; background-position: center;">

<div class="login-box">
    <div class="login-logo">
        <a href="{{ url('/') }}">
            <img src="{{ url($setting->path_logo) }}" alt="logo.png">
            <br>
            <br>
            <div style="font-size: 18px;"><b>Sistem Aplikasi COBIT</b></div>
        </a>
    </div>

    <div class="login-box-body">
        <form id="loginForm" action="{{ route('login') }}" method="post" class="form-login">
            @csrf
            <div class="form-group has-feedback @error('email') has-error @enderror">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required value="{{ old('email') }}" autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @error('email')
                    <span class="help-block">{{ $message }}</span>
                @else
                    <span class="help-block with-errors"></span>
                @enderror
            </div>

            <div class="form-group has-feedback @error('password') has-error @enderror" style="position: relative;">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <span class="fa fa-eye eye-icon" id="togglePassword"></span>
                @error('password')
                    <span class="help-block">{{ $message }}</span>
                @else
                    <span class="help-block with-errors"></span>
                @enderror
            </div>

            <div class="form-group mb-3">
                {!! NoCaptcha::renderJs() !!}
                {!! NoCaptcha::display() !!}
                <span id="captcha-error" style="color: red; display: none;">Please complete the CAPTCHA.</span>
            </div>

            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox"> Remember Me
                        </label>
                    </div>
                </div>

                <div class="col-xs-4">
                    <button type="submit" class="btn" id="submit-btn">Sign In</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Captcha Validation
    document.getElementById('submit-btn').addEventListener('click', function(event) {
        // Cek apakah CAPTCHA sudah diisi
        if (!grecaptcha.getResponse()) {
            // Tampilkan pesan error jika CAPTCHA belum diisi
            document.getElementById('captcha-error').style.display = 'block';
            event.preventDefault(); // Mencegah form untuk disubmit
        } else {
            document.getElementById('captcha-error').style.display = 'none';
        }
    });
    
    // Get the password field and the toggle button
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        // Toggle the password field visibility
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;

        // Toggle the Font Awesome eye icon
        togglePassword.classList.toggle('fa-eye-slash', passwordField.type === 'text');
        togglePassword.classList.toggle('fa-eye', passwordField.type === 'password');
    });

    // Form validation
    const form = document.getElementById('loginForm');
    form.addEventListener('submit', function(event) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!email || !password) {
            event.preventDefault();
            alert('Email and Password fields are required.');
        }
    });
</script>

</body>
</html>
@endsection