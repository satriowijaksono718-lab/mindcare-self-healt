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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relaxation - Mind Care</title>
    
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
            pointer-events: none;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            opacity: 0.04;
            filter: blur(60px);
            transform-origin: center;
        }

        .bubble-1 { width: 300px; height: 300px; background: rgba(255, 255, 255, 0.3); top: -50px; left: -50px; animation: float 15s ease-in-out infinite; }
        .bubble-2 { width: 200px; height: 200px; background: rgba(160, 223, 232, 0.4); top: 50%; right: -100px; animation: float 20s ease-in-out infinite; animation-delay: -5s; }
        .bubble-3 { width: 250px; height: 250px; background: rgba(255, 255, 255, 0.2); bottom: -100px; left: 10%; animation: float 18s ease-in-out infinite; animation-delay: -10s; }
        .bubble-4 { width: 150px; height: 150px; background: rgba(160, 223, 232, 0.3); top: 20%; right: 10%; animation: float 22s ease-in-out infinite; animation-delay: -3s; }

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
            padding: 1.2rem 2rem;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 500;
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1a2332;
            font-weight: 800;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .logo-brand:hover {
            transform: scale(1.08) translateY(-2px);
            color: #8b5cf6;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .back-link {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s ease;
            padding: 8px 16px;
            background: rgba(124, 58, 237, 0.08);
            border-radius: 10px;
            border: 2px solid rgba(124, 58, 237, 0.15);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-link:hover {
            color: #6d28d9;
            background: rgba(124, 58, 237, 0.15);
            border-color: rgba(124, 58, 237, 0.3);
            transform: translateX(4px);
        }

        .main-content {
            flex: 1;
            z-index: 10;
            padding-top: 90px;
        }

        .container-main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .page-title {
            font-size: 48px;
            font-weight: 900;
            color: #1a2332;
            margin-bottom: 15px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #666;
            font-weight: 500;
        }

        /* Cards Grid */
        .relaxation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 28px;
            margin-bottom: 40px;
        }

        /* Card Glass Style */
        .relaxation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.08);
            border: 1px solid rgba(139, 92, 246, 0.1);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .relaxation-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #a0dfe8 0%, #8ed8e3 100%);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .relaxation-card:hover {
            box-shadow: 0 25px 70px rgba(139, 92, 246, 0.12);
            transform: translateY(-8px);
        }

        .relaxation-card:hover::before {
            transform: scaleX(1);
        }

        .card-number {
            display: inline-block;
            background: linear-gradient(135deg, #a0dfe8 0%, #8ed8e3 100%);
            color: #1a2332;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin-bottom: 16px;
            font-size: 18px;
            box-shadow: 0 8px 20px rgba(160, 223, 232, 0.2);
        }

        .card-icon {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
        }

        .card-title {
            font-size: 20px;
            font-weight: 800;
            color: #1a2332;
            margin-bottom: 12px;
        }

        .card-description {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .card-section {
            margin-bottom: 16px;
        }

        .card-section-title {
            font-weight: 800;
            color: #7c3aed;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-section-content {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
        }

        .card-section ul {
            list-style: none;
            margin-left: 0;
        }

        .card-section li {
            margin-bottom: 6px;
            padding-left: 18px;
            position: relative;
        }

        .card-section li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #8b5cf6;
            font-weight: 800;
            font-size: 14px;
        }

        .card-tip {
            background: linear-gradient(135deg, rgba(160, 223, 232, 0.1) 0%, rgba(139, 92, 246, 0.08) 100%);
            padding: 14px 16px;
            border-radius: 12px;
            border-left: 4px solid #8b5cf6;
            font-size: 13px;
            color: #666;
            margin-top: 16px;
        }

        .card-tip strong {
            color: #1a2332;
            font-weight: 700;
        }

        .read-more-btn {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #a0dfe8 0%, #8ed8e3 100%);
            color: #1a2332;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .read-more-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(160, 223, 232, 0.2);
        }

        /* Modal */
        .modal-custom {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            /* allow scrolling inside the overlay when dialog is taller than viewport */
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            /* align to start so tall dialogs can grow and be scrolled into view */
            align-items: flex-start;
            justify-content: center;
            padding-top: 40px;
            padding-bottom: 40px;
            animation: fadeIn 0.2s ease;
        }

        /* Allow scroll events to propagate normally; we'll handle redirection in JS */

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-custom.active {
            display: flex;
        }

        .modal-dialog {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 700px;
            width: 90%;
            /* use viewport-aware max height so content can scroll inside the dialog */
            max-height: calc(100vh - 120px);
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.3s ease;
            margin: 0 auto; /* center horizontally while allowing vertical scroll */
        }

        .modal-dialog:focus {
            outline: none;
        }

        /* scroll control buttons shown on the dialog to force scroll if wheel doesn't work */
        .scroll-controls {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 1200;
        }

        .scroll-controls button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: rgba(124, 58, 237, 0.12);
            color: #6d28d9;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(124,58,237,0.08);
        }

        .scroll-controls button:hover {
            transform: translateY(-2px);
            background: rgba(124,58,237,0.18);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            border-bottom: 2px solid rgba(139, 92, 246, 0.1);
            padding-bottom: 18px;
        }

        .modal-title {
            font-size: 28px;
            font-weight: 900;
            color: #1a2332;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #aaa;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            color: #1a2332;
            transform: rotate(90deg);
        }

        .modal-description {
            color: #666;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 24px;
        }

        .instructions {
            background: linear-gradient(135deg, rgba(160, 223, 232, 0.08) 0%, rgba(139, 92, 246, 0.06) 100%);
            padding: 20px;
            border-radius: 14px;
            margin-bottom: 24px;
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .instructions h4 {
            color: #1a2332;
            margin-bottom: 14px;
            font-size: 16px;
            font-weight: 800;
        }

        .instructions ol {
            margin-left: 24px;
            line-height: 1.8;
            color: #666;
            font-size: 14px;
        }

        .instructions li {
            margin-bottom: 8px;
        }

        .modal-tip {
            background: linear-gradient(135deg, rgba(160, 223, 232, 0.1) 0%, rgba(139, 92, 246, 0.08) 100%);
            padding: 16px;
            border-radius: 12px;
            border-left: 4px solid #8b5cf6;
            font-size: 14px;
            color: #666;
            margin-bottom: 24px;
        }

        .modal-tip strong {
            color: #1a2332;
            font-weight: 700;
        }

        /* Timer Section */
        .timer {
            display: none;
            background: linear-gradient(135deg, #a0dfe8 0%, #8ed8e3 100%);
            padding: 24px;
            border-radius: 14px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(160, 223, 232, 0.2);
        }

        .timer.active {
            display: block;
        }

        .timer-display {
            font-size: 64px;
            font-weight: 800;
            color: #1a2332;
            margin-bottom: 18px;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        .timer-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .timer-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            background: white;
            color: #1a2332;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timer-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .relaxation-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding-top: 80px;
            }

            .page-title {
                font-size: 32px;
                margin-bottom: 30px;
            }

            .page-subtitle {
                font-size: 14px;
            }

            .relaxation-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .relaxation-card {
                padding: 24px;
            }

            .card-number {
                width: 42px;
                height: 42px;
                font-size: 16px;
            }

            .card-icon {
                font-size: 40px;
            }

            .modal-dialog {
                padding: 28px;
                max-width: 90%;
            }

            .modal-title {
                font-size: 22px;
            }

            .timer-display {
                font-size: 48px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 24px;
            }

            .page-header {
                margin-bottom: 30px;
            }

            .relaxation-card {
                padding: 16px;
            }

            .card-title {
                font-size: 18px;
            }

            .modal-dialog {
                padding: 20px;
                max-width: 95%;
            }

            .instructions ol {
                margin-left: 18px;
            }

            .timer-display {
                font-size: 40px;
            }

            .timer-btn {
                padding: 10px 16px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-decoration">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
        <div class="bubble bubble-4"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar-custom">
        <a href="dashboard.php" class="logo-brand">
            <div class="logo-icon">⊙</div>
            <span>Mind Care</span>
        </a>
        <div class="nav-right">
            <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-main">
            <!-- Page Header -->
            <div class="page-header" data-aos="fade-up">
                <h1 class="page-title">🧘 Relaxation</h1>
                <p class="page-subtitle">Temukan teknik relaksasi yang tepat untuk mengurangi stres dan meningkatkan kesejahteraan mental</p>
            </div>

            <!-- Relaxation Cards Grid -->
            <div class="relaxation-grid">
                <!-- Card 1: Deep Breathing -->
                <div class="relaxation-card" onclick="openModal(0)" data-aos="fade-up">
                    <div class="card-number">1</div>
                    <div class="card-icon">🫁</div>
                    <div class="card-title">Relaksasi Pernapasan Dalam</div>
                    <div class="card-description">Membantu menurunkan stres dan kecemasan melalui teknik pernapasan.</div>
                    
                    <div class="card-section">
                        <div class="card-section-title">Cara Singkat:</div>
                        <div class="card-section-content">
                            <ul>
                                <li>Tarik napas 4 detik</li>
                                <li>Tahan napas 6 detik</li>
                                <li>Hembuskan 6 detik</li>
                                <li>Ulangi 5-10 kali</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-tip">
                        <strong>✨ Tips:</strong> Ideal dilakukan saat cemas atau sebelum tidur
                    </div>
                    <button class="read-more-btn" onclick="openModal(0); event.stopPropagation();">Pelajari Lebih Lanjut</button>
                </div>

                <!-- Card 2: Progressive Muscle Relaxation -->
                <div class="relaxation-card" onclick="openModal(1)" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-number">2</div>
                    <div class="card-icon">💪</div>
                    <div class="card-title">Relaksasi Otot Progresif</div>
                    <div class="card-description">Mengurangi ketegangan fisik akibat stres dengan cara terstruktur.</div>
                    
                    <div class="card-section">
                        <div class="card-section-title">Cara Singkat:</div>
                        <div class="card-section-content">
                            <ul>
                                <li>Tegangkan otot selama 5 detik</li>
                                <li>Lepaskan dan rileks 10 detik</li>
                                <li>Lanjutkan ke otot lain</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-tip">
                        <strong>✨ Cocok untuk:</strong> Orang yang sering tegang atau sulit tidur
                    </div>
                    <button class="read-more-btn" onclick="openModal(1); event.stopPropagation();">Pelajari Lebih Lanjut</button>
                </div>

                <!-- Card 3: Mindfulness -->
                <div class="relaxation-card" onclick="openModal(2)" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-number">3</div>
                    <div class="card-icon">🧠</div>
                    <div class="card-title">Mindfulness / Meditasi Sadar</div>
                    <div class="card-description">Melatih fokus pada saat ini tanpa menghakimi pemikiran Anda.</div>
                    
                    <div class="card-section">
                        <div class="card-section-title">Cara Singkat:</div>
                        <div class="card-section-content">
                            <ul>
                                <li>Fokus pada napas atau tubuh</li>
                                <li>Kembalikan perhatian dengan lembut</li>
                                <li>Lakukan 5-10 menit setiap hari</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-tip">
                        <strong>✨ Manfaat:</strong> Mengurangi overthinking dan meningkatkan kesadaran diri
                    </div>
                    <button class="read-more-btn" onclick="openModal(2); event.stopPropagation();">Pelajari Lebih Lanjut</button>
                </div>

                <!-- Card 4: Music Relaxation -->
                <div class="relaxation-card" onclick="openModal(3)" data-aos="fade-up">
                    <div class="card-number">4</div>
                    <div class="card-icon">🎵</div>
                    <div class="card-title">Relaksasi dengan Musik</div>
                    <div class="card-description">Menstabilkan emosi dan menenangkan pikiran melalui musik.</div>
                    
                    <div class="card-section">
                        <div class="card-section-title">Cara Singkat:</div>
                        <div class="card-section-content">
                            <ul>
                                <li>Pilih musik instrumental</li>
                                <li>Gunakan earphone dan duduk santai</li>
                                <li>Tutup mata dan dengarkan</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-tip">
                        <strong>✨ Rekomendasi:</strong> Baik dilakukan setelah aktivitas padat
                    </div>
                    <button class="read-more-btn" onclick="openModal(3); event.stopPropagation();">Pelajari Lebih Lanjut</button>
                </div>

                <!-- Card 5: Journaling -->
                <div class="relaxation-card" onclick="openModal(4)" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-number">5</div>
                    <div class="card-icon">📝</div>
                    <div class="card-title">Menulis Perasaan (Journaling)</div>
                    <div class="card-description">Melepaskan beban emosi yang terpendani melalui tulisan.</div>
                    
                    <div class="card-section">
                        <div class="card-section-title">Cara Singkat:</div>
                        <div class="card-section-content">
                            <ul>
                                <li>Tulis apa yang Anda rasakan</li>
                                <li>Tidak perlu rapi atau panjang</li>
                                <li>Tulis dengan bebas selama 10-15 menit</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-tip">
                        <strong>✨ Efektif untuk:</strong> Mengelola stres emosional dan pikiran
                    </div>
                    <button class="read-more-btn" onclick="openModal(4); event.stopPropagation();">Pelajari Lebih Lanjut</button>
                </div>

                <!-- Card 6: Light Exercise -->
                <div class="relaxation-card" onclick="openModal(5)" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-number">6</div>
                    <div class="card-icon">🏃</div>
                    <div class="card-title">Aktivitas Fisik Ringan</div>
                    <div class="card-description">Meningkatkan hormon endorfin (hormon bahagia) tubuh Anda.</div>
                    
                    <div class="card-section">
                        <div class="card-section-title">Contoh Aktivitas:</div>
                        <div class="card-section-content">
                            <ul>
                                <li>Jalan santai</li>
                                <li>Stretching</li>
                                <li>Yoga ringan</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-tip">
                        <strong>✨ Durasi:</strong> Lakukan 15-30 menit setiap hari
                    </div>
                    <button class="read-more-btn" onclick="openModal(5); event.stopPropagation();">Pelajari Lebih Lanjut</button>
                </div>

                <!-- Card 7: Talk to Someone -->
                <div class="relaxation-card" onclick="openModal(6)" data-aos="fade-up">
                    <div class="card-number">7</div>
                    <div class="card-icon">💬</div>
                    <div class="card-title">Berbicara dengan Orang Terpercaya</div>
                    <div class="card-description">Meringankan beban mental dengan berbagi kepada orang yang tepat.</div>
                    
                    <div class="card-section">
                        <div class="card-section-title">Cara:</div>
                        <div class="card-section-content">
                            <ul>
                                <li>Cerita ke teman atau keluarga</li>
                                <li>Cari waktu yang tepat</li>
                                <li>Jelaskan dengan jujur</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-tip">
                        <strong>✨ Ingat:</strong> Jangan memendam sendiri, berbagi untuk kesehatan mental yang lebih baik
                    </div>
                    <button class="read-more-btn" onclick="openModal(6); event.stopPropagation();">Pelajari Lebih Lanjut</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-custom" id="relaxationModal">
        <div class="modal-dialog" id="modalDialog" tabindex="-1">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle"></h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <p class="modal-description" id="modalDescription"></p>

            <div class="instructions">
                <h4>📋 Langkah-Langkah:</h4>
                <ol id="modalInstructions"></ol>
            </div>

            <div class="modal-tip">
                <strong>💡 Tips:</strong> <span id="modalTips"></span>
            </div>

            <div class="timer" id="timer">
                <div class="timer-display" id="timerDisplay">05:00</div>
                <div class="timer-buttons">
                    <button class="timer-btn" onclick="startTimer()"><i class="fas fa-play"></i> Mulai</button>
                    <button class="timer-btn" onclick="pauseTimer()"><i class="fas fa-pause"></i> Pause</button>
                    <button class="timer-btn" onclick="resetTimer()"><i class="fas fa-redo"></i> Reset</button>
                </div>
            </div>
            
            <!-- Scroll controls for when wheel/touch don't scroll the dialog -->
            <div class="scroll-controls" aria-hidden="true">
                <button id="scrollUp" title="Gulir ke atas">▲</button>
                <button id="scrollDown" title="Gulir ke bawah">▼</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({ duration: 700, once: true });

        // Relaxation data
        const relaxations = [
            {
                title: "🫁 Relaksasi Pernapasan Dalam",
                description: "Teknik pernapasan yang telah terbukti secara ilmiah untuk menenangkan sistem saraf dan mengurangi kecemasan. Metode ini sangat efektif untuk mengatasi situasi stres akut.",
                instructions: [
                    "Duduk atau berbaring dengan posisi nyaman",
                    "Tutup mulut dan tarik napas perlahan melalui hidung selama 4 detik",
                    "Tahan napas selama 6 detik di dalam paru-paru Anda",
                    "Hembuskan napas perlahan melalui mulut selama 6 detik",
                    "Tunggu 2 detik sebelum mengulangi",
                    "Ulangi proses ini 5-10 kali atau sampai merasa tenang",
                    "Untuk hasil maksimal, lakukan setiap hari secara rutin"
                ],
                tips: "Lakukan saat cemas, panik, atau sebelum tidur. Anda juga bisa melakukan teknik ini di tempat kerja atau sekolah tanpa diketahui orang lain.",
                timer: true
            },
            {
                title: "💪 Relaksasi Otot Progresif",
                description: "Teknik menenangkan yang dilakukan dengan cara mengencangkan dan melepaskan kelompok otot secara bertahap. Sangat efektif untuk orang yang menyimpan ketegangan di tubuh mereka.",
                instructions: [
                    "Cari posisi yang nyaman, baik duduk atau berbaring",
                    "Mulai dari otot tangan kanan: tegangkan seuat mungkin selama 5 detik",
                    "Lepaskan tiba-tiba dan rasakan rileks selama 10 detik",
                    "Lanjutkan dengan tangan kiri, bahu, wajah, perut, dan kaki",
                    "Perhatikan sensasi kontras antara ketegangan dan relaksasi",
                    "Ulangi 2-3 kali untuk setiap kelompok otot",
                    "Lakukan rutin, terutama sebelum tidur, untuk hasil optimal"
                ],
                tips: "Lakukan sebelum tidur atau saat merasa banyak stres. Teknik ini sangat bagus dikombinasikan dengan pernapasan dalam untuk efek yang lebih baik.",
                timer: true
            },
            {
                title: "🧠 Mindfulness / Meditasi Sadar",
                description: "Latihan fokus pada moment yang sekarang tanpa menghakimi pemikiran atau perasaan Anda. Praktik kuno yang telah terbukti meningkatkan kesejahteraan mental dan emosional.",
                instructions: [
                    "Cari tempat yang tenang dan nyaman untuk duduk",
                    "Tutup mata atau fokuskan pandangan ke satu titik",
                    "Mulai dengan pernapasan normal dan perhatikan setiap napas",
                    "Ketika pikiran melayang, kembalikan perhatian dengan lembut ke napas",
                    "Jangan mencoba mengontrol pikiran, cukup amati dan lepaskan",
                    "Lakukan selama 5-10 menit, lebih lama jika Anda merasa nyaman",
                    "Tingkatkan durasi secara bertahap seiring dengan kebiasaan"
                ],
                tips: "Membantu mengurangi overthinking dan meningkatkan kesadaran diri. Konsistensi lebih penting daripada durasi. Bahkan 5 menit setiap hari lebih baik daripada satu sesi panjang seminggu.",
                timer: true
            },
            {
                title: "🎵 Relaksasi dengan Musik",
                description: "Mendengarkan musik untuk menstabilkan emosi, menurunkan detak jantung, dan menenangkan pikiran. Musik memiliki kekuatan universal untuk menyentuh emosi kita.",
                instructions: [
                    "Pilih musik yang Anda sukai: instrumental, alam, atau lagu favorit",
                    "Pastikan volumenya cukup namun tidak terlalu keras",
                    "Gunakan earphone berkualitas baik atau speaker yang bagus",
                    "Cari tempat yang nyaman untuk duduk atau berbaring",
                    "Tutup mata dan biarkan musik membimbing pikiran Anda",
                    "Biarkan emosi mengalir tanpa menahan atau mendorong mereka",
                    "Lakukan selama 20-30 menit untuk relaksasi optimal"
                ],
                tips: "Baik dilakukan setelah aktivitas padat atau sebelum istirahat. Coba genre berbeda untuk menemukan musik yang paling menenangkan bagi Anda.",
                timer: true
            },
            {
                title: "📝 Menulis Perasaan (Journaling)",
                description: "Melepaskan beban emosi melalui tulisan tanpa filter. Hal ini membantu mengorganisir pikiran dan memberikan perspektif baru terhadap masalah.",
                instructions: [
                    "Ambil kertas dan pena atau buka aplikasi catatan di ponsel",
                    "Tulis apa yang Anda rasakan tanpa disaring atau dipertimbangkan",
                    "Tidak perlu khawatkan tata bahasa, ejaan, atau struktur kalimat",
                    "Tuliskan semua yang ada di pikiran Anda, positif atau negatif",
                    "Jangan berhenti untuk berpikir atau mengedit saat menulis",
                    "Tulis dengan bebas selama 10-15 menit atau lebih jika perlu",
                    "Setelah selesai, Anda boleh membaca atau langsung membuangnya"
                ],
                tips: "Efektif untuk mengelola stres emosional dan mengorganisir pikiran. Banyak orang merasa lebih ringan setelah menulis. Lakukan setiap hari atau kapan pun diperlukan.",
                timer: false
            },
            {
                title: "🏃 Aktivitas Fisik Ringan",
                description: "Olahraga ringan untuk meningkatkan hormon endorfin (hormon bahagia) dan meningkatkan kesehatan fisik secara keseluruhan. Bergerak adalah obat alami untuk stress.",
                instructions: [
                    "Pilih aktivitas ringan yang Anda nikmati: jalan santai, stretching, atau yoga",
                    "Lakukan di tempat yang nyaman seperti taman, rumah, atau kamar gym",
                    "Mulai dengan durasi 15-20 menit jika Anda pemula",
                    "Tingkatkan durasi secara bertahap menjadi 30 menit",
                    "Lakukan dengan ritme yang nyaman, bukan untuk kecepatan atau intensitas",
                    "Fokus pada bagaimana tubuh Anda terasa saat bergerak",
                    "Konsistensi lebih penting daripada intensitas untuk hasil jangka panjang"
                ],
                tips: "Lakukan 15-30 menit setiap hari untuk meningkatkan mood dan kesehatan fisik. Aktivitas ringan lebih baik daripada tidak sama sekali. Temukan aktivitas yang Anda sukai agar lebih termotivasi.",
                timer: true
            },
            {
                title: "💬 Berbicara dengan Orang Terpercaya",
                description: "Meringankan beban mental dengan berbagi kepada orang yang tepat. Memiliki seseorang untuk mendengarkan adalah salah satu bentuk dukungan emosional yang paling kuat.",
                instructions: [
                    "Identifikasi orang yang Anda percaya sepenuhnya (teman, keluarga, atau profesional)",
                    "Cari waktu yang tepat ketika mereka tidak terburu-buru",
                    "Mulai percakapan dengan jujur tentang apa yang Anda rasakan",
                    "Jelaskan situasi atau perasaan Anda dengan detail",
                    "Dengarkan feedback dan dukungan mereka dengan terbuka",
                    "Bersiaplah untuk menerima perspektif yang berbeda",
                    "Ucapkan terima kasih atas waktu dan dukungan mereka"
                ],
                tips: "Jangan memendam sendiri. Berbagi membantu kesehatan mental yang lebih baik. Jika merasa perlu bantuan profesional, jangan ragu untuk menghubungi psikolog atau konselor.",
                timer: false
            }
        ];

        let currentTimer = null;
        let timerSeconds = 300;

        function openModal(index) {
            const relaxation = relaxations[index];
            document.getElementById('modalTitle').textContent = relaxation.title;
            document.getElementById('modalDescription').textContent = relaxation.description;

            const instructionsList = document.getElementById('modalInstructions');
            instructionsList.innerHTML = '';
            relaxation.instructions.forEach(instruction => {
                const li = document.createElement('li');
                li.textContent = instruction;
                instructionsList.appendChild(li);
            });

            document.getElementById('modalTips').textContent = relaxation.tips;

            const timerDiv = document.getElementById('timer');
            if (relaxation.timer) {
                timerDiv.classList.add('active');
            } else {
                timerDiv.classList.remove('active');
                resetTimer();
            }

            // lock background scroll while keeping modal scrollable
            // store current scroll position
            const scrollY = window.scrollY || window.pageYOffset;
            document.body.dataset.scrollY = scrollY;
            // fix body in place
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.left = '0';
            document.body.style.right = '0';

            document.getElementById('relaxationModal').classList.add('active');
            // focus dialog so wheel/touch events target it
            const dlg = document.getElementById('modalDialog');
            if (dlg) {
                dlg.focus({ preventScroll: true });
            }

            // add overlay handlers to redirect all scroll to dialog
            const overlay = document.getElementById('relaxationModal');
            const dlgEl = document.getElementById('modalDialog');
            if (overlay && dlgEl) {
                // capture-phase wheel handler to intercept and redirect to dialog
                const wheelHandlerOverlay = function(e) {
                    if (dlgEl.contains(e.target)) {
                        // event is inside dialog, let dialog handler take it
                        return;
                    }
                    // event is on overlay, redirect to dialog
                    e.preventDefault();
                    e.stopPropagation();
                    dlgEl.scrollBy({ top: e.deltaY, behavior: 'auto' });
                };
                overlay.addEventListener('wheel', wheelHandlerOverlay, { capture: true, passive: false });
                
                // capture-phase touchmove handler to redirect to dialog
                let lastTouchY = 0;
                const touchStartOverlay = function(e) {
                    if (dlgEl.contains(e.target)) return;
                    lastTouchY = e.touches && e.touches[0] ? e.touches[0].clientY : 0;
                };
                const touchMoveOverlay = function(e) {
                    if (dlgEl.contains(e.target)) return;
                    if (!e.touches || !e.touches[0]) return;
                    e.preventDefault();
                    e.stopPropagation();
                    const currentY = e.touches[0].clientY;
                    const dy = lastTouchY - currentY;
                    if (Math.abs(dy) > 0) {
                        dlgEl.scrollBy({ top: dy, behavior: 'auto' });
                        lastTouchY = currentY;
                    }
                };
                overlay.addEventListener('touchstart', touchStartOverlay, { capture: true, passive: false });
                overlay.addEventListener('touchmove', touchMoveOverlay, { capture: true, passive: false });
                
                overlay._wheelHandler = wheelHandlerOverlay;
                overlay._touchStart = touchStartOverlay;
                overlay._touchMove = touchMoveOverlay;
            }
        }

        function closeModal() {
            document.getElementById('relaxationModal').classList.remove('active');
            pauseTimer();
            // restore body scroll position
            const prevScroll = parseInt(document.body.dataset.scrollY || '0', 10);
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.left = '';
            document.body.style.right = '';
            // remove stored value
            delete document.body.dataset.scrollY;
            // remove overlay handlers (they're now document-level)
            const overlay = document.getElementById('relaxationModal');
            if (overlay && overlay._wheelHandler) {
                overlay.removeEventListener('wheel', overlay._wheelHandler, { capture: true });
                overlay.removeEventListener('touchstart', overlay._touchStart, { capture: true });
                overlay.removeEventListener('touchmove', overlay._touchMove, { capture: true });
                delete overlay._wheelHandler;
                delete overlay._touchStart;
                delete overlay._touchMove;
            }
            // remove dialog handlers
            const dlg = document.getElementById('modalDialog');
            if (dlg) {
                if (dlg._wheelHandler) dlg.removeEventListener('wheel', dlg._wheelHandler, { passive: false });
                if (dlg._touchStart) dlg.removeEventListener('touchstart', dlg._touchStart, { passive: false });
                if (dlg._touchMove) dlg.removeEventListener('touchmove', dlg._touchMove, { passive: false });
                delete dlg._wheelHandler;
                delete dlg._touchStart;
                delete dlg._touchMove;
            }
            // restore window scroll
            window.scrollTo(0, prevScroll);
        }

        function startTimer() {
            if (currentTimer) return;
            
            currentTimer = setInterval(() => {
                timerSeconds--;
                updateTimerDisplay();
                
                if (timerSeconds <= 0) {
                    clearInterval(currentTimer);
                    currentTimer = null;
                    alert('✨ Waktu relaksasi selesai! Bagaimana perasaan Anda sekarang?');
                    resetTimer();
                }
            }, 1000);
        }

        function pauseTimer() {
            if (currentTimer) {
                clearInterval(currentTimer);
                currentTimer = null;
            }
        }

        function resetTimer() {
            pauseTimer();
            timerSeconds = 300;
            updateTimerDisplay();
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timerSeconds / 60);
            const seconds = timerSeconds % 60;
            document.getElementById('timerDisplay').textContent = 
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }

        // Close modal when clicking outside
        document.getElementById('relaxationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Scroll control functions
        function scrollDialog(delta) {
            const dlg = document.getElementById('modalDialog');
            if (!dlg) return;
            dlg.scrollBy({ top: delta, behavior: 'smooth' });
        }

        // Attach controls
        document.addEventListener('DOMContentLoaded', function() {
            const up = document.getElementById('scrollUp');
            const down = document.getElementById('scrollDown');
            if (up) up.addEventListener('click', function(e){ e.stopPropagation(); scrollDialog(-240); });
            if (down) down.addEventListener('click', function(e){ e.stopPropagation(); scrollDialog(240); });

            const dlg = document.getElementById('modalDialog');
            if (dlg) {
                dlg.addEventListener('keydown', function(e){
                    if (e.key === 'ArrowDown') { e.preventDefault(); scrollDialog(80); }
                    if (e.key === 'ArrowUp') { e.preventDefault(); scrollDialog(-80); }
                    if (e.key === 'PageDown') { e.preventDefault(); scrollDialog(400); }
                    if (e.key === 'PageUp') { e.preventDefault(); scrollDialog(-400); }
                });
            }
        });

        // document-level scroll interception: when modal is open, redirect all scroll to dialog
        document.addEventListener('wheel', function(e) {
            if (!document.getElementById('relaxationModal').classList.contains('active')) return;
            const dlg = document.getElementById('modalDialog');
            if (!dlg) return;
            e.preventDefault();
            dlg.scrollBy({ top: e.deltaY, behavior: 'auto' });
        }, { passive: false, capture: true });

        document.addEventListener('touchmove', function(e) {
            if (!document.getElementById('relaxationModal').classList.contains('active')) return;
            const dlg = document.getElementById('modalDialog');
            if (!dlg) return;
            if (dlg.contains(e.target)) return; // if target is inside dialog, let it scroll normally
            
            e.preventDefault();
            if (!e.touches || !e.touches[0]) return;
            
            // simple touch scroll: redirect touch delta to dialog
            let touchStartY = 0;
            
            // on first touchmove for this touch, record Y
            if (!e._touchStartY) {
                e._touchStartY = e.touches[0].clientY;
            }
            
            const currentY = e.touches[0].clientY;
            const dy = e._touchStartY - currentY;
            if (Math.abs(dy) > 0) {
                dlg.scrollBy({ top: dy, behavior: 'auto' });
                e._touchStartY = currentY;
            }
        }, { passive: false, capture: true });
    </script>
</body>
</html>
