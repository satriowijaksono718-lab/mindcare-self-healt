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
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = "Semua kolom harus diisi!";
    } elseif (strlen($name) < 3) {
        $error = "Nama harus minimal 3 karakter!";
    } elseif (!validateEmail($email)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password harus minimal 6 karakter!";
    } elseif ($password !== $password_confirm) {
        $error = "Password tidak cocok!";
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Insert new user
            $hashed_password = hashPassword($password);
            $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                setSuccess("Akun berhasil dibuat! Silakan login.");
                header("Location: " . BASE_URL . "login.php");
                exit();
            } else {
                $error = "Gagal membuat akun. Silakan coba lagi.";
            }
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
    <title>Daftar Akun - Mind Care | Self Help Mental Health Support</title>
    
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
        .register-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(80px, 15vw, 120px) clamp(15px, 3vw, 25px) clamp(30px, 5vw, 40px);
            position: relative;
            z-index: 10;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 25px;
            padding: clamp(30px, 5vw, 50px) clamp(20px, 5vw, 40px);
            width: 100%;
            max-width: 500px;
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
        .form-group:nth-child(4) { animation-delay: 0.6s; }

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

        input[type="text"],
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

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15);
            background: rgba(139, 92, 246, 0.03);
        }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #b0b0b0;
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
            font-size: 12px;
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

        /* Password Strength */
        .password-strength {
            margin-top: clamp(8px, 2vw, 10px);
            font-size: clamp(11px, 2vw, 12px);
            color: #666;
        }

        .strength-bar {
            height: 5px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
            margin: clamp(5px, 1vw, 6px) 0;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 3px;
        }

        .strength-text {
            font-size: clamp(10px, 2vw, 12px);
            font-weight: 600;
            margin-top: 4px;
        }

        .btn-register {
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

        .btn-register::before {
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

        .btn-register:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(160, 223, 232, 0.5);
        }

        .btn-register:hover::before {
            left: 0;
        }

        .btn-register:active {
            transform: translateY(-2px);
        }

        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .login-link {
            text-align: center;
            color: #2d3e50;
            font-size: 14px;
            animation: fadeIn 0.6s ease 0.8s both;
        }

        .login-link a {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: #7c3aed;
            text-decoration: underline;
        }

        /* Alert Messages */
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
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

        .login-link {
            text-align: center;
            color: #2d3e50;
            font-size: clamp(12px, 2.5vw, 14px);
            animation: fadeIn 0.6s ease 0.8s both;
        }

        .login-link a {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
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

            .register-wrapper {
                padding: clamp(70px, 12vw, 90px) clamp(12px, 3vw, 20px) clamp(20px, 4vw, 30px);
            }

            .register-container {
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

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                padding: clamp(12px, 2vw, 13px) clamp(12px, 2vw, 14px) clamp(12px, 2vw, 13px) clamp(36px, 7vw, 40px);
                font-size: 16px;
            }

            .btn-register {
                padding: clamp(12px, 2vw, 14px);
                font-size: clamp(14px, 2.2vw, 15px);
            }

            .form-group {
                margin-bottom: clamp(16px, 3vw, 18px);
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

            .register-wrapper {
                padding: clamp(60px, 10vw, 80px) clamp(10px, 2vw, 15px) clamp(15px, 3vw, 20px);
            }

            .register-container {
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

            input[type="text"],
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

            .form-group {
                margin-bottom: clamp(15px, 3vw, 16px);
            }

            .btn-register {
                padding: clamp(12px, 2vw, 13px);
                font-size: clamp(13px, 2.2vw, 14px);
                margin-bottom: clamp(14px, 2.5vw, 16px);
            }

            .login-link {
                font-size: clamp(11px, 2vw, 13px);
            }

            .alert {
                font-size: clamp(11px, 2vw, 12px);
                padding: clamp(10px, 2vw, 12px) clamp(12px, 2.5vw, 14px);
                margin-bottom: clamp(15px, 3vw, 20px);
            }

            .password-strength {
                margin-top: clamp(6px, 1.5vw, 8px);
                font-size: clamp(10px, 2vw, 11px);
            }

            .strength-bar {
                margin: clamp(4px, 1vw, 5px) 0;
                height: 4px;
            }
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

    <!-- Register Section -->
    <div class="register-wrapper">
        <div class="register-container">
            <a href="index.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>

            <div class="form-header">
                <i class="fas fa-user-plus form-icon"></i>
                <h1 class="form-title">DAFTAR AKUN</h1>
                <p class="form-subtitle">Buat akun Mind Care baru Anda</p>
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

            <form method="POST" action="" id="registerForm" novalidate>
                <!-- Full Name Field -->
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-group-custom">
                        <i class="fas fa-user"></i>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            placeholder="Masukkan nama lengkap Anda" 
                            required
                        >
                    </div>
                    <div class="error-message" id="nameError"></div>
                </div>

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
                            placeholder="Minimal 6 karakter" 
                            required
                        >
                        <button type="button" class="password-toggle" id="togglePassword"></button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <div class="strength-text" id="strengthText"></div>
                    </div>
                    <div class="error-message" id="passwordError"></div>
                </div>

                <!-- Confirm Password Field -->
                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password</label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            placeholder="Ulangi password Anda" 
                            required
                        >
                        <button type="button" class="password-toggle" id="togglePasswordConfirm"></button>
                    </div>
                    <div class="error-message" id="passwordConfirmError"></div>
                </div>

                <!-- Register Button -->
                <button type="submit" class="btn-register" id="registerBtn">
                    <i class="fas fa-user-plus" style="margin-right: 8px;"></i>
                    DAFTAR AKUN
                </button>
            </form>

            <!-- Login Link -->
            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
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
        const registerForm = document.getElementById('registerForm');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');
        const togglePasswordBtn = document.getElementById('togglePassword');
        const togglePasswordConfirmBtn = document.getElementById('togglePasswordConfirm');
        const registerBtn = document.getElementById('registerBtn');
        
        const nameError = document.getElementById('nameError');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        const passwordConfirmError = document.getElementById('passwordConfirmError');
        
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        // Validation functions
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            return strength;
        }

        function updatePasswordStrength(strength) {
            const widths = [0, 20, 40, 60, 80, 100];
            const colors = ['#e0e0e0', '#ff6b6b', '#ffa940', '#faad14', '#52c41a', '#13c2c2'];
            const texts = ['', 'Lemah', 'Sedang', 'Bagus', 'Kuat', 'Sangat Kuat'];

            strengthFill.style.width = widths[strength] + '%';
            strengthFill.style.backgroundColor = colors[strength];
            strengthText.textContent = texts[strength];
            strengthText.style.color = colors[strength];
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

        // Name validation on blur
        nameInput.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                showError(this, nameError, 'Nama lengkap harus diisi');
            } else if (this.value.trim().length < 3) {
                showError(this, nameError, 'Nama minimal 3 karakter');
            } else {
                clearError(this, nameError);
                showSuccess(this);
            }
        });

        nameInput.addEventListener('input', function() {
            if (this.classList.contains('input-error') && this.value.trim().length >= 3) {
                clearError(this, nameError);
                showSuccess(this);
            }
        });

        // Email validation on blur
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
        passwordInput.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            updatePasswordStrength(strength);

            if (this.value.trim() === '') {
                showError(this, passwordError, 'Password harus diisi');
            } else if (this.value.length < 6) {
                showError(this, passwordError, 'Password minimal 6 karakter');
            } else {
                clearError(this, passwordError);
                showSuccess(this);
            }

            // Check if passwords match
            if (passwordConfirmInput.value && passwordConfirmInput.classList.contains('input-error')) {
                if (this.value === passwordConfirmInput.value) {
                    clearError(passwordConfirmInput, passwordConfirmError);
                    showSuccess(passwordConfirmInput);
                }
            }
        });

        // Confirm password validation on input
        passwordConfirmInput.addEventListener('input', function() {
            if (this.value === '') {
                showError(this, passwordConfirmError, 'Konfirmasi password harus diisi');
            } else if (this.value !== passwordInput.value) {
                showError(this, passwordConfirmError, 'Password tidak cocok');
            } else {
                clearError(this, passwordConfirmError);
                showSuccess(this);
            }
        });

        passwordConfirmInput.addEventListener('blur', function() {
            if (this.value === '') {
                showError(this, passwordConfirmError, 'Konfirmasi password harus diisi');
            } else if (this.value !== passwordInput.value) {
                showError(this, passwordConfirmError, 'Password tidak cocok');
            } else {
                clearError(this, passwordConfirmError);
                showSuccess(this);
            }
        });

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

        togglePasswordConfirmBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i');
            if (passwordConfirmInput.type === 'password') {
                passwordConfirmInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordConfirmInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form submission
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;

            // Validate name
            if (nameInput.value.trim() === '') {
                showError(nameInput, nameError, 'Nama lengkap harus diisi');
                isValid = false;
            } else if (nameInput.value.trim().length < 3) {
                showError(nameInput, nameError, 'Nama minimal 3 karakter');
                isValid = false;
            } else {
                clearError(nameInput, nameError);
            }

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

            // Validate confirm password
            if (passwordConfirmInput.value.trim() === '') {
                showError(passwordConfirmInput, passwordConfirmError, 'Konfirmasi password harus diisi');
                isValid = false;
            } else if (passwordConfirmInput.value !== passwordInput.value) {
                showError(passwordConfirmInput, passwordConfirmError, 'Password tidak cocok');
                isValid = false;
            } else {
                clearError(passwordConfirmInput, passwordConfirmError);
            }

            if (!isValid) {
                e.preventDefault();
            } else {
                // Add loading state
                registerBtn.classList.add('loading');
                registerBtn.disabled = true;
                registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>REGISTERING...';
            }
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
