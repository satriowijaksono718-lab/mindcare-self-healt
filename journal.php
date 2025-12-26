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

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Verify user exists in database
$check_sql = "SELECT id FROM users WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    setError("User tidak ditemukan. Silakan login kembali.");
    header("Location: " . BASE_URL . "login.php");
    exit();
}
$check_stmt->close();

// Handle journal submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $mood = sanitize($_POST['mood'] ?? '');

    if (empty($title) || empty($content)) {
        setError("Judul dan isi jurnal harus diisi!");
    } else {
        $sql = "INSERT INTO journals (user_id, title, content, mood) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            setError("Database error: " . $conn->error);
        } else {
            $stmt->bind_param("isss", $user_id, $title, $content, $mood);
            
            if ($stmt->execute()) {
                setSuccess("Jurnal berhasil disimpan!");
                $title = '';
                $content = '';
                $mood = '';
            } else {
                setError("Gagal menyimpan jurnal: " . $stmt->error);
            }
            $stmt->close();
        }
    }
}

$error = getError();
$success = getSuccess();

// Get user's journal entries
$journals = [];
$sql = "SELECT * FROM journals WHERE user_id = ? ORDER BY created_at DESC LIMIT 50";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $journals[] = $row;
    }
    $stmt->close();
} else {
    setError("Database error: Tabel journals belum dibuat. Silakan jalankan database.sql terlebih dahulu.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self Help Journal - Mind Care</title>
    
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
            font-size: 42px;
            font-weight: 900;
            color: #1a2332;
            margin-bottom: 15px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #666;
            font-weight: 500;
        }

        /* Card Glass Style */
        .card-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.08);
            border: 1px solid rgba(139, 92, 246, 0.1);
            transition: all 0.3s ease;
        }

        .card-glass:hover {
            box-shadow: 0 25px 70px rgba(139, 92, 246, 0.12);
            transform: translateY(-2px);
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            align-items: start;
        }

        /* Form Section */
        .form-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .form-title {
            font-size: 22px;
            font-weight: 800;
            color: #1a2332;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .day-badge {
            background: linear-gradient(135deg, #a0dfe8 0%, #8ed8e3 100%);
            color: #1a2332;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(160, 223, 232, 0.2);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 700;
            color: #1a2332;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            padding: 14px 18px;
            border: 2px solid rgba(26, 35, 50, 0.08);
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.5);
        }

        .form-control:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.12);
            background: white;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 160px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 20px;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #a0dfe8 0%, #8ed8e3 100%);
            color: #1a2332;
            box-shadow: 0 8px 20px rgba(160, 223, 232, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(160, 223, 232, 0.3);
        }

        .btn-reset {
            background: rgba(26, 35, 50, 0.08);
            color: #1a2332;
            border: 2px solid rgba(26, 35, 50, 0.12);
        }

        .btn-reset:hover {
            background: rgba(26, 35, 50, 0.12);
            border-color: rgba(26, 35, 50, 0.2);
        }

        /* Journal List Section */
        .list-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .list-title {
            font-size: 22px;
            font-weight: 800;
            color: #1a2332;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .journal-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            max-height: 650px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .journal-list::-webkit-scrollbar {
            width: 6px;
        }

        .journal-list::-webkit-scrollbar-track {
            background: rgba(26, 35, 50, 0.04);
            border-radius: 10px;
        }

        .journal-list::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.2);
            border-radius: 10px;
        }

        .journal-list::-webkit-scrollbar-thumb:hover {
            background: rgba(139, 92, 246, 0.4);
        }

        .journal-item {
            padding: 16px;
            background: linear-gradient(135deg, rgba(160, 223, 232, 0.08) 0%, rgba(139, 92, 246, 0.06) 100%);
            border: 2px solid rgba(139, 92, 246, 0.08);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .journal-item:hover {
            background: linear-gradient(135deg, rgba(160, 223, 232, 0.15) 0%, rgba(139, 92, 246, 0.12) 100%);
            border-color: rgba(139, 92, 246, 0.15);
            transform: translateX(6px);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.1);
        }

        .journal-item-title {
            font-weight: 700;
            color: #1a2332;
            margin-bottom: 6px;
            font-size: 15px;
        }

        .journal-item-meta {
            display: flex;
            gap: 12px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .journal-item-date {
            font-size: 12px;
            color: #888;
            font-weight: 600;
        }

        .journal-item-mood {
            display: inline-block;
            background: linear-gradient(135deg, #a0dfe8 0%, #8ed8e3 100%);
            color: #1a2332;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .journal-item-preview {
            color: #666;
            font-size: 13px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .empty-state {
            text-align: center;
            padding: 60px 30px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.4;
        }

        .empty-state-text {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-state-subtext {
            font-size: 13px;
            color: #aaa;
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 24px;
            font-size: 14px;
            border: 2px solid transparent;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border-color: rgba(76, 175, 80, 0.2);
        }

        .alert-danger {
            background: rgba(244, 67, 54, 0.1);
            color: #c62828;
            border-color: rgba(244, 67, 54, 0.2);
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
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease;
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
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.3s ease;
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
            font-size: 24px;
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

        .modal-meta {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .modal-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #888;
            font-weight: 600;
        }

        .modal-content {
            font-size: 15px;
            line-height: 1.8;
            color: #333;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Error message */
        .text-danger {
            display: block;
            font-size: 12px;
            color: #c62828 !important;
            margin-top: 4px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }

            .journal-list {
                max-height: 400px;
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

            .card-glass {
                padding: 24px;
            }

            .form-title, .list-title {
                font-size: 18px;
            }

            .button-group {
                grid-template-columns: 1fr;
            }

            .modal-dialog {
                padding: 28px;
                max-width: 90%;
            }

            .modal-title {
                font-size: 20px;
            }

            textarea.form-control {
                min-height: 120px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 24px;
            }

            .card-glass {
                padding: 16px;
            }

            .form-title, .list-title {
                font-size: 16px;
            }

            .btn {
                padding: 12px 20px;
                font-size: 13px;
            }

            .journal-list {
                max-height: 300px;
            }

            .modal-dialog {
                padding: 20px;
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
                <h1 class="page-title">📖 Self Help Journal</h1>
                <p class="page-subtitle">Tulis perasaan dan pikiran Anda untuk perjalanan self-healing</p>
            </div>

            <!-- Alerts -->
            <?php if ($error): ?>
                <div class="alert alert-danger" data-aos="fade-up">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success" data-aos="fade-up">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Content -->
            <div class="content-wrapper">
                <!-- Journal Form Section -->
                <div class="form-section">
                    <div class="card-glass" data-aos="fade-up">
                        <div class="form-title">
                            <span class="day-badge" id="dayBadge">HARI INI</span>
                        </div>

                        <form method="POST" action="" id="journalForm">
                            <div class="form-group">
                                <label class="form-label">Judul Jurnal</label>
                                <input type="text" class="form-control" name="title" placeholder="Berikan judul untuk jurnal Anda..." id="titleInput">
                                <small class="text-danger" id="titleError"></small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Apa yang Anda rasakan hari ini?</label>
                                <textarea class="form-control" name="content" placeholder="Tulis perasaan dan pikiran Anda..." id="contentInput"></textarea>
                                <small class="text-danger" id="contentError"></small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Pilih Mood</label>
                                <select class="form-control" name="mood" id="moodInput">
                                    <option value="">-- Pilih Mood --</option>
                                    <option value="😊 Happy">😊 Happy</option>
                                    <option value="😢 Sad">😢 Sad</option>
                                    <option value="😠 Angry">😠 Angry</option>
                                    <option value="😰 Anxious">😰 Anxious</option>
                                    <option value="😌 Calm">😌 Calm</option>
                                    <option value="🤩 Excited">🤩 Excited</option>
                                    <option value="😴 Tired">😴 Tired</option>
                                    <option value="😐 Neutral">😐 Neutral</option>
                                </select>
                            </div>

                            <div class="button-group">
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <button type="reset" class="btn btn-reset">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Journal List Section -->
                <div class="list-section">
                    <div class="card-glass" data-aos="fade-up" data-aos-delay="100">
                        <div class="list-title">
                            <i class="fas fa-book"></i> Jurnal Saya
                        </div>

                        <?php if (empty($journals)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">📝</div>
                                <div class="empty-state-text">Belum ada jurnal</div>
                                <div class="empty-state-subtext">Mulai tulis jurnal pertama Anda hari ini untuk memulai perjalanan self-healing</div>
                            </div>
                        <?php else: ?>
                            <div class="journal-list">
                                <?php foreach ($journals as $journal): ?>
                                    <div class="journal-item" onclick="viewJournal(<?php echo $journal['id']; ?>, '<?php echo htmlspecialchars(addslashes($journal['title']), ENT_QUOTES); ?>', '<?php echo htmlspecialchars(addslashes($journal['content']), ENT_QUOTES); ?>', '<?php echo htmlspecialchars($journal['mood']); ?>', '<?php echo $journal['created_at']; ?>')" data-aos="fade-up">
                                        <div class="journal-item-title"><?php echo htmlspecialchars($journal['title']); ?></div>
                                        <div class="journal-item-meta">
                                            <span class="journal-item-date">
                                                <i class="fas fa-calendar-alt"></i> <?php echo date('d M Y, H:i', strtotime($journal['created_at'])); ?>
                                            </span>
                                            <?php if (!empty($journal['mood'])): ?>
                                                <span class="journal-item-mood"><?php echo htmlspecialchars($journal['mood']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="journal-item-preview"><?php echo htmlspecialchars(substr($journal['content'], 0, 120)); ?>...</div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing journal details -->
    <div class="modal-custom" id="journalModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle"></h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-meta">
                <div class="modal-meta-item">
                    <i class="fas fa-calendar"></i>
                    <span id="modalDate"></span>
                </div>
                <div class="modal-meta-item" id="modalMoodContainer"></div>
            </div>
            <div class="modal-content" id="modalContent"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({ duration: 700, once: true });

        // Set day badge
        const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
        const today = new Date();
        document.getElementById('dayBadge').textContent = days[today.getDay()];

        // Form Validation
        const journalForm = document.getElementById('journalForm');
        const titleInput = document.getElementById('titleInput');
        const contentInput = document.getElementById('contentInput');

        function validateForm() {
            let isValid = true;
            document.getElementById('titleError').textContent = '';
            document.getElementById('contentError').textContent = '';

            if (!titleInput.value.trim()) {
                document.getElementById('titleError').textContent = 'Judul tidak boleh kosong';
                isValid = false;
            } else if (titleInput.value.length < 3) {
                document.getElementById('titleError').textContent = 'Judul minimal 3 karakter';
                isValid = false;
            }

            if (!contentInput.value.trim()) {
                document.getElementById('contentError').textContent = 'Isi jurnal tidak boleh kosong';
                isValid = false;
            } else if (contentInput.value.length < 10) {
                document.getElementById('contentError').textContent = 'Isi jurnal minimal 10 karakter';
                isValid = false;
            }

            return isValid;
        }

        journalForm.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });

        // Real-time validation
        titleInput.addEventListener('blur', validateForm);
        contentInput.addEventListener('blur', validateForm);

        // Modal functions
        function viewJournal(id, title, content, mood, date) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalContent').textContent = content;
            document.getElementById('modalDate').textContent = new Date(date).toLocaleString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const moodContainer = document.getElementById('modalMoodContainer');
            if (mood) {
                moodContainer.innerHTML = '<span style="font-size:16px; font-weight:700;">' + htmlEscape(mood) + '</span>';
            } else {
                moodContainer.innerHTML = '';
            }

            document.getElementById('journalModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('journalModal').classList.remove('active');
        }

        function htmlEscape(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        // Close modal when clicking outside
        document.getElementById('journalModal').addEventListener('click', function(e) {
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
    </script>
</body>
</html>
