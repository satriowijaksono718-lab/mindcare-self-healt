<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mind Care - Self Help | Mental Health Support</title>
    
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
            width: clamp(200px, 30vw, 350px); 
            height: clamp(200px, 30vw, 350px); 
            background: rgba(255, 255, 255, 0.3); 
            top: -50px; 
            left: -50px; 
            animation: float 15s ease-in-out infinite;
        }

        .bubble-2 { 
            width: clamp(150px, 20vw, 250px); 
            height: clamp(150px, 20vw, 250px); 
            background: rgba(160, 223, 232, 0.4); 
            top: 50%; 
            right: -100px; 
            animation: float 20s ease-in-out infinite;
            animation-delay: -5s;
        }

        .bubble-3 { 
            width: clamp(180px, 25vw, 300px); 
            height: clamp(180px, 25vw, 300px); 
            background: rgba(255, 255, 255, 0.2); 
            bottom: -100px; 
            left: 10%; 
            animation: float 18s ease-in-out infinite;
            animation-delay: -10s;
        }

        .bubble-4 {
            width: clamp(100px, 15vw, 200px);
            height: clamp(100px, 15vw, 200px);
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
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(139, 92, 246, 0.15);
            padding: clamp(0.8rem, 2vw, 1.5rem) clamp(1rem, 3vw, 2rem);
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

        @keyframes heartbeat {
            0% { transform: scale(1); }
            25% { transform: scale(1.2); }
            50% { transform: scale(1); }
            75% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Hero Section */
        .hero-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: clamp(80px, 10vw, 120px);
            padding-bottom: clamp(30px, 5vw, 50px);
            position: relative;
            z-index: 10;
            min-height: 100vh;
        }

        .hero-content {
            text-align: center;
            color: white;
            position: relative;
            z-index: 10;
            width: 100%;
            padding: clamp(15px, 5vw, 30px);
        }

        .hero-icon {
            font-size: clamp(60px, 15vw, 120px);
            margin-bottom: clamp(15px, 3vw, 30px);
            display: block;
            animation: heartbeat 2s cubic-bezier(0.36, 0, 0.66, 1) infinite;
            filter: drop-shadow(0 0 20px rgba(160, 223, 232, 0.4));
            color: #ff0000;
        }

        .main-title {
            font-size: clamp(36px, 10vw, 80px);
            font-weight: 900;
            color: #1a2332;
            letter-spacing: clamp(-0.5px, -1vw, -1px);
            margin-bottom: clamp(10px, 2vw, 20px);
            text-shadow: 0 4px 15px rgba(139, 92, 246, 0.1);
            animation: slideInDown 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
            line-height: 1.1;
        }

        .subtitle {
            font-size: clamp(28px, 8vw, 56px);
            font-weight: 700;
            background: linear-gradient(135deg, #a0dfe8 0%, #00d4ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: clamp(12px, 2vw, 25px);
            animation: slideInUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation-delay: 0.2s;
            animation-fill-mode: both;
            line-height: 1.1;
        }

        .tagline {
            font-size: clamp(14px, 3vw, 22px);
            color: #2d3e50;
            margin-bottom: clamp(25px, 5vw, 60px);
            font-weight: 500;
            letter-spacing: 0.5px;
            animation: fadeIn 0.8s ease;
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Button Styles */
        .btn-container {
            display: flex;
            flex-direction: row;
            gap: clamp(12px, 3vw, 25px);
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: clamp(35px, 8vw, 70px);
            width: 100%;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #a0dfe8 0%, #00d4ff 100%);
            color: #1a2332;
            border: none;
            padding: clamp(12px, 2vw, 18px) clamp(30px, 5vw, 50px);
            font-size: clamp(13px, 2vw, 18px);
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: clamp(8px, 2vw, 12px);
            min-width: clamp(140px, 30vw, 220px);
            justify-content: center;
            box-shadow: 0 10px 30px rgba(160, 223, 232, 0.35);
            position: relative;
            overflow: hidden;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }

        .btn-primary-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(160, 223, 232, 0.5);
            color: #1a2332;
        }

        @media (max-width: 768px) {
            .btn-container {
                gap: 12px;
                margin-bottom: 40px;
            }
            .btn-primary-custom {
                min-width: 160px;
            }
        }

        @media (max-width: 480px) {
            .btn-container {
                flex-direction: column;
                gap: 12px;
                margin-bottom: 30px;
                padding: 0 15px;
            }
            .btn-primary-custom {
                width: 100%;
                min-width: unset;
            }
        }

        @media (max-width: 375px) {
            .btn-container {
                gap: 10px;
                margin-bottom: 25px;
                padding: 0 10px;
            }
            .btn-primary-custom {
                padding: 12px 24px;
                font-size: 13px;
            }
        }

        .btn-secondary-custom {
            background: rgba(255, 255, 255, 0.12);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.35);
            padding: 14px 45px;
            font-size: 17px;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 200px;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .btn-secondary-custom:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.6);
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(255, 255, 255, 0.15);
            color: white;
        }

        /* Features Section */
        .features-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(150px, 25vw, 230px), 1fr));
            gap: clamp(15px, 3vw, 25px);
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            z-index: 10;
            padding: clamp(15px, 5vw, 30px);
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(139, 92, 246, 0.2);
            padding: clamp(20px, 5vw, 35px);
            border-radius: 20px;
            text-align: center;
            color: #1a2332;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.6);
            border-color: rgba(139, 92, 246, 0.4);
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(139, 92, 246, 0.15);
        }

        .feature-icon {
            font-size: clamp(35px, 8vw, 50px);
            margin-bottom: clamp(10px, 2vw, 18px);
            display: block;
            transition: all 0.4s ease;
            filter: drop-shadow(0 0 10px rgba(160, 223, 232, 0.3));
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.15) rotate(10deg);
            filter: drop-shadow(0 0 15px rgba(160, 223, 232, 0.6));
        }

        .feature-title {
            font-size: clamp(13px, 2.5vw, 18px);
            font-weight: 700;
            margin-bottom: clamp(6px, 1vw, 10px);
        }

        .feature-desc {
            font-size: clamp(11px, 2vw, 15px);
            opacity: 0.7;
            line-height: 1.5;
            transition: opacity 0.4s ease;
            color: #2d3e50;
        }

        .feature-card:hover .feature-desc {
            opacity: 1;
        }

        @media (max-width: 1024px) {
            .features-section {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .features-section {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .features-section {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 12px;
            }
        }

        @media (max-width: 375px) {
            .features-section {
                gap: 8px;
                padding: 10px;
            }
            .feature-card {
                padding: 15px 12px;
            }
        }

        /* Footer */
        .footer-custom {
            position: fixed;
            bottom: clamp(15px, 3vw, 35px);
            left: clamp(15px, 3vw, 35px);
            color: #1a2332;
            z-index: 100;
            animation: slideInLeft 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation-delay: 0.6s;
            animation-fill-mode: both;
        }

        .author-info {
            font-size: clamp(10px, 2vw, 15px);
            font-weight: 600;
            margin-bottom: 5px;
            color: #8b5cf6;
            transition: all 0.3s ease;
        }

        .footer-custom:hover .author-info {
            color: #a78bfa;
            text-shadow: 0 0 10px rgba(139, 92, 246, 0.3);
        }

        .author-label {
            font-size: clamp(9px, 1.5vw, 13px);
            opacity: 0.7;
        }

        /* Footer Right - Social Links */
        .footer-right {
            position: fixed;
            bottom: clamp(15px, 3vw, 35px);
            right: clamp(15px, 3vw, 35px);
            display: flex;
            gap: clamp(8px, 2vw, 15px);
            z-index: 100;
            animation: slideInRight 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation-delay: 0.8s;
            animation-fill-mode: both;
        }

        .social-icon {
            width: clamp(38px, 8vw, 55px);
            height: clamp(38px, 8vw, 55px);
            background: rgba(139, 92, 246, 0.15);
            border: 2px solid rgba(139, 92, 246, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b5cf6;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: clamp(14px, 3vw, 20px);
            backdrop-filter: blur(10px);
            -webkit-tap-highlight-color: transparent;
        }

        .social-icon:hover {
            background: rgba(139, 92, 246, 0.25);
            border-color: rgba(139, 92, 246, 0.5);
            transform: translateY(-8px) scale(1.1);
            color: #a78bfa;
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.2);
        }

        @media (max-width: 768px) {
            .footer-custom {
                bottom: 20px;
                left: 20px;
            }
            .author-info {
                font-size: 12px;
            }
            .author-label {
                font-size: 11px;
            }
            .footer-right {
                bottom: 20px;
                right: 20px;
                gap: 10px;
            }
            .social-icon {
                width: 45px;
                height: 45px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .footer-custom {
                bottom: 15px;
                left: 15px;
            }
            .author-info {
                font-size: 11px;
            }
            .author-label {
                font-size: 10px;
            }
            .footer-right {
                bottom: 15px;
                right: 15px;
                gap: 8px;
            }
            .social-icon {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }
        }

        @media (max-width: 375px) {
            .footer-custom {
                bottom: 10px;
                left: 10px;
            }
            .footer-right {
                bottom: 10px;
                right: 10px;
                gap: 6px;
            }
            .social-icon {
                width: 36px;
                height: 36px;
                font-size: 13px;
                border-width: 1px;
            }
        }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .main-title {
                font-size: 56px;
            }

            .subtitle {
                font-size: 40px;
            }

            .tagline {
                font-size: 18px;
            }

            .btn-container {
                gap: 15px;
            }

            .btn-primary-custom,
            .btn-secondary-custom {
                min-width: 180px;
                font-size: 16px;
                padding: 14px 35px;
            }

            .features-section {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .feature-card {
                padding: 20px 15px;
            }

            .feature-icon {
                font-size: 35px;
            }
        }

        @media (max-width: 768px) {
            .navbar-custom {
                padding: 1rem 1.5rem;
            }

            .logo-brand {
                font-size: 20px;
                gap: 8px;
            }

            .logo-icon {
                width: 38px;
                height: 38px;
                font-size: 18px;
            }

            .hero-section {
                padding-top: 90px;
                padding-bottom: 30px;
            }

            .hero-icon {
                font-size: 80px;
                margin-bottom: 20px;
            }

            .main-title {
                font-size: 44px;
            }

            .subtitle {
                font-size: 32px;
            }

            .tagline {
                font-size: 16px;
                margin-bottom: 35px;
            }

            .btn-container {
                flex-direction: column;
                gap: 12px;
                margin-bottom: 40px;
            }

            .btn-primary-custom,
            .btn-secondary-custom {
                min-width: 200px;
                font-size: 15px;
                padding: 13px 30px;
            }

            .features-section {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 0 10px;
            }

            .feature-card {
                padding: 15px 12px;
                border-radius: 15px;
            }

            .feature-icon {
                font-size: 32px;
                margin-bottom: 10px;
            }

            .feature-title {
                font-size: 14px;
                margin-bottom: 6px;
            }

            .feature-desc {
                font-size: 12px;
            }

            .footer-custom,
            .footer-right {
                font-size: 12px;
                bottom: 15px;
            }

            .footer-custom {
                left: 15px;
            }

            .footer-right {
                right: 15px;
                gap: 10px;
            }

            .social-icon {
                width: 44px;
                height: 44px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .main-title {
                font-size: 36px;
            }

            .subtitle {
                font-size: 28px;
            }

            .tagline {
                font-size: 14px;
            }

            .btn-container {
                gap: 10px;
                margin-bottom: 30px;
            }

            .btn-primary-custom,
            .btn-secondary-custom {
                min-width: 160px;
                font-size: 14px;
                padding: 12px 25px;
            }

            .features-section {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .feature-card {
                padding: 15px 12px;
            }

            .footer-custom,
            .footer-right {
                bottom: 10px;
                font-size: 11px;
            }

            .footer-custom {
                left: 10px;
            }

            .footer-right {
                right: 10px;
                gap: 8px;
            }

            .social-icon {
                width: 40px;
                height: 40px;
                font-size: 14px;
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
        <a href="#" class="logo-brand">
            <div class="logo-icon">
                <i class="fas fa-brain"></i>
            </div>
            <span>Mind Care</span>
        </a>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content" style="width: 100%; padding: 0 20px;">
            <i class="fas fa-heart hero-icon"></i>
            
            <div class="main-title">MIND CARE</div>
            <div class="subtitle">SELF-HELP</div>
            <div class="tagline">Your Mental Health Assistant</div>

            <!-- Buttons -->
            <div class="btn-container">
                <a href="login.php" class="btn-primary-custom">
                    <i class="fas fa-sign-in-alt"></i>
                    LOGIN
                </a>
                <a href="register.php" class="btn-primary-custom">
                    <i class="fas fa-user-plus"></i>
                    DAFTAR AKUN
                </a>
            </div>

            <!-- Features -->
            <div class="features-section">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="0">
                    <span class="feature-icon"><i class="fas fa-chart-line"></i></span>
                    <div class="feature-title">Mood Tracking</div>
                    <div class="feature-desc">Monitor your emotional wellbeing daily</div>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <span class="feature-icon"><i class="fas fa-book"></i></span>
                    <div class="feature-title">Journal</div>
                    <div class="feature-desc">Express yourself freely and safely</div>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <span class="feature-icon"><i class="fas fa-spa"></i></span>
                    <div class="feature-title">Relaxation</div>
                    <div class="feature-desc">De-stress with guided exercises</div>
                </div>
                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <span class="feature-icon"><i class="fas fa-user-md"></i></span>
                    <div class="feature-title">Consultation</div>
                    <div class="feature-desc">Talk to professional counselors</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Left -->
    <div class="footer-custom">
        <div class="author-info">Lorna Alvarado</div>
        <div class="author-label">Presented by</div>
    </div>

    <!-- Footer Right - Social Links -->
    <div class="footer-right">
        <a href="#" class="social-icon" title="Facebook" onclick="event.preventDefault();">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a href="#" class="social-icon" title="Twitter" onclick="event.preventDefault();">
            <i class="fab fa-twitter"></i>
        </a>
        <a href="#" class="social-icon" title="Instagram" onclick="event.preventDefault();">
            <i class="fab fa-instagram"></i>
        </a>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });

        // Smooth scroll for links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add hover ripple effect to buttons
        document.querySelectorAll('.btn-primary-custom, .btn-secondary-custom').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.5);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                `;

                if (!this.style.position) this.style.position = 'relative';
                if (!this.style.overflow) this.style.overflow = 'hidden';
                
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Add ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Parallax effect on scroll
        window.addEventListener('scroll', () => {
            const bubbles = document.querySelectorAll('.bubble');
            bubbles.forEach((bubble, index) => {
                const scrollY = window.scrollY;
                const speed = 0.5 + (index * 0.1);
                bubble.style.transform = `translateY(${scrollY * speed}px)`;
            });
        });

        // Dynamic year in footer
        const year = new Date().getFullYear();
        console.log(`Mind Care ${year} - Your Mental Health Assistant`);
    </script>
</body>
</html>
