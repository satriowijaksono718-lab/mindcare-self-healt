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
    <title>Edukasi Kesehatan Mental - Mind Care | Panduan Lengkap</title>
    
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

        /* Education Grid */
        .education-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 28px;
            margin-bottom: 60px;
        }

        /* Card Glass Style */
        .education-card {
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

        .education-card::before {
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

        .education-card:hover {
            box-shadow: 0 25px 70px rgba(139, 92, 246, 0.12);
            transform: translateY(-8px);
        }

        .education-card:hover::before {
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
            transition: transform 0.3s ease;
        }

        .education-card:hover .card-icon {
            transform: scale(1.1) rotateZ(5deg);
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
            margin-bottom: 16px;
            font-weight: 500;
        }

        .card-action {
            display: inline-block;
            color: #7c3aed;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            padding: 8px 16px;
            background: rgba(124, 58, 237, 0.08);
            border-radius: 8px;
            border: 2px solid rgba(124, 58, 237, 0.2);
        }

        .card-action:hover {
            background: rgba(124, 58, 237, 0.15);
            border-color: rgba(124, 58, 237, 0.4);
            transform: translateX(4px);
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
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            align-items: flex-start;
            justify-content: center;
            padding-top: 40px;
            padding-bottom: 40px;
            animation: fadeIn 0.2s ease;
            touch-action: auto; /* allow pointer/touch interactions */
        }

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
            max-height: calc(100vh - 120px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch; /* enable momentum scrolling on iOS */
            touch-action: pan-y; /* allow vertical touchpad/touch scrolling */
            overscroll-behavior: contain; /* prevent scroll chaining to the body */
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.3s ease;
            margin: 0 auto;
        }

        .modal-dialog:focus {
            outline: none;
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

        .modal-section {
            margin-bottom: 28px;
        }

        .modal-section-title {
            color: #7c3aed;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }

        .modal-section-content {
            color: #666;
            font-size: 14px;
            line-height: 1.8;
        }

        .modal-section ul {
            list-style: none;
            margin-left: 0;
            padding-left: 0;
        }

        .modal-section li {
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }

        .modal-section li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #8b5cf6;
            font-weight: 800;
        }

        .modal-section ol {
            margin-left: 20px;
        }

        .modal-section ol li {
            margin-bottom: 10px;
        }

        /* Scrollbar Styling */
        .modal-dialog::-webkit-scrollbar {
            width: 8px;
        }

        .modal-dialog::-webkit-scrollbar-track {
            background: rgba(139, 92, 246, 0.05);
            border-radius: 10px;
        }

        .modal-dialog::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            border-radius: 10px;
        }

        .modal-dialog::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .education-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

            .education-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .education-card {
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
        }

        @media (max-width: 480px) {
            .navbar-custom {
                padding: 1rem;
            }

            .logo-brand {
                font-size: 16px;
                gap: 8px;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .back-link {
                padding: 6px 12px;
                font-size: 12px;
            }

            .page-title {
                font-size: 24px;
            }

            .page-header {
                margin-bottom: 30px;
            }

            .education-card {
                padding: 16px;
            }

            .card-title {
                font-size: 18px;
            }

            .modal-dialog {
                padding: 20px;
                max-width: 95%;
            }

            .modal-title {
                font-size: 20px;
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
                <h1 class="page-title">📚 Edukasi Kesehatan Mental</h1>
                <p class="page-subtitle">Pelajari informasi penting tentang kesehatan mental dan cara merawat diri dengan baik</p>
            </div>

            <!-- Education Cards Grid -->
            <div class="education-grid">
                <!-- Card 1: Stress Management -->
                <div class="education-card" onclick="openModal(0)" data-aos="fade-up">
                    <div class="card-number">1</div>
                    <div class="card-icon">😰</div>
                    <div class="card-title">Mengelola Stres</div>
                    <div class="card-description">Pelajari pengertian stres, penyebab, gejala, dan cara-cara efektif untuk mengelola stres dalam kehidupan sehari-hari.</div>
                    <a href="javascript:void(0)" class="card-action" onclick="openModal(0); event.stopPropagation();">Pelajari Lebih Lanjut</a>
                </div>

                <!-- Card 2: Depression -->
                <div class="education-card" onclick="openModal(1)" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-number">2</div>
                    <div class="card-icon">😢</div>
                    <div class="card-title">Mengenai Depresi</div>
                    <div class="card-description">Pahami apa itu depresi, gejala-gejalanya, faktor risiko, dan kapan harus mencari bantuan dari profesional.</div>
                    <a href="javascript:void(0)" class="card-action" onclick="openModal(1); event.stopPropagation();">Pelajari Lebih Lanjut</a>
                </div>

                <!-- Card 3: Self-Care -->
                <div class="education-card" onclick="openModal(2)" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-number">3</div>
                    <div class="card-icon">💅</div>
                    <div class="card-title">Self-Care (Perawatan Diri)</div>
                    <div class="card-description">Diskover berbagai jenis self-care untuk menjaga kesehatan fisik, mental, emosional, sosial, dan spiritual.</div>
                    <a href="javascript:void(0)" class="card-action" onclick="openModal(2); event.stopPropagation();">Pelajari Lebih Lanjut</a>
                </div>

                <!-- Card 4: Anxiety -->
                <div class="education-card" onclick="openModal(3)" data-aos="fade-up">
                    <div class="card-number">4</div>
                    <div class="card-icon">😟</div>
                    <div class="card-title">Anxiety (Gangguan Kecemasan)</div>
                    <div class="card-description">Ketahui pengertian anxiety, gejala fisik dan psikologis, penyebab, dan teknik mengatasi kecemasan.</div>
                    <a href="javascript:void(0)" class="card-action" onclick="openModal(3); event.stopPropagation();">Pelajari Lebih Lanjut</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-custom" id="educationModal">
        <div class="modal-dialog" id="modalDialog" tabindex="0">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle"></h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="modalContent"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({ duration: 700, once: true });

        // Education Content Data
        const educationContent = [
            {
                title: "😰 Mengelola Stres (Stress Management)",
                sections: [
                    {
                        title: "A. Pengertian Stres",
                        content: "Stres adalah respons fisik, emosional, dan psikologis seseorang terhadap tuntutan atau tekanan (stressor) yang melebihi kemampuan adaptasi individu."
                    },
                    {
                        title: "B. Penyebab Stres",
                        items: [
                            "Tekanan akademik atau pekerjaan",
                            "Masalah keluarga dan hubungan sosial",
                            "Masalah ekonomi",
                            "Penyakit fisik atau kronis",
                            "Perubahan hidup (kehilangan, pindah, gagal)"
                        ]
                    },
                    {
                        title: "C. Tanda dan Gejala Stres",
                        subtitle: "Fisik:",
                        items: [
                            "Sakit kepala, nyeri otot",
                            "Gangguan tidur",
                            "Jantung berdebar",
                            "Mudah lelah"
                        ],
                        subtitle2: "Psikologis:",
                        items2: [
                            "Mudah marah",
                            "Cemas berlebihan",
                            "Sulit konsentrasi",
                            "Perasaan tertekan"
                        ],
                        subtitle3: "Perilaku:",
                        items3: [
                            "Menarik diri",
                            "Perubahan pola makan",
                            "Prokrastinasi",
                            "Konsumsi rokok/kafein berlebihan"
                        ]
                    },
                    {
                        title: "D. Cara Mengelola Stres",
                        items: [
                            "Manajemen waktu (prioritas kegiatan)",
                            "Relaksasi (napas dalam, meditasi, doa)",
                            "Aktivitas fisik ringan (jalan kaki, stretching)",
                            "Berbagi cerita dengan orang terpercaya",
                            "Tidur cukup dan pola makan sehat",
                            "Mengenal batas diri (tidak memaksakan diri)"
                        ]
                    }
                ]
            },
            {
                title: "😢 Mengenai Depresi",
                sections: [
                    {
                        title: "A. Pengertian Depresi",
                        content: "Depresi adalah gangguan suasana hati yang ditandai dengan perasaan sedih mendalam, kehilangan minat, dan gangguan fungsi sehari-hari dalam waktu lama (≥ 2 minggu)."
                    },
                    {
                        title: "B. Tanda dan Gejala Depresi",
                        items: [
                            "Perasaan sedih terus-menerus",
                            "Kehilangan minat atau kesenangan",
                            "Mudah lelah, energi menurun",
                            "Gangguan tidur (insomnia/hipersomnia)",
                            "Perasaan tidak berharga atau bersalah",
                            "Sulit konsentrasi",
                            "Pikiran tentang kematian atau bunuh diri ⚠️"
                        ]
                    },
                    {
                        title: "C. Faktor Risiko",
                        items: [
                            "Riwayat depresi keluarga",
                            "Stres berkepanjangan",
                            "Trauma atau kehilangan",
                            "Penyakit kronis",
                            "Kurangnya dukungan sosial"
                        ]
                    },
                    {
                        title: "D. Kapan Harus Mencari Bantuan?",
                        items: [
                            "Gejala berlangsung > 2 minggu",
                            "Mengganggu aktivitas sehari-hari",
                            "Muncul pikiran menyakiti diri"
                        ]
                    }
                ]
            },
            {
                title: "💅 Self-Care (Perawatan Diri)",
                sections: [
                    {
                        title: "A. Pengertian Self-Care",
                        content: "Self-care adalah upaya sadar untuk menjaga kesehatan fisik, mental, dan emosional agar tetap seimbang dan optimal."
                    },
                    {
                        title: "B. Jenis-Jenis Self-Care",
                        subtitle: "1. Physical Self-Care",
                        items: [
                            "Tidur cukup",
                            "Makan bergizi",
                            "Olahraga teratur",
                            "Minum air putih cukup"
                        ],
                        subtitle2: "2. Emotional Self-Care",
                        items2: [
                            "Mengungkapkan perasaan",
                            "Menetapkan batasan",
                            "Menghargai diri sendiri"
                        ],
                        subtitle3: "3. Mental Self-Care",
                        items3: [
                            "Membaca buku",
                            "Mengurangi overthinking",
                            "Istirahat dari media sosial"
                        ],
                        subtitle4: "4. Social Self-Care",
                        items4: [
                            "Menjaga hubungan positif",
                            "Quality time dengan keluarga/teman"
                        ],
                        subtitle5: "5. Spiritual Self-Care",
                        items5: [
                            "Berdoa",
                            "Meditasi",
                            "Refleksi diri"
                        ]
                    },
                    {
                        title: "C. Contoh Aktivitas Self-Care Harian",
                        items: [
                            "Menarik napas dalam 5 menit",
                            "Menulis jurnal 3 hal yang disyukuri",
                            "Berjalan santai 15-30 menit",
                            "Tidur tanpa gadget",
                            "Melakukan hobi"
                        ]
                    }
                ]
            },
            {
                title: "😟 Anxiety (Gangguan Kecemasan)",
                sections: [
                    {
                        title: "A. Pengertian Anxiety",
                        content: "Anxiety adalah rasa takut atau khawatir berlebihan yang sulit dikendalikan dan tidak sebanding dengan situasi yang dihadapi."
                    },
                    {
                        title: "B. Gejala Anxiety",
                        subtitle: "Fisik:",
                        items: [
                            "Jantung berdebar",
                            "Sesak napas",
                            "Tangan gemetar",
                            "Berkeringat berlebihan"
                        ],
                        subtitle2: "Psikologis:",
                        items2: [
                            "Pikiran negatif berulang",
                            "Sulit fokus",
                            "Perasaan tidak aman",
                            "Takut kehilangan kontrol"
                        ]
                    },
                    {
                        title: "C. Penyebab Anxiety",
                        items: [
                            "Tekanan hidup",
                            "Pengalaman traumatis",
                            "Overthinking",
                            "Kurang istirahat",
                            "Konsumsi kafein berlebih"
                        ]
                    },
                    {
                        title: "D. Cara Mengatasi Anxiety",
                        items: [
                            "Teknik pernapasan (deep breathing)",
                            "Grounding technique (5-4-3-2-1)",
                            "Olahraga ringan",
                            "Membatasi kafein",
                            "Menulis jurnal perasaan",
                            "Konseling psikolog bila perlu"
                        ]
                    }
                ]
            }
        ];

        function openModal(index) {
            const content = educationContent[index];
            document.getElementById('modalTitle').textContent = content.title;

            let html = '';
            content.sections.forEach(section => {
                html += `<div class="modal-section">`;
                html += `<div class="modal-section-title">${section.title}</div>`;
                
                if (section.content) {
                    html += `<div class="modal-section-content">${section.content}</div>`;
                }

                if (section.items) {
                    html += `<div class="modal-section-content"><ul>`;
                    section.items.forEach(item => {
                        html += `<li>${item}</li>`;
                    });
                    html += `</ul></div>`;
                }

                if (section.subtitle && section.items) {
                    html += `<div class="modal-section-content"><strong>${section.subtitle}</strong><ul>`;
                    section.items.forEach(item => {
                        html += `<li>${item}</li>`;
                    });
                    html += `</ul></div>`;
                }

                if (section.subtitle2 && section.items2) {
                    html += `<div class="modal-section-content"><strong>${section.subtitle2}</strong><ul>`;
                    section.items2.forEach(item => {
                        html += `<li>${item}</li>`;
                    });
                    html += `</ul></div>`;
                }

                if (section.subtitle3 && section.items3) {
                    html += `<div class="modal-section-content"><strong>${section.subtitle3}</strong><ul>`;
                    section.items3.forEach(item => {
                        html += `<li>${item}</li>`;
                    });
                    html += `</ul></div>`;
                }

                if (section.subtitle4 && section.items4) {
                    html += `<div class="modal-section-content"><strong>${section.subtitle4}</strong><ul>`;
                    section.items4.forEach(item => {
                        html += `<li>${item}</li>`;
                    });
                    html += `</ul></div>`;
                }

                if (section.subtitle5 && section.items5) {
                    html += `<div class="modal-section-content"><strong>${section.subtitle5}</strong><ul>`;
                    section.items5.forEach(item => {
                        html += `<li>${item}</li>`;
                    });
                    html += `</ul></div>`;
                }

                html += `</div>`;
            });

            document.getElementById('modalContent').innerHTML = html;
            // show modal and lock body scroll so touchpad/touch scroll targets the modal
            document.getElementById('educationModal').classList.add('active');
            document.body.style.overflow = 'hidden';

            const dlg = document.getElementById('modalDialog');
            if (dlg) {
                // focus without scrolling the page
                dlg.focus({ preventScroll: true });
            }
            // add scroll forwarding handlers to ensure touchpad/touch scrolls modal
            _addScrollForwarding();
        }

        function closeModal() {
            document.getElementById('educationModal').classList.remove('active');
            // restore body scrolling
            document.body.style.overflow = '';
            // remove fallback handlers
            _removeScrollForwarding();
        }

        document.getElementById('educationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // --- Wheel & Touch fallback handlers so touchpad/touch scrolls the modal reliably ---
        let _wheelHandler = null;
        let _touchStartY = null;
        let _touchMoveHandler = null;
        let _touchStartHandler = null;

        function _addScrollForwarding() {
            const dlg = document.getElementById('modalDialog');
            if (!dlg) return;

            // wheel handler (for touchpad two-finger scroll)
            _wheelHandler = function(e) {
                // check if modal can scroll in the direction
                const canScrollUp = dlg.scrollTop > 0;
                const canScrollDown = dlg.scrollTop + dlg.clientHeight < dlg.scrollHeight;
                if (canScrollUp || canScrollDown) {
                    dlg.scrollTop += e.deltaY;
                    e.preventDefault();
                }
            };

            // touch handlers (for touchscreen)
            _touchStartHandler = function(e) {
                if (e.touches && e.touches.length === 1) {
                    _touchStartY = e.touches[0].clientY;
                }
            };

            _touchMoveHandler = function(e) {
                if (!_touchStartY || !e.touches || e.touches.length !== 1) return;
                const currentY = e.touches[0].clientY;
                const delta = _touchStartY - currentY;
                // apply scroll into dialog
                dlg.scrollTop += delta;
                _touchStartY = currentY;
                e.preventDefault();
            };

            // attach listeners on document so gestures are captured even if pointer is not exactly over dialog
            document.addEventListener('wheel', _wheelHandler, { passive: false });
            document.addEventListener('touchstart', _touchStartHandler, { passive: false });
            document.addEventListener('touchmove', _touchMoveHandler, { passive: false });
        }

        function _removeScrollForwarding() {
            if (_wheelHandler) document.removeEventListener('wheel', _wheelHandler);
            if (_touchStartHandler) document.removeEventListener('touchstart', _touchStartHandler);
            if (_touchMoveHandler) document.removeEventListener('touchmove', _touchMoveHandler);
            _wheelHandler = null; _touchStartHandler = null; _touchMoveHandler = null; _touchStartY = null;
        }
    </script>
</body>
</html>
