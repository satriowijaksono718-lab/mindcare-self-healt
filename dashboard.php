<?php
ob_start();
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_initial = strtoupper(substr($user_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mind Care | Mental Health Support</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* ========== RESET & CSS VARIABLES ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #8b5cf6;
            --primary-light: #a78bfa;
            --accent: #00d4ff;
            --accent-light: #a0dfe8;
            --dark: #1a2332;
            --text: #666;
            --light: #999;
            --error: #ff6b6b;
            --success: #51cf66;
            --spacing-xs: clamp(8px, 2vw, 12px);
            --spacing-sm: clamp(12px, 3vw, 16px);
            --spacing-md: clamp(16px, 4vw, 24px);
            --spacing-lg: clamp(24px, 5vw, 32px);
            --radius: clamp(12px, 3vw, 20px);
            --trans: 0.3s ease;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #f5f0ff 0%, #e8dff5 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        /* ========== BACKGROUND DECORATION ========== */
        .bg-decoration {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            opacity: 0.04;
            filter: blur(60px);
        }

        .bubble-1 {
            width: clamp(250px, 30vw, 400px);
            height: clamp(250px, 30vw, 400px);
            background: rgba(255, 255, 255, 0.3);
            top: clamp(-50px, -10%, -100px);
            left: clamp(-50px, -10%, -100px);
            animation: float 15s ease-in-out infinite;
        }

        .bubble-2 {
            width: clamp(180px, 25vw, 300px);
            height: clamp(180px, 25vw, 300px);
            background: rgba(160, 223, 232, 0.4);
            top: 40%;
            right: clamp(-100px, -10%, -150px);
            animation: float 20s ease-in-out infinite;
            animation-delay: -5s;
        }

        .bubble-3 {
            width: clamp(200px, 28vw, 350px);
            height: clamp(200px, 28vw, 350px);
            background: rgba(255, 255, 255, 0.2);
            bottom: clamp(-100px, -15%, -150px);
            left: 10%;
            animation: float 18s ease-in-out infinite;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(clamp(30px, 5vh, 80px)); }
        }

        /* ========== NAVBAR ========== */
        .navbar-custom {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(139, 92, 246, 0.15);
            padding: clamp(0.8rem, 2vw, 1.2rem) clamp(1rem, 3vw, 2rem);
            z-index: 500;
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.1);
        }

        .logo-brand {
            display: flex;
            align-items: center;
            gap: clamp(8px, 2vw, 12px);
            color: var(--dark);
            font-weight: 800;
            font-size: clamp(20px, 5vw, 28px);
            text-decoration: none;
            transition: var(--trans);
        }

        .logo-brand:hover {
            transform: scale(1.05) translateY(-2px);
            color: var(--primary);
        }

        .logo-icon {
            width: clamp(40px, 8vw, 50px);
            height: clamp(40px, 8vw, 50px);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(20px, 5vw, 26px);
            color: white;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
            flex-shrink: 0;
        }

        /* ========== USER PROFILE ========== */
        .user-profile-fixed {
            position: fixed;
            top: clamp(12px, 2vw, 20px);
            right: clamp(12px, 2vw, 30px);
            z-index: 999;
            display: flex;
            align-items: center;
            gap: clamp(10px, 2vw, 15px);
            padding: clamp(8px, 1.5vw, 12px) clamp(12px, 2vw, 18px);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--trans);
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.2);
        }

        .user-profile-fixed:hover {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 40px rgba(139, 92, 246, 0.25);
        }

        .user-avatar-fixed {
            width: clamp(38px, 8vw, 48px);
            height: clamp(38px, 8vw, 48px);
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(16px, 4vw, 20px);
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-info-fixed {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name-fixed {
            color: var(--dark);
            font-weight: 700;
            font-size: clamp(12px, 2.5vw, 14px);
            line-height: 1.2;
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-email-fixed {
            color: var(--light);
            font-size: clamp(10px, 2vw, 12px);
            line-height: 1.2;
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dropdown-arrow {
            color: var(--primary);
            font-size: clamp(10px, 2vw, 12px);
            transition: var(--trans);
        }

        .user-profile-fixed.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-menu-fixed {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: var(--radius);
            padding: clamp(8px, 2vw, 12px) 0;
            min-width: clamp(200px, 80vw, 260px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--trans);
            z-index: 2000;
        }

        .user-profile-fixed.active .dropdown-menu-fixed {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: clamp(12px, 2vw, 16px) clamp(14px, 3vw, 18px);
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
            display: flex;
            align-items: center;
            gap: clamp(10px, 2vw, 12px);
        }

        .dropdown-avatar {
            width: clamp(45px, 8vw, 55px);
            height: clamp(45px, 8vw, 55px);
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(20px, 5vw, 28px);
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }

        .dropdown-info h4 {
            font-size: clamp(13px, 2.5vw, 15px);
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 3px;
        }

        .dropdown-info p {
            font-size: clamp(11px, 2vw, 12px);
            color: var(--light);
            margin: 0;
        }

        .dropdown-item {
            padding: clamp(10px, 2vw, 13px) clamp(14px, 3vw, 18px);
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: clamp(10px, 2vw, 12px);
            transition: var(--trans);
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: inherit;
            font-size: clamp(12px, 2.5vw, 13px);
        }

        .dropdown-item:hover {
            background: rgba(139, 92, 246, 0.1);
            color: var(--primary);
            padding-left: clamp(18px, 5vw, 25px);
        }

        .dropdown-divider {
            height: 1px;
            background: rgba(139, 92, 246, 0.1);
            margin: clamp(6px, 1vw, 8px) 0;
        }

        .dropdown-item.logout {
            color: var(--error);
        }

        .dropdown-item.logout:hover {
            background: rgba(255, 107, 107, 0.1);
            color: #ff5252;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            position: relative;
            z-index: 1;
            flex: 1;
            padding: clamp(100px, 12vw, 140px) var(--spacing-md) clamp(30px, 5vw, 40px);
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* ========== GREETING SECTION ========== */
        .greeting-section {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(139, 92, 246, 0.15);
            border-radius: var(--radius);
            padding: clamp(25px, 5vw, 40px);
            margin-bottom: clamp(30px, 5vw, 40px);
            display: flex;
            align-items: center;
            gap: clamp(20px, 5vw, 40px);
            animation: slideInDown 0.6s ease;
        }

        .greeting-avatar {
            width: clamp(60px, 15vw, 100px);
            height: clamp(60px, 15vw, 100px);
            border-radius: 50%;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(30px, 8vw, 50px);
            flex-shrink: 0;
            box-shadow: 0 8px 25px rgba(251, 191, 36, 0.3);
        }

        .greeting-content {
            flex: 1;
        }

        .greeting-title {
            font-size: clamp(24px, 6vw, 36px);
            font-weight: 900;
            color: var(--dark);
            margin-bottom: clamp(8px, 2vw, 12px);
            letter-spacing: -0.5px;
        }

        .greeting-subtitle {
            font-size: clamp(13px, 3vw, 16px);
            color: var(--text);
            margin: 0;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========== CONSULTATION BANNER ========== */
        .consultation-banner {
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent) 100%);
            border-radius: var(--radius);
            padding: clamp(25px, 5vw, 40px);
            margin-bottom: clamp(30px, 5vw, 40px);
            color: white;
            text-align: center;
            animation: slideInUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
            box-shadow: 0 15px 40px rgba(160, 223, 232, 0.3);
        }

        .consultation-title {
            font-size: clamp(18px, 5vw, 28px);
            font-weight: 900;
            margin: 0;
            letter-spacing: -0.3px;
            line-height: 1.3;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========== SERVICES SECTION ========== */
        .section-title {
            font-size: clamp(22px, 5vw, 28px);
            font-weight: 900;
            color: var(--dark);
            margin-bottom: clamp(20px, 4vw, 25px);
            letter-spacing: -0.5px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(130px, 25vw, 180px), 1fr));
            gap: var(--spacing-md);
            margin-bottom: clamp(30px, 5vw, 40px);
        }

        .service-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: var(--radius);
            padding: clamp(20px, 4vw, 35px) clamp(15px, 3vw, 25px);
            text-align: center;
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        .service-card:nth-child(1) { animation-delay: 0.1s; }
        .service-card:nth-child(2) { animation-delay: 0.2s; }
        .service-card:nth-child(3) { animation-delay: 0.3s; }
        .service-card:nth-child(4) { animation-delay: 0.4s; }
        .service-card:nth-child(5) { animation-delay: 0.5s; }

        .service-card:hover {
            transform: translateY(clamp(-5px, -2vw, -10px));
            box-shadow: 0 20px 50px rgba(139, 92, 246, 0.2);
            border-color: rgba(139, 92, 246, 0.4);
        }

        .service-icon {
            font-size: clamp(40px, 10vw, 60px);
            margin-bottom: clamp(10px, 2vw, 15px);
            display: block;
        }

        .service-name {
            font-size: clamp(12px, 3vw, 15px);
            font-weight: 700;
            color: var(--dark);
            line-height: 1.4;
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

        /* ========== DOCTORS SECTION ========== */
        .doctors-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: var(--radius);
            padding: clamp(30px, 5vw, 40px);
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.15);
            animation: slideInUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
        }

        .doctor-card {
            display: flex;
            align-items: center;
            gap: clamp(15px, 4vw, 25px);
            padding: clamp(20px, 3vw, 25px);
            margin-bottom: clamp(15px, 3vw, 25px);
            background: linear-gradient(135deg, #f5f0ff 0%, #f0ebff 100%);
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--trans);
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .doctor-card:last-child {
            margin-bottom: 0;
        }

        .doctor-card:hover {
            transform: translateX(clamp(3px, 1vw, 5px));
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.15);
            background: linear-gradient(135deg, #f0ebff 0%, #e5deff 100%);
        }

        .doctor-avatar {
            width: clamp(70px, 15vw, 100px);
            height: clamp(70px, 15vw, 100px);
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(35px, 8vw, 50px);
            flex-shrink: 0;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        .doctor-info {
            flex: 1;
            min-width: 0;
        }

        .doctor-name {
            font-size: clamp(15px, 4vw, 18px);
            font-weight: 800;
            color: var(--dark);
            margin-bottom: clamp(6px, 1.5vw, 10px);
            letter-spacing: -0.3px;
        }

        .doctor-rating {
            display: flex;
            align-items: center;
            gap: clamp(8px, 2vw, 10px);
            font-size: clamp(12px, 2.5vw, 14px);
            flex-wrap: wrap;
        }

        .stars {
            font-size: clamp(14px, 3vw, 18px);
            color: #fbbf24;
        }

        .rating-text {
            font-weight: 700;
            color: var(--dark);
        }

        .review-text {
            color: var(--light);
        }

        .doctor-buttons {
            display: flex;
            gap: clamp(8px, 2vw, 12px);
            flex-wrap: wrap;
        }

        .btn-action {
            padding: clamp(8px, 1.5vw, 12px) clamp(14px, 3vw, 20px);
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent) 100%);
            color: white;
            border: none;
            border-radius: clamp(8px, 2vw, 12px);
            cursor: pointer;
            font-weight: 700;
            font-size: clamp(11px, 2.5vw, 13px);
            transition: var(--trans);
            box-shadow: 0 5px 15px rgba(160, 223, 232, 0.3);
            display: flex;
            align-items: center;
            gap: clamp(6px, 1vw, 8px);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(160, 223, 232, 0.4);
        }

        /* ========== MODALS ========== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: clamp(10px, 3vw, 20px);
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        .chat-container {
            background: white;
            border-radius: var(--radius);
            width: 100%;
            max-width: 450px;
            height: clamp(400px, 80vh, 600px);
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideInUp 0.4s ease;
        }

        .chat-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: clamp(15px, 3vw, 20px);
            border-radius: var(--radius) var(--radius) 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(139, 92, 246, 0.2);
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: clamp(10px, 2vw, 12px);
        }

        .chat-avatar {
            width: clamp(40px, 8vw, 50px);
            height: clamp(40px, 8vw, 50px);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(18px, 4vw, 24px);
        }

        .chat-name {
            font-size: clamp(14px, 3vw, 16px);
            font-weight: 700;
            margin-bottom: 3px;
        }

        .online-status {
            font-size: clamp(11px, 2.5vw, 12px);
            opacity: 0.9;
        }

        .close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: clamp(16px, 4vw, 24px);
            cursor: pointer;
            padding: clamp(5px, 1vw, 10px);
            border-radius: 8px;
            transition: var(--trans);
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: clamp(15px, 3vw, 20px);
            background: #f9f7fc;
            display: flex;
            flex-direction: column;
            gap: clamp(10px, 2vw, 12px);
        }

        .message {
            display: flex;
            animation: slideIn 0.3s ease;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 70%;
            padding: clamp(10px, 2vw, 12px) clamp(12px, 2vw, 16px);
            border-radius: clamp(10px, 2vw, 15px);
            word-wrap: break-word;
            font-size: clamp(13px, 2.5vw, 14px);
            line-height: 1.4;
        }

        .message.user .message-bubble {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-radius: 15px 0 15px 15px;
        }

        .message.consultant .message-bubble {
            background: white;
            color: var(--dark);
            border: 1px solid rgba(139, 92, 246, 0.1);
            border-radius: 0 15px 15px 15px;
        }

        .chat-input-area {
            padding: clamp(12px, 3vw, 20px);
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: clamp(8px, 2vw, 10px);
            background: white;
            border-radius: 0 0 var(--radius) var(--radius);
        }

        .chat-input {
            flex: 1;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: clamp(10px, 2vw, 12px) clamp(12px, 2vw, 16px);
            font-size: clamp(12px, 2.5vw, 14px);
            font-family: inherit;
            transition: var(--trans);
        }

        .chat-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            background: rgba(139, 92, 246, 0.02);
        }

        .send-btn {
            padding: clamp(8px, 1.5vw, 12px) clamp(12px, 2vw, 20px);
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: clamp(11px, 2.5vw, 12px);
            transition: var(--trans);
            box-shadow: 0 5px 15px rgba(160, 223, 232, 0.3);
        }

        .send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(160, 223, 232, 0.4);
        }

        /* ========== CALL INTERFACE ========== */
        .call-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: clamp(20px, 5vw, 30px);
            color: white;
        }

        .call-avatar-large {
            width: clamp(100px, 25vw, 150px);
            height: clamp(100px, 25vw, 150px);
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(50px, 12vw, 80px);
            box-shadow: 0 20px 50px rgba(139, 92, 246, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        .call-info {
            text-align: center;
        }

        .call-info h2 {
            font-size: clamp(24px, 6vw, 32px);
            margin-bottom: clamp(8px, 2vw, 12px);
            font-weight: 900;
        }

        .call-status {
            font-size: clamp(14px, 3vw, 16px);
            opacity: 0.9;
        }

        .call-timer {
            font-size: clamp(24px, 6vw, 32px);
            font-weight: 900;
            margin-top: clamp(10px, 2vw, 15px);
            font-family: 'Courier New', monospace;
        }

        .call-controls {
            display: flex;
            gap: clamp(12px, 3vw, 20px);
            position: fixed;
            bottom: clamp(15px, 3vw, 30px);
            left: 50%;
            transform: translateX(-50%);
            background: rgba(80, 80, 80, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: clamp(10px, 2vw, 15px) clamp(15px, 3vw, 25px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            z-index: 2001;
        }

        .call-btn {
            background: none;
            border: none;
            color: white;
            font-size: clamp(18px, 4vw, 24px);
            cursor: pointer;
            padding: clamp(8px, 1.5vw, 10px);
            transition: var(--trans);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .call-end {
            background: #ff3333;
            border-radius: 50%;
            width: clamp(40px, 8vw, 50px);
            height: clamp(40px, 8vw, 50px);
            font-size: clamp(16px, 4vw, 20px);
            box-shadow: 0 5px 15px rgba(255, 51, 51, 0.3);
        }

        .call-end:hover {
            background: #ff1111;
            transform: scale(1.1);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* ========== RESPONSIVE DESIGN ========== */
        @media (max-width: 768px) {
            .main-content {
                padding: clamp(90px, 10vw, 110px) clamp(12px, 2.5vw, 15px) clamp(20px, 3vw, 25px);
            }

            .greeting-section {
                flex-direction: column;
                text-align: center;
            }

            .services-grid {
                grid-template-columns: repeat(auto-fit, minmax(clamp(100px, 22vw, 140px), 1fr));
            }

            .doctor-card {
                flex-direction: column;
                text-align: center;
            }

            .doctor-buttons {
                justify-content: center;
                width: 100%;
            }

            .btn-action {
                flex: 1;
                min-width: clamp(100px, 40vw, 150px);
            }
        }

        @media (max-width: 480px) {
            .navbar-custom {
                padding: clamp(0.7rem, 1.5vw, 0.9rem) clamp(0.8rem, 2vw, 1rem);
            }

            .main-content {
                padding: clamp(80px, 9vw, 95px) clamp(12px, 2.5vw, 15px) clamp(15px, 2.5vw, 20px);
            }

            .services-grid {
                grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
                gap: clamp(10px, 2vw, 12px);
            }

            .consultation-title {
                font-size: clamp(16px, 4.5vw, 20px);
            }

            .chat-container {
                max-width: 100%;
                height: clamp(350px, 70vh, 450px);
            }
        }
    </style>
</head>
<body>
    <!-- Background -->
    <div class="bg-decoration">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar-custom">
        <a href="index.php" class="logo-brand">
            <div class="logo-icon"><i class="fas fa-brain"></i></div>
            <span>Mind Care</span>
        </a>
    </nav>

    <!-- User Profile Dropdown -->
    <div class="user-profile-fixed" onclick="toggleMenu(event)">
        <div class="user-avatar-fixed"><?php echo $user_initial; ?></div>
        <div class="user-info-fixed">
            <div class="user-name-fixed"><?php echo htmlspecialchars($user_name); ?></div>
            <div class="user-email-fixed"><?php echo htmlspecialchars($user_email); ?></div>
        </div>
        <i class="fas fa-chevron-down dropdown-arrow"></i>

        <div class="dropdown-menu-fixed">
            <div class="dropdown-header">
                <div class="dropdown-avatar"><?php echo $user_initial; ?></div>
                <div class="dropdown-info">
                    <h4><?php echo htmlspecialchars($user_name); ?></h4>
                    <p><?php echo htmlspecialchars($user_email); ?></p>
                </div>
            </div>
            <a href="dashboard.php" class="dropdown-item"><i class="fas fa-home"></i> Dashboard</a>
            <a href="mood_tracker.php" class="dropdown-item"><i class="fas fa-heart"></i> Mood Tracker</a>
            <a href="journal.php" class="dropdown-item"><i class="fas fa-book"></i> Journal</a>
            <a href="relaxation.php" class="dropdown-item"><i class="fas fa-spa"></i> Relaxation</a>
            <div class="dropdown-divider"></div>
            <a href="logout.php" class="dropdown-item logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Greeting -->
        <section class="greeting-section" data-aos="fade-up">
            <div class="greeting-avatar">👋</div>
            <div class="greeting-content">
                <h1 class="greeting-title">Halo <?php echo htmlspecialchars($user_name); ?>!</h1>
                <p class="greeting-subtitle">Bagaimana perasaan Anda hari ini?</p>
            </div>
        </section>

        <!-- Banner -->
        <section class="consultation-banner" data-aos="fade-up" data-aos-delay="100">
            <h2 class="consultation-title">Ayo mulai hidup sehat dengan berkonsultasi untuk kesehatan mental</h2>
        </section>

        <!-- Services -->
        <section data-aos="fade-up" data-aos-delay="150">
            <h2 class="section-title">Layanan Kami :</h2>
            <div class="services-grid">
                <a href="mood_tracker.php" class="service-card">
                    <i class="fas fa-heart service-icon" style="color: #ff6b9d;"></i>
                    <p class="service-name">Mood Tracker</p>
                </a>
                <a href="journal.php" class="service-card">
                    <i class="fas fa-book service-icon" style="color: #4a5f8f;"></i>
                    <p class="service-name">Journal</p>
                </a>
                <a href="relaxation.php" class="service-card">
                    <i class="fas fa-spa service-icon" style="color: #52c41a;"></i>
                    <p class="service-name">Relaxation</p>
                </a>
                <a href="konseling.php" class="service-card">
                    <i class="fas fa-comments service-icon" style="color: #1890ff;"></i>
                    <p class="service-name">Konseling</p>
                </a>
                <a href="edukasi.php" class="service-card">
                    <i class="fas fa-graduation-cap service-icon" style="color: #faad14;"></i>
                    <p class="service-name">Edukasi</p>
                </a>
            </div>
        </section>

        <!-- Doctors -->
        <section class="doctors-section" data-aos="fade-up" data-aos-delay="200">
            <h2 class="section-title">Konsultan Profesional :</h2>
            
            <div class="doctor-card">
                <div class="doctor-avatar"><i class="fas fa-user-md"></i></div>
                <div class="doctor-info">
                    <h3 class="doctor-name">Dr. Sarah Anderson</h3>
                    <div class="doctor-rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span class="rating-text">4.9</span>
                        <span class="review-text">(280 Reviews)</span>
                    </div>
                </div>
                <div class="doctor-buttons">
                    <button class="btn-action" onclick="startChat('Dr. Sarah Anderson')">
                        <i class="fas fa-comments"></i> Chat
                    </button>
                    <button class="btn-action" onclick="startCall('Dr. Sarah Anderson')">
                        <i class="fas fa-phone"></i> Telepon
                    </button>
                </div>
            </div>

            <div class="doctor-card">
                <div class="doctor-avatar"><i class="fas fa-user-md"></i></div>
                <div class="doctor-info">
                    <h3 class="doctor-name">Dr. Michael Chen</h3>
                    <div class="doctor-rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span class="rating-text">4.8</span>
                        <span class="review-text">(215 Reviews)</span>
                    </div>
                </div>
                <div class="doctor-buttons">
                    <button class="btn-action" onclick="startChat('Dr. Michael Chen')">
                        <i class="fas fa-comments"></i> Chat
                    </button>
                    <button class="btn-action" onclick="startCall('Dr. Michael Chen')">
                        <i class="fas fa-phone"></i> Telepon
                    </button>
                </div>
            </div>
        </section>
    </main>

    <!-- Chat Modal -->
    <div id="chatModal" class="modal-overlay">
        <div class="chat-container">
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="chat-avatar"><i class="fas fa-user-md"></i></div>
                    <div>
                        <h3 class="chat-name" id="chatName">Dr. Unknown</h3>
                        <div class="online-status">🟢 Online</div>
                    </div>
                </div>
                <button class="close-btn" onclick="closeChat()"><i class="fas fa-times"></i></button>
            </div>
            <div class="chat-messages" id="messages"></div>
            <div class="chat-input-area">
                <input type="text" class="chat-input" id="chatInput" placeholder="Pesan...">
                <button class="send-btn" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <!-- Call Modal -->
    <div id="callModal" class="modal-overlay" style="background: rgba(26,35,50,0.95);">
        <div class="call-container">
            <div class="call-avatar-large"><i class="fas fa-user-md"></i></div>
            <div class="call-info">
                <h2 id="callName">Dr. Unknown</h2>
                <div class="call-status" id="callStatus">Menghubungi...</div>
                <div class="call-timer" id="callTimer" style="display:none;">00:00</div>
            </div>
            <div class="call-controls">
                <button class="call-btn" onclick="toggleMute()"><i class="fas fa-microphone"></i></button>
                <button class="call-btn" onclick="toggleVideo()"><i class="fas fa-video"></i></button>
                <button class="call-btn" onclick="toggleVolume()"><i class="fas fa-volume-up"></i></button>
                <button class="call-end call-btn" onclick="endCall()"><i class="fas fa-phone"></i></button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, offset: 100 });

        function toggleMenu(e) {
            e.stopPropagation();
            e.currentTarget.classList.toggle('active');
        }

        document.addEventListener('click', (e) => {
            const menu = document.querySelector('.user-profile-fixed');
            if (menu && !e.target.closest('.user-profile-fixed')) {
                menu.classList.remove('active');
            }
        });

        let callDuration = 0, callTimer = null;
        let isMuted = false, isVideoOn = false;

        function startChat(name) {
            document.getElementById('chatName').textContent = name;
            document.getElementById('messages').innerHTML = '<div class="message consultant"><div class="message-bubble">Halo! Apa yang bisa saya bantu?</div></div>';
            document.getElementById('chatInput').value = '';
            document.getElementById('chatModal').classList.add('active');
            document.getElementById('chatInput').focus();
        }

        function closeChat() {
            document.getElementById('chatModal').classList.remove('active');
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const msg = input.value.trim();
            if (!msg) return;

            const msgs = document.getElementById('messages');
            msgs.innerHTML += `<div class="message user"><div class="message-bubble">${msg}</div></div>`;
            input.value = '';
            msgs.scrollTop = msgs.scrollHeight;

            setTimeout(() => {
                msgs.innerHTML += `<div class="message consultant"><div class="message-bubble">Baik, terima kasih. Ada lagi?</div></div>`;
                msgs.scrollTop = msgs.scrollHeight;
            }, 800);
        }

        document.getElementById('chatInput')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });

        function startCall(name) {
            document.getElementById('callName').textContent = name;
            document.getElementById('callStatus').textContent = 'Menghubungi...';
            document.getElementById('callTimer').style.display = 'none';
            document.getElementById('callModal').classList.add('active');
            callDuration = 0;
            isMuted = false;
            isVideoOn = false;

            setTimeout(() => {
                document.getElementById('callStatus').textContent = '⏱️ Sedang berlangsung';
                document.getElementById('callTimer').style.display = 'block';
                if (callTimer) clearInterval(callTimer);
                callTimer = setInterval(() => {
                    callDuration++;
                    const m = Math.floor(callDuration / 60);
                    const s = callDuration % 60;
                    document.getElementById('callTimer').textContent = 
                        String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                }, 1000);
            }, 3000);
        }

        function endCall() {
            document.getElementById('callModal').classList.remove('active');
            if (callTimer) clearInterval(callTimer);
            const m = Math.floor(callDuration / 60);
            const s = callDuration % 60;
            alert(`Panggilan berakhir (${m}m ${s}s)`);
        }

        function toggleMute() {
            isMuted = !isMuted;
            const btn = document.querySelectorAll('.call-btn')[0];
            btn.innerHTML = isMuted ? '<i class="fas fa-microphone-slash"></i>' : '<i class="fas fa-microphone"></i>';
            btn.style.opacity = isMuted ? '0.5' : '1';
        }

        function toggleVideo() {
            isVideoOn = !isVideoOn;
            const btn = document.querySelectorAll('.call-btn')[1];
            btn.style.opacity = isVideoOn ? '1' : '0.5';
        }

        function toggleVolume() {
            const btn = document.querySelectorAll('.call-btn')[2];
            btn.style.opacity = btn.style.opacity === '0.5' ? '1' : '0.5';
        }
    </script>
</body>
</html>



