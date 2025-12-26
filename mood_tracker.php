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

// Handle mood submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_moods = isset($_POST['moods']) ? $_POST['moods'] : [];
    
    if (!empty($selected_moods)) {
        // Save moods to database
        $mood_string = implode(',', $selected_moods);

        // Optional: allow saving for a specific date (edit by date)
        $save_date = isset($_POST['date']) && !empty($_POST['date']) ? $_POST['date'] : null;

        // If date provided, remove existing entries for that date for this user
        if ($save_date) {
            $del_sql = "DELETE FROM mood_tracker WHERE user_id = ? AND DATE(created_at) = ?";
            $del_stmt = $conn->prepare($del_sql);
            if ($del_stmt) {
                $del_stmt->bind_param("is", $user_id, $save_date);
                $del_stmt->execute();
                $del_stmt->close();
            }
        }

        if ($save_date) {
            $sql = "INSERT INTO mood_tracker (user_id, mood, intensity, created_at) VALUES (?, ?, ?, ?)";
        } else {
            $sql = "INSERT INTO mood_tracker (user_id, mood, intensity) VALUES (?, ?, ?)";
        }

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            setError("Database error: " . $conn->error);
        } else {
            $intensity = count($selected_moods);
            if ($save_date) {
                // Bind with created_at
                $stmt->bind_param("issi", $user_id, $mood_string, $intensity, $save_date);
            } else {
                $stmt->bind_param("isi", $user_id, $mood_string, $intensity);
            }

            if ($stmt->execute()) {
                setSuccess("Mood tracker berhasil disimpan!");
                // Reset form
                $selected_moods = [];
            } else {
                setError("Gagal menyimpan mood tracker: " . $stmt->error);
            }
            $stmt->close();
        }
    }
}

// Simple AJAX endpoints for frontend: summary, entries, notes
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    if ($action === 'summary') {
        // Return daily counts and avg intensity for the user
        $sql = "SELECT DATE(created_at) as d, COUNT(*) as cnt, AVG(intensity) as avg_int FROM mood_tracker WHERE user_id = ? GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[$row['d']] = ['count' => (int)$row['cnt'], 'avg' => (float)$row['avg_int']];
        }
        echo json_encode($out);
        exit();
    }

    if ($action === 'entries' && isset($_GET['date'])) {
        $date = $_GET['date'];
        $sql = "SELECT id, mood, intensity, created_at FROM mood_tracker WHERE user_id = ? AND DATE(created_at) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $date);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[] = $row;
        }
        echo json_encode($out);
        exit();
    }

    if ($action === 'save_note' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $note_date = $_POST['date'] ?? null;
        $note_text = $_POST['note'] ?? '';
        if (!$note_date) { echo json_encode(['ok' => false, 'msg' => 'Missing date']); exit(); }

        // Ensure table exists
        $conn->query("CREATE TABLE IF NOT EXISTS mood_notes (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, note_date DATE NOT NULL, note TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY ux_user_date (user_id, note_date)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Upsert
        $up_sql = "INSERT INTO mood_notes (user_id, note_date, note) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE note = VALUES(note)";
        $up_stmt = $conn->prepare($up_sql);
        $up_stmt->bind_param("iss", $user_id, $note_date, $note_text);
        $ok = $up_stmt->execute();
        echo json_encode(['ok' => (bool)$ok]);
        exit();
    }

    if ($action === 'get_note' && isset($_GET['date'])) {
        $n_date = $_GET['date'];
        $sql = "SELECT note FROM mood_notes WHERE user_id = ? AND note_date = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $n_date);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        echo json_encode(['note' => $row['note'] ?? '']);
        exit();
    }

    // default
    echo json_encode(['ok' => false, 'msg' => 'Unknown action']);
    exit();
}

$error = getError();
$success = getSuccess();

