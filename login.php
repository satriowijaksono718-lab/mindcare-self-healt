<?php
ob_start();
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if already logged in
if (isLoggedIn()) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit();
}

$error = getError();
$success = getSuccess();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi!";
    } elseif (!validateEmail($email)) {
        $error = "Format email tidak valid!";
    } else {
        // Check database
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                setSuccess("Login berhasil! Selamat datang " . $user['name']);
                header("Location: " . BASE_URL . "dashboard.php");
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Email tidak ditemukan!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mind Care | Self Help Mental Health Support</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Main Gradient Background */
        body {
            background: linear-gradient(135deg, #f5f0ff 0%, #e8dff5 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-decoration {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            opacity: 0.08;
            filter: blur(40px);
        }

        .bubble-1 { 
            width: 300px; 
            height: 300px; 
            background: rgba(255, 255, 255, 0.3); 
            top: -50px; 
            left: -50px; 
            animation: float 15s ease-in-out infinite;
        }

        .bubble-2 { 
            width: 200px; 
            height: 200px; 
            background: rgba(160, 223, 232, 0.4); 
            top: 50%; 
            right: -100px; 
            animation: float 20s ease-in-out infinite;
            animation-delay: -5s;
        }

        .bubble-3 { 
            width: 250px; 
            height: 250px; 
            background: rgba(255, 255, 255, 0.2); 
            bottom: -100px; 
            left: 10%; 
            animation: float 18s ease-in-out infinite;
            animation-delay: -10s;
        }

        .bubble-4 {
            width: 150px;
            height: 150px;
            background: rgba(160, 223, 232, 0.3);
            top: 20%;
            right: 10%;
            animation: float 22s ease-in-out infinite;
            animation-delay: -3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            25% { transform: translateY(30px) translateX(20px); }
            50% { transform: translateY(60px) translateX(-20px); }
            75% { transform: translateY(30px) translateX(20px); }
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(139, 92, 246, 0.15);
            padding: clamp(0.8rem, 2vw, 1.2rem) clamp(1rem, 3vw, 2rem);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.1);
        }

        .logo-brand {
            display: flex;
            align-items: center;
            gap: clamp(8px, 2vw, 12px);
            color: #1a2332;
            font-weight: 800;
            font-size: clamp(18px, 4vw, 24px);
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .logo-brand:hover {
            transform: scale(1.08) translateY(-2px);
            color: #8b5cf6;
            text-shadow: 0 0 20px rgba(139, 92, 246, 0.3);
        }

        .logo-icon {
            width: clamp(35px, 8vw, 45px);
            height: clamp(35px, 8vw, 45px);
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(18px, 4vw, 24px);
            color: white;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
            animation: pulse-glow 2s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); }
            50% { box-shadow: 0 8px 35px rgba(102, 126, 234, 0.6); }
        }

        /* Main Content */
        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(80px, 15vw, 120px) clamp(15px, 3vw, 25px) clamp(30px, 5vw, 40px);
            position: relative;
            z-index: 10;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 25px;
            padding: clamp(30px, 5vw, 50px) clamp(20px, 5vw, 40px);
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.2);
            animation: slideInUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 25px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .back-button:hover {
            color: #7c3aed;
            transform: translateX(-5px);
        }

        .form-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .form-icon {
            font-size: clamp(50px, 12vw, 60px);
            color: #8b5cf6;
            margin-bottom: clamp(12px, 3vw, 15px);
            display: block;
            animation: fadeInDown 0.6s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-title {
            font-size: clamp(28px, 8vw, 36px);
            font-weight: 900;
            color: #1a2332;
            margin-bottom: clamp(6px, 1vw, 8px);
            letter-spacing: -0.5px;
            animation: fadeInDown 0.6s ease 0.1s both;
        }

        .form-subtitle {
            font-size: clamp(13px, 3vw, 15px);
            color: #666;
            font-weight: 500;
            animation: fadeInUp 0.6s ease 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: clamp(18px, 4vw, 22px);
            animation: fadeIn 0.6s ease forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.3s; }
        .form-group:nth-child(2) { animation-delay: 0.4s; }
        .form-group:nth-child(3) { animation-delay: 0.5s; }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        label {
            display: block;
            margin-bottom: clamp(8px, 2vw, 10px);
            color: #1a2332;
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            letter-spacing: 0.3px;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-custom i {
            position: absolute;
            left: clamp(12px, 2vw, 16px);
            color: #8b5cf6;
            font-size: clamp(14px, 3vw, 16px);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: clamp(12px, 2vw, 14px) clamp(12px, 2vw, 16px) clamp(12px, 2vw, 14px) clamp(40px, 8vw, 45px);
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: clamp(13px, 2.5vw, 14px);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15);
            background: rgba(139, 92, 246, 0.03);
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #b0b0b0;
        }

        .password-toggle {
            position: absolute;
            right: clamp(12px, 2vw, 16px);
            cursor: pointer;
            color: #8b5cf6;
            background: none;
            border: none;
            font-size: clamp(14px, 3vw, 16px);
            transition: color 0.3s ease;
            padding: 8px;
            margin-right: -8px;
        }

        .password-toggle:hover {
            color: #7c3aed;
        }

        .input-error {
            border-color: #ff6b6b !important;
        }

        .input-success {
            border-color: #51cf66 !important;
        }

        .error-message {
            display: none;
            color: #ff6b6b;
            font-size: clamp(11px, 2vw, 12px);
            margin-top: 6px;
            animation: slideDown 0.3s ease;
        }

        .error-message.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .remember-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: clamp(20px, 4vw, 28px);
            font-size: clamp(12px, 2.5vw, 14px);
            animation: fadeIn 0.6s ease 0.6s both;
            flex-wrap: wrap;
            gap: clamp(10px, 2vw, 15px);
        }

        .remember-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
            font-weight: 500;
            color: #2d3e50;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .remember-group label:hover {
            color: #8b5cf6;
        }

        .remember-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #8b5cf6;
        }

        .forgot-password {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .forgot-password:hover {
            color: #7c3aed;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: clamp(13px, 2vw, 16px);
            background: linear-gradient(135deg, #a0dfe8 0%, #00d4ff 100%);
            border: none;
            border-radius: 12px;
            font-size: clamp(14px, 2.5vw, 16px);
            font-weight: 700;
            color: #1a2332;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-bottom: clamp(15px, 3vw, 20px);
            letter-spacing: 0.5px;
            box-shadow: 0 10px 30px rgba(160, 223, 232, 0.35);
            animation: fadeIn 0.6s ease 0.7s both;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #00d4ff 0%, #a0dfe8 100%);
            z-index: -1;
            transition: left 0.4s ease;
        }

        .btn-login:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(160, 223, 232, 0.5);
        }

        .btn-login:hover::before {
            left: 0;
        }

        .btn-login:active {
            transform: translateY(-2px);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .signup-link {
            text-align: center;
            color: #2d3e50;
            font-size: clamp(12px, 2.5vw, 14px);
            animation: fadeIn 0.6s ease 0.8s both;
        }

        .signup-link a {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .signup-link a:hover {
            color: #7c3aed;
            text-decoration: underline;
        }

        /* Alert Messages */
        .alert {
            padding: clamp(12px, 2vw, 14px) clamp(14px, 3vw, 16px);
            border-radius: 12px;
            margin-bottom: clamp(20px, 4vw, 25px);
            font-size: clamp(12px, 2.5vw, 14px);
            animation: slideDown 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid transparent;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ffe5e5 0%, #fff0f0 100%);
            color: #c92a2a;
            border-color: #ffb3b3;
        }

        .alert-danger i {
            color: #c92a2a;
        }

        .alert-success {
            background: linear-gradient(135deg, #e5f8f0 0%, #f0fff8 100%);
            color: #2d8a5f;
            border-color: #b3e5db;
        }

        .alert-success i {
            color: #2d8a5f;
        }

        /* Responsive Design - Mobile & Tablet Only */
        @media (max-width: 768px) {
            .navbar-custom {
                padding: clamp(0.8rem, 2vw, 1rem) clamp(1rem, 3vw, 1.5rem);
            }

            .logo-brand {
                font-size: clamp(16px, 4vw, 20px);
                gap: clamp(6px, 2vw, 10px);
            }

            .logo-icon {
                width: clamp(32px, 7vw, 40px);
                height: clamp(32px, 7vw, 40px);
                font-size: clamp(16px, 4vw, 20px);
            }

            .login-wrapper {
                padding: clamp(70px, 12vw, 90px) clamp(12px, 3vw, 20px) clamp(20px, 4vw, 30px);
            }

            .login-container {
                padding: clamp(25px, 5vw, 35px) clamp(18px, 4vw, 25px);
                border-radius: 20px;
            }

            .form-title {
                font-size: clamp(24px, 7vw, 28px);
            }

            .form-subtitle {
                font-size: clamp(12px, 2.5vw, 14px);
            }

            .form-icon {
                font-size: clamp(45px, 10vw, 50px);
                margin-bottom: clamp(10px, 2vw, 12px);
            }

            .remember-group {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: clamp(18px, 4vw, 22px);
            }
        }

        @media (max-width: 480px) {
            .navbar-custom {
                padding: clamp(0.7rem, 1.5vw, 0.9rem) clamp(0.8rem, 2vw, 1rem);
            }

            .logo-brand {
                font-size: clamp(14px, 3vw, 18px);
                gap: clamp(6px, 1.5vw, 8px);
            }

            .logo-icon {
                width: clamp(30px, 6vw, 36px);
                height: clamp(30px, 6vw, 36px);
                font-size: clamp(14px, 3vw, 18px);
            }

            .login-wrapper {
                padding: clamp(60px, 10vw, 80px) clamp(10px, 2vw, 15px) clamp(15px, 3vw, 20px);
            }

            .login-container {
                padding: clamp(20px, 4vw, 28px) clamp(15px, 3vw, 20px);
                border-radius: 18px;
            }

            .form-title {
                font-size: clamp(22px, 6vw, 24px);
            }

            .form-subtitle {
                font-size: clamp(11px, 2.2vw, 13px);
            }

            .form-icon {
                font-size: clamp(40px, 9vw, 45px);
                margin-bottom: clamp(10px, 2vw, 12px);
            }

            .back-button {
                margin-bottom: clamp(18px, 3vw, 20px);
                font-size: clamp(12px, 2vw, 13px);
            }

            .form-header {
                margin-bottom: clamp(25px, 4vw, 28px);
            }

            label {
                font-size: clamp(12px, 2.2vw, 13px);
                margin-bottom: clamp(6px, 1.5vw, 8px);
            }

            input[type="email"],
            input[type="password"] {
                padding: clamp(11px, 2vw, 12px) clamp(10px, 2vw, 12px) clamp(11px, 2vw, 12px) clamp(36px, 7vw, 40px);
                font-size: 16px;
            }

            .input-group-custom i {
                left: clamp(10px, 2vw, 12px);
                font-size: clamp(13px, 2.5vw, 14px);
            }

            .password-toggle {
                right: clamp(10px, 2vw, 12px);
                font-size: clamp(13px, 2.5vw, 14px);
            }

            .remember-group {
                font-size: clamp(11px, 2vw, 13px);
                margin-bottom: clamp(16px, 3vw, 20px);
                gap: clamp(8px, 2vw, 10px);
            }

            .form-group {
                margin-bottom: clamp(15px, 3vw, 18px);
            }

            .btn-login {
                padding: clamp(12px, 2vw, 13px);
                font-size: clamp(13px, 2.2vw, 14px);
                margin-bottom: clamp(14px, 2.5vw, 16px);
            }

            .signup-link {
                font-size: clamp(11px, 2vw, 13px);
            }

            .alert {
                font-size: clamp(11px, 2vw, 12px);
                padding: clamp(10px, 2vw, 12px) clamp(12px, 2.5vw, 14px);
                margin-bottom: clamp(15px, 3vw, 20px);
            }
        }

        /* Loading state */
        .btn-login.loading {
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: '';
            display: inline-block;
            width: 14px;
            height: 14px;
            margin-left: 8px;
            border: 2px solid #1a2332;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Background Decoration -->
    <div class="bg-decoration">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
        <div class="bubble bubble-4"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar-custom">
        <a href="index.php" class="logo-brand">
            <div class="logo-icon">
                <i class="fas fa-brain"></i>
            </div>
            <span>Mind Care</span>
        </a>
    </nav>

    <!-- Login Section -->
    <div class="login-wrapper">
        <div class="login-container">
            <a href="index.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>

            <div class="form-header">
                <i class="fas fa-lock form-icon"></i>
                <h1 class="form-title">LOGIN</h1>
                <p class="form-subtitle">Masuk ke akun Mind Care Anda</p>
            </div>

            <!-- Error/Success Messages -->
            <div id="alertContainer">
                <?php
                if ($error) {
                    echo '<div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>' . htmlspecialchars($error) . '</span>
                    </div>';
                }
                if ($success) {
                    echo '<div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>' . htmlspecialchars($success) . '</span>
                    </div>';
                }
                ?>
            </div>

            <form method="POST" action="" id="loginForm" novalidate>
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group-custom">
                        <i class="fas fa-envelope"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="nama@example.com" 
                            required
                        >
                    </div>
                    <div class="error-message" id="emailError"></div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Masukkan password Anda" 
                            required
                        >
                        <button type="button" class="password-toggle" id="togglePassword"></button>
                    </div>
                    <div class="error-message" id="passwordError"></div>
                </div>

                <!-- Remember & Forgot -->
                <div class="remember-group">
                    <label>
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="forgot-password">Lupa password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                    LOGIN
                </button>
            </form>

            <!-- Sign Up Link -->
            <div class="signup-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>

    <!-- Form Validation & Interactions -->
    <script>
        const loginForm = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('togglePassword');
        const loginBtn = document.getElementById('loginBtn');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');

        // Validation functions
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function showError(input, errorElement, message) {
            input.classList.add('input-error');
            input.classList.remove('input-success');
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }

        function clearError(input, errorElement) {
            input.classList.remove('input-error');
            errorElement.textContent = '';
            errorElement.classList.remove('show');
        }

        function showSuccess(input) {
            input.classList.add('input-success');
            input.classList.remove('input-error');
        }

        // Password toggle visibility
        togglePasswordBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Email validation on input
        emailInput.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                showError(this, emailError, 'Email harus diisi');
            } else if (!validateEmail(this.value)) {
                showError(this, emailError, 'Format email tidak valid');
            } else {
                clearError(this, emailError);
                showSuccess(this);
            }
        });

        emailInput.addEventListener('input', function() {
            if (this.classList.contains('input-error') && this.value.trim() !== '') {
                if (validateEmail(this.value)) {
                    clearError(this, emailError);
                    showSuccess(this);
                }
            }
        });

        // Password validation on input
        passwordInput.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                showError(this, passwordError, 'Password harus diisi');
            } else if (this.value.length < 6) {
                showError(this, passwordError, 'Password minimal 6 karakter');
            } else {
                clearError(this, passwordError);
                showSuccess(this);
            }
        });

        passwordInput.addEventListener('input', function() {
            if (this.classList.contains('input-error') && this.value.length >= 6) {
                clearError(this, passwordError);
                showSuccess(this);
            }
        });

        // Form submission
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;

            // Validate email
            if (emailInput.value.trim() === '') {
                showError(emailInput, emailError, 'Email harus diisi');
                isValid = false;
            } else if (!validateEmail(emailInput.value)) {
                showError(emailInput, emailError, 'Format email tidak valid');
                isValid = false;
            } else {
                clearError(emailInput, emailError);
            }

            // Validate password
            if (passwordInput.value.trim() === '') {
                showError(passwordInput, passwordError, 'Password harus diisi');
                isValid = false;
            } else if (passwordInput.value.length < 6) {
                showError(passwordInput, passwordError, 'Password minimal 6 karakter');
                isValid = false;
            } else {
                clearError(passwordInput, passwordError);
            }

            if (!isValid) {
                e.preventDefault();
            } else {
                // Add loading state
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>LOGGING IN...';
            }
        });

        // Clear validation on form reset
        loginForm.addEventListener('reset', function() {
            emailInput.classList.remove('input-error', 'input-success');
            passwordInput.classList.remove('input-error', 'input-success');
            emailError.textContent = '';
            emailError.classList.remove('show');
            passwordError.textContent = '';
            passwordError.classList.remove('show');
        });

        // Auto-hide alert messages after 5 seconds
        const alertContainer = document.getElementById('alertContainer');
        if (alertContainer.children.length > 0) {
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    alert.style.animation = 'slideDown 0.3s ease reverse';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        }
    </script>
</body>
</html>