// Mood definitions
$moods = [
    'happy' => ['name' => 'HAPPY', 'color' => '#f4a5a5'],
    'nervous' => ['name' => 'NERVOUS', 'color' => '#c084fc'],
    'productive' => ['name' => 'PRODUCTIVE', 'color' => '#ff9a56'],
    'sick' => ['name' => 'SICK', 'color' => '#4a7c59'],
    'sad' => ['name' => 'SAD', 'color' => '#4f63ff'],
    'tired' => ['name' => 'TIRED', 'color' => '#d0d0d0'],
    'angry' => ['name' => 'ANGRY', 'color' => '#d42426']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Tracker - Mind Care</title>
    <!-- Bootstrap, FontAwesome, AOS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(180deg, #f5f0ff 0%, #f6f7fb 100%);
            min-height: 100vh;
            color: #1a2332;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(139, 92, 246, 0.15);
            padding: 1.2rem 2rem;
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 60;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 800;
            color: #1a2332;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .logo:hover {
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

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            text-align: center;
            font-size: 36px;
            font-weight: 900;
            color: #1a2332;
            margin-bottom: 30px;
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1200px) {
            .content-wrapper {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        .card-glass {
            background: rgba(255,255,255,0.95);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(139,92,246,0.07);
            border: 1px solid rgba(139,92,246,0.06);
        }

        .puzzle-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0;
            border: 3px solid rgba(26,35,50,0.08);
            background: transparent;
            padding: 6px;
            border-radius: 8px;
        }

        .puzzle-piece {
            aspect-ratio: 1;
            background: white;
            border: 2px solid rgba(26,35,50,0.06);
            cursor: pointer;
            position: relative;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .puzzle-piece:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(26,35,50,0.08);
        }

        .puzzle-piece.selected {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 18px 40px rgba(0,0,0,0.08);
            border-color: rgba(0,0,0,0.06);
        }

        .legend-container {
            /* reuse glass card */
        }

        .legend-title {
            font-size: 18px;
            font-weight: 800;
            color: #1a2332;
            margin-bottom: 18px;
        }

        .legend-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        /* Calendar Heatmap */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            margin-bottom: 12px;
        }

        .calendar-day {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: #fff;
            border: 1px solid rgba(26,35,50,0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #333;
            cursor: pointer;
            transition: transform 0.12s ease;
        }

        .calendar-day:hover { transform: translateY(-4px); }

        .heat-legend { display:flex; gap:8px; align-items:center; margin-top:8px; }

        #trendChart { width:100%; height:160px; }

        #noteArea { width:100%; min-height:90px; border-radius:8px; border:1px solid rgba(26,35,50,0.06); padding:10px; }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mood-box {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            flex-shrink: 0;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            border: 3px solid transparent;
        }

        .mood-box:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,0.06); }

        .mood-box.selected { outline: 4px solid rgba(139,92,246,0.12); transform: translateY(-4px); }

        .mood-label { font-size: 13px; font-weight: 700; color: #1a2332; }

        .button-group {
            margin-top: 40px;
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit {
            background: linear-gradient(135deg, #a0dfe8 0%, #8ed8e3 100%);
            color: #1a2332;
            flex: 1;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(160, 223, 232, 0.3);
        }

        .btn-reset {
            background: #f0f0f0;
            color: #1a2332;
            flex: 1;
        }

        .btn-reset:hover {
            background: #e0e0e0;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }

        .alert-danger {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .selected-count {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .mb-3 { margin-bottom: 20px; }

        .mood-legend {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 28px;
                margin-bottom: 20px;
            }

            .puzzle-grid {
                grid-template-columns: repeat(5, 1fr);
            }

            .mood-legend {
                grid-template-columns: 1fr 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                padding: 14px 20px;
                font-size: 15px;
            }
        }

        @media (max-width: 1024px) {
            .page-title {
                font-size: 28px;
                margin-bottom: 30px;
            }

            .puzzle-grid {
                grid-template-columns: repeat(5, 1fr);
            }

            .legend-grid {
                grid-template-columns: 1fr;
            }

            .puzzle-container,
            .legend-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php" class="logo">
            <div class="logo-icon">⊙</div>
            <span>Mind Care</span>
        </a>
        <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
    </div>

    <div class="container">
        <h1 class="page-title">Mood Tracker</h1>

        <?php
        if ($error) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
        }
        if ($success) {
            echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
        }
        ?>

        <form method="POST" action="">
            <div class="content-wrapper">
                <!-- Left Column: Mood Picker & Puzzle Grid -->
                <div>
                    <!-- Pilih Mood Legend -->
                    <div class="card-glass mb-3" data-aos="fade-up">
                        <div class="legend-title">📊 Pilih Mood Anda:</div>
                        <div class="mood-legend">
                            <?php
                            foreach ($moods as $mood_key => $mood_data) {
                                echo '<div class="legend-item">';
                                echo '<div class="mood-box" style="background-color: ' . $mood_data['color'] . ';" onclick="selectMood(\'' . $mood_key . '\', this)"></div>';
                                echo '<span class="mood-label">' . $mood_data['name'] . '</span>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Puzzle Grid -->
                    <div class="card-glass" data-aos="fade-up" data-aos-delay="100">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <strong>Grid Mood Harian (7x6)</strong>
                            <small style="color:#666;">Dipilih: <span id="selectedCount">0</span>/42</small>
                        </div>
                        <div class="puzzle-grid" id="puzzleGrid">
                            <?php
                            // Create 42 puzzle pieces (7x6)
                            for ($i = 0; $i < 42; $i++) {
                                echo '<div class="puzzle-piece" data-index="' . $i . '" onclick="togglePuzzle(this)"></div>';
                            }
                            ?>
                        </div>

                        <div class="button-group" style="margin-top:16px;">
                            <button type="submit" class="btn btn-submit">💾 SIMPAN MOOD</button>
                            <button type="button" class="btn btn-reset" onclick="resetPuzzles()">🔄 RESET</button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary, Calendar, Notes -->
                <div>
                    <!-- Mood Summary Widget -->
                    <div class="card-glass mb-3" data-aos="fade-up">
                        <div style="margin-bottom:8px;">
                            <strong style="font-size:15px;">📈 Ringkasan Mood</strong>
                        </div>
                        <div id="pageMoodMini" style="display:flex; gap:6px; margin-bottom:8px; flex-wrap:wrap;"></div>
                        <div id="pageMoodLast" style="color:#666; font-weight:700; font-size:13px;"></div>
                    </div>

                    <!-- Calendar Heatmap + Trend Chart -->
                    <div class="card-glass mb-3" data-aos="fade-up" data-aos-delay="100">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <strong style="font-size:15px;">📅 Kalender (Bulan Ini)</strong>
                        </div>
                        <div id="calendarHeatmap" class="calendar-grid"></div>
                        <div class="heat-legend" style="margin-top:10px;">
                            <small>Rendah</small>
                            <div style="width:60px; height:10px; background:linear-gradient(90deg,#f0f0f0,#ffd6d6,#ff9a56); border-radius:4px;"></div>
                            <small>Tinggi</small>
                        </div>
                        <canvas id="trendChart" style="margin-top:12px; height:140px;"></canvas>
                    </div>

                    <!-- Daily Notes -->
                    <div class="card-glass mb-3" data-aos="fade-up" data-aos-delay="200">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <strong style="font-size:15px;">📝 Catatan Harian</strong>
                            <small id="noteDateLabel" style="color:#8b5cf6; font-weight:700;"></small>
                        </div>
                        <textarea id="noteArea" placeholder="Tulis catatan untuk tanggal yang Anda pilih..." style="min-height:100px; font-size:14px;"></textarea>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; margin-top:10px;">
                            <button type="button" class="btn btn-submit" id="saveNoteBtn">💾 Simpan</button>
                            <button type="button" class="btn btn-reset" id="clearNoteBtn">🗑️ Bersihkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let selectedMood = null;
        let selectedMoodsSet = new Set();

        const moods = {
            'happy': '#f4a5a5',
            'nervous': '#c084fc',
            'productive': '#ff9a56',
            'sick': '#4a7c59',
            'sad': '#4f63ff',
            'tired': '#d0d0d0',
            'angry': '#d42426'
        };

        function selectMood(mood, elem) {
            selectedMood = mood;
            // Visual feedback: mark selected box
            document.querySelectorAll('.mood-box').forEach(box => box.classList.remove('selected'));
            if (elem) elem.classList.add('selected');
        }

        function togglePuzzle(element) {
            const index = element.dataset.index;

            if (!selectedMood) {
                alert('Pilih mood terlebih dahulu!');
                return;
            }

            const color = moods[selectedMood];
            const moodKey = selectedMood + '_' + index;

            if (selectedMoodsSet.has(moodKey)) {
                // Unselect
                selectedMoodsSet.delete(moodKey);
                element.style.background = 'white';
                element.classList.remove('selected');
            } else {
                // Select
                selectedMoodsSet.add(moodKey);
                element.style.background = color;
                element.classList.add('selected');
            }

            updateSelectedCount();
        }

        function updateSelectedCount() {
            document.getElementById('selectedCount').textContent = selectedMoodsSet.size;
        }

        function resetPuzzles() {
            selectedMoodsSet.clear();
            document.querySelectorAll('.puzzle-piece').forEach(piece => {
                piece.style.background = 'white';
                piece.style.color = 'inherit';
            });
            updateSelectedCount();
        }

        // Add hidden inputs for form submission (AJAX save)
        document.querySelector('form').addEventListener('submit', async function(e) {
            e.preventDefault();
            // Get unique moods from selectedMoodsSet
            const uniqueMoods = new Set();
            selectedMoodsSet.forEach(item => {
                const mood = item.split('_')[0];
                uniqueMoods.add(mood);
            });

            if (uniqueMoods.size === 0) {
                showToast('Pilih minimal satu mood!', true);
                return;
            }

            // Prepare form data
            const fd = new FormData();
            uniqueMoods.forEach(mood => fd.append('moods[]', mood));

            // If a date is selected in the note area, include it so we save/edit that date
            const dateLabel = document.getElementById('noteDateLabel').textContent;
            if (dateLabel && dateLabel.trim() !== '') {
                fd.append('date', dateLabel.trim());
            }

            // Send AJAX POST to save
            try {
                const resp = await fetch(window.location.pathname, { method: 'POST', body: fd });
                // server will redirect when normal form used; here expect JSON via headers not set, but we check success via page reload fallback
                // If server set success message via session, reload to show it; otherwise fetch summary and update UI
                await fetchAndRender();
                showToast('Mood tersimpan');
                // clear selection after save
                resetPuzzles();
            } catch (err) {
                console.error(err);
                showToast('Gagal menyimpan mood', true);
            }
        });
    </script>

    <!-- Chart.js and app scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Fetch summary and render calendar heatmap
        async function fetchSummary() {
            const res = await fetch('mood_tracker.php?action=summary');
            return res.json();
        }

        function colorForAvg(avg) {
            // map 0..5 approx to color scale
            if (!avg) return '#f0f0f0';
            if (avg < 1.5) return '#ffe9e9';
            if (avg < 2.5) return '#ffd6d6';
            if (avg < 3.5) return '#ffb3b3';
            if (avg < 4.5) return '#ff9a56';
            return '#ff4d4d';
        }

        function renderCalendar(summary) {
            const container = document.getElementById('calendarHeatmap');
            container.innerHTML = '';
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth();
            const first = new Date(year, month, 1);
            const last = new Date(year, month + 1, 0);
            const startWeekDay = first.getDay();
            // pad empty slots
            for (let i = 0; i < startWeekDay; i++) {
                const d = document.createElement('div'); d.className = 'calendar-day'; d.style.visibility='hidden'; container.appendChild(d);
            }
            for (let d = 1; d <= last.getDate(); d++) {
                const dateStr = year + '-' + String(month+1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
                const info = summary[dateStr] || null;
                const el = document.createElement('div');
                el.className = 'calendar-day';
                el.dataset.date = dateStr;
                el.textContent = d;
                if (info) el.style.background = colorForAvg(info.avg);
                el.addEventListener('click', () => onSelectDate(dateStr, el));
                container.appendChild(el);
            }
        }

        async function fetchAndRender() {
            const summary = await fetchSummary();
            renderCalendar(summary);
            renderTrendChart(summary);
        }

        let trendChart = null;
        function renderTrendChart(summary) {
            // prepare last 14 days
            const labels = [];
            const data = [];
            for (let i = 13; i >= 0; i--) {
                const dt = new Date(); dt.setDate(dt.getDate() - i);
                const key = dt.getFullYear() + '-' + String(dt.getMonth()+1).padStart(2,'0') + '-' + String(dt.getDate()).padStart(2,'0');
                labels.push(String(dt.getDate()) + '/' + String(dt.getMonth()+1));
                const v = summary[key] ? summary[key].avg : 0;
                data.push(parseFloat(v.toFixed(2)));
            }
            const ctx = document.getElementById('trendChart').getContext('2d');
            if (trendChart) trendChart.destroy();
            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{ label: 'Avg Intensity', data: data, borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.12)', tension: 0.35, fill: true }]
                },
                options: { scales: { y: { beginAtZero: true, max: 6 } }, plugins: { legend: { display: false } } }
            });
        }

        // Date selection / load entries
        async function onSelectDate(dateStr, el) {
            document.querySelectorAll('.calendar-day').forEach(x=>x.style.outline='');
            el.style.outline = '3px solid rgba(139,92,246,0.12)';
            document.getElementById('noteDateLabel').textContent = dateStr;
            // fetch entries for that date and pre-select puzzle pieces
            const res = await fetch('mood_tracker.php?action=entries&date=' + dateStr);
            const items = await res.json();
            // clear previous selections
            resetPuzzles();
            // preselect mood boxes according to returned moods
            if (items && items.length) {
                // pick first entry moods
                const moodStr = items[0].mood || '';
                const moodsArr = moodStr.split(',');
                // select a mood in legend
                if (moodsArr.length) {
                    const selector = document.querySelector('.mood-box[onclick*="' + moodsArr[0] + '"]');
                    if (selector) selector.classList.add('selected');
                }
                // prefill tiles according to intensity
                const intensity = parseInt(items[0].intensity) || 0;
                if (intensity > 0) {
                    const pieces = Array.from(document.querySelectorAll('.puzzle-piece'));
                    for (let i = 0; i < Math.min(intensity, pieces.length); i++) {
                        const elp = pieces[i];
                        elp.style.background = moods[moodsArr[0]] || '#fff';
                        elp.classList.add('selected');
                        selectedMoodsSet.add((moodsArr[0] || 'mood') + '_' + elp.dataset.index);
                    }
                    updateSelectedCount();
                }
            }
            // load note
            const noteRes = await fetch('mood_tracker.php?action=get_note&date=' + dateStr);
            const noteJson = await noteRes.json();
            document.getElementById('noteArea').value = noteJson.note || '';
            // attach save handler to include date when saving main form
            document.getElementById('saveNoteBtn').onclick = async function() {
                const noteText = document.getElementById('noteArea').value;
                const fd = new FormData(); fd.append('date', dateStr); fd.append('note', noteText);
                const r = await fetch('mood_tracker.php?action=save_note', { method: 'POST', body: fd });
                const j = await r.json();
                if (j.ok) alert('Catatan disimpan'); else alert('Gagal menyimpan');
            };
        }

        document.getElementById('clearNoteBtn').addEventListener('click', () => document.getElementById('noteArea').value = '');

        // Initialize
        fetchAndRender();

        // Load and display mood summary on page load
        async function loadPageMoodSummary() {
            try {
                const res = await fetch('mood_tracker.php?action=summary');
                const data = await res.json();
                const keys = Object.keys(data).sort().reverse();
                const mini = document.getElementById('pageMoodMini');
                const last = document.getElementById('pageMoodLast');
                if (!mini) return;
                mini.innerHTML = '';
                // show last 7 entries (dates)
                const dates = keys.slice(0,7).reverse();
                dates.forEach(d => {
                    const el = document.createElement('div');
                    el.style.width = '32px'; el.style.height='32px'; el.style.borderRadius='8px'; 
                    el.style.background = data[d] && data[d].avg ? (data[d].avg<2? '#ffd6d6' : data[d].avg<3.5 ? '#ffb3b3' : '#ff9a56') : '#eee';
                    el.style.cursor = 'pointer';
                    el.title = d + ' — intensity: ' + (data[d] ? data[d].avg.toFixed(2) : '0');
                    el.addEventListener('click', () => { document.querySelector('[data-date="' + d + '"]')?.click(); });
                    mini.appendChild(el);
                });
                if (keys.length) {
                    const d = keys[0];
                    last.textContent = 'Terakhir: ' + d + ' — intensity ' + (data[d] ? data[d].avg.toFixed(2) : '—');
                } else {
                    last.textContent = 'Belum ada data mood.';
                }
            } catch (e) {
                console.error(e);
            }
        }
        loadPageMoodSummary();

        // Toast helper
        function showToast(msg, isError = false) {
            let t = document.getElementById('mct-toast');
            if (!t) {
                t = document.createElement('div');
                t.id = 'mct-toast';
                t.style.position = 'fixed';
                t.style.right = '20px';
                t.style.bottom = '20px';
                t.style.padding = '12px 16px';
                t.style.borderRadius = '10px';
                t.style.background = isError ? 'rgba(220,20,60,0.95)' : 'rgba(26,35,50,0.95)';
                t.style.color = '#fff';
                t.style.boxShadow = '0 10px 30px rgba(0,0,0,0.18)';
                t.style.opacity = '0';
                t.style.transition = 'opacity 0.25s ease';
                document.body.appendChild(t);
            }
            t.textContent = msg;
            t.style.background = isError ? 'rgba(220,20,60,0.95)' : 'rgba(26,35,50,0.95)';
            t.style.opacity = '1';
            setTimeout(() => { t.style.opacity = '0'; }, 3000);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 700, once: true });
    </script>
</body>
</html>
