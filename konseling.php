<?php
ob_start();
session_start();
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$doctors = [
    ['id' => 1, 'name' => 'Dr. Konselor', 'icon' => '👩‍⚕️', 'specialty' => 'Psikolog Klinis Profesional', 'desc' => 'Spesialis stress, anxiety, & masalah emosional'],
    ['id' => 2, 'name' => 'Dr. Ahmad', 'icon' => '👨‍⚕️', 'specialty' => 'Psikiater Bersertifikat', 'desc' => 'Ahli terapi kognitif & manajemen depresi'],
    ['id' => 3, 'name' => 'Dr. Siti', 'icon' => '👩‍⚕️', 'specialty' => 'Konselor Krisis & Trauma', 'desc' => 'Spesialis pemulihan trauma & krisis'],
    ['id' => 4, 'name' => 'Dr. Budi', 'icon' => '👨‍⚕️', 'specialty' => 'Terapis Relasi & Keluarga', 'desc' => 'Ahli masalah hubungan & keluarga'],
    ['id' => 5, 'name' => 'Dr. Rina', 'icon' => '👩‍⚕️', 'specialty' => 'Psikolog Anak & Remaja', 'desc' => 'Ahli anak & remaja']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konseling Profesional - Mind Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #8b5cf6;
            --accent: #00d4ff;
            --dark: #1a2332;
            --text: #666;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: linear-gradient(135deg, #f5f0ff 0%, #e8dff5 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        
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
        
        .bubble-1 { width: 400px; height: 400px; background: #fff; top: -100px; left: -100px; animation: float 15s ease-in-out infinite; }
        .bubble-2 { width: 300px; height: 300px; background: rgba(160, 223, 232, 0.4); top: 40%; right: -150px; animation: float 20s ease-in-out infinite 5s; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(50px); }
        }
        
        .navbar-custom {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(15px);
            padding: 1rem 2rem;
            z-index: 500;
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.1);
            border-bottom: 1px solid rgba(139, 92, 246, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--dark);
            font-weight: 800;
            font-size: 24px;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .logo-brand:hover { color: var(--primary); transform: scale(1.05); }
        
        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary) 0%, #a78bfa 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }
        
        .back-btn {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 10px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-btn:hover {
            background: white;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.2);
            transform: translateX(4px);
        }
        
        .main-content {
            position: relative;
            z-index: 1;
            flex: 1;
            padding: 120px 30px 40px;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .page-title {
            font-size: 48px;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .page-subtitle {
            font-size: 18px;
            color: var(--text);
        }
        
        .doctor-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
            overflow-x: auto;
            padding-bottom: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .doctor-tab {
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid rgba(139, 92, 246, 0.2);
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
            transition: 0.3s;
            white-space: nowrap;
            text-decoration: none;
            color: var(--text);
            box-shadow: 0 5px 15px rgba(139, 92, 246, 0.1);
        }
        
        .doctor-tab:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-3px);
        }
        
        .doctor-tab.active {
            background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
        }
        
        .doctor-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(139, 92, 246, 0.15);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.15);
            text-align: center;
            animation: slideUp 0.6s ease 0.2s both;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .doctor-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a0dfe8 0%, var(--accent) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            margin: 0 auto 25px;
            box-shadow: 0 15px 40px rgba(160, 223, 232, 0.3);
        }
        
        .doctor-name {
            font-size: 36px;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .doctor-specialty {
            font-size: 13px;
            color: var(--primary);
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        
        .doctor-description {
            font-size: 16px;
            color: var(--text);
            line-height: 1.6;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .buttons-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-action {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 180px;
            justify-content: center;
        }
        
        .btn-video {
            background: linear-gradient(135deg, #a0dfe8 0%, var(--accent) 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(160, 223, 232, 0.3);
        }
        
        .btn-video:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(160, 223, 232, 0.4);
        }
        
        .btn-audio {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
        }
        
        .btn-audio:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(72, 187, 120, 0.4);
        }
        
        .btn-chat {
            background: linear-gradient(135deg, #a78bfa 0%, var(--primary) 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }
        
        .btn-chat:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(139, 92, 246, 0.4);
        }
        
        /* MODALS */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        /* CHAT MODAL */
        .chat-container {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            height: 600px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s ease;
        }
        
        .chat-header {
            background: linear-gradient(135deg, var(--primary) 0%, #a78bfa 100%);
            color: white;
            padding: 20px;
            border-radius: 20px 20px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chat-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-right: 12px;
        }
        
        .chat-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 3px;
        }
        
        .online-status {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: 0.3s;
        }
        
        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9f7fc;
            display: flex;
            flex-direction: column;
            gap: 15px;
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
            padding: 12px 16px;
            border-radius: 15px;
            word-wrap: break-word;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .message.user .message-bubble {
            background: linear-gradient(135deg, var(--primary) 0%, #a78bfa 100%);
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
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 10px;
            background: white;
            border-radius: 0 0 20px 20px;
        }
        
        .chat-input {
            flex: 1;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            font-family: inherit;
            transition: 0.3s;
        }
        
        .chat-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .send-btn {
            padding: 12px 20px;
            background: linear-gradient(135deg, #a0dfe8 0%, var(--accent) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            transition: 0.3s;
        }
        
        .send-btn:hover {
            transform: translateY(-2px);
        }
        
        /* CALL MODAL */
        .call-modal-overlay {
            background: rgba(26, 35, 50, 0.95) !important;
        }
        
        .call-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            color: white;
        }
        
        .call-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #a78bfa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            box-shadow: 0 20px 50px rgba(139, 92, 246, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .call-name {
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 900;
        }
        
        .call-status {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .call-timer {
            font-size: 32px;
            font-weight: 900;
            margin-top: 15px;
            font-family: 'Courier New', monospace;
            display: none;
        }
        
        .call-controls {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
            background: rgba(80, 80, 80, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 15px 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            z-index: 3001;
        }
        
        .call-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            transition: 0.3s;
        }
        
        .call-btn:hover {
            transform: scale(1.1);
        }
        
        .call-end {
            background: #ff3333;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(255, 51, 51, 0.3);
        }
        
        .call-end:hover {
            background: #ff1111;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .main-content { padding: 100px 20px 30px; }
            .doctor-container { padding: 30px 20px; }
            .page-title { font-size: 32px; }
            .buttons-container { flex-direction: column; }
            .btn-action { min-width: 100%; }
            .chat-container { max-width: 100%; height: 500px; }
        }
    </style>
</head>
<body>
    <div class="bg-decoration">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
    </div>

    <nav class="navbar-custom">
        <a href="dashboard.php" class="logo-brand">
            <div class="logo-icon"><i class="fas fa-brain"></i></div>
            <span>Mind Care</span>
        </a>
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </nav>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">💬 Konseling Profesional</h1>
            <p class="page-subtitle">Dapatkan dukungan dari konselor berpengalaman</p>
        </div>

        <div class="doctor-tabs">
            <?php foreach ($doctors as $i => $doctor): ?>
                <button class="doctor-tab <?php echo $i === 0 ? 'active' : ''; ?>" onclick="selectDoctor(<?php echo $i; ?>)">
                    <?php echo $doctor['icon']; ?> <?php echo $doctor['name']; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="doctor-container">
            <div class="doctor-avatar" id="doctorAvatar"><?php echo $doctors[0]['icon']; ?></div>
            <h2 class="doctor-name" id="doctorName"><?php echo $doctors[0]['name']; ?></h2>
            <div class="doctor-specialty" id="doctorSpecialty"><?php echo $doctors[0]['specialty']; ?></div>
            <p class="doctor-description" id="doctorDescription"><?php echo $doctors[0]['desc']; ?></p>

            <div class="buttons-container">
                <button class="btn-action btn-video" onclick="startVideoCall()">
                    <i class="fas fa-video"></i> Video Call
                </button>
                <button class="btn-action btn-audio" onclick="startAudioCall()">
                    <i class="fas fa-phone"></i> Audio Call
                </button>
                <button class="btn-action btn-chat" onclick="startChat()">
                    <i class="fas fa-comments"></i> Chat
                </button>
            </div>
        </div>
    </main>

    <!-- Chat Modal -->
    <div id="chatModal" class="modal-overlay">
        <div class="chat-container">
            <div class="chat-header">
                <div style="display: flex; align-items: center;">
                    <div class="chat-avatar" id="chatAvatar">👨‍⚕️</div>
                    <div>
                        <div class="chat-name" id="chatName">Dr. Konselor</div>
                        <div class="online-status">🟢 Online</div>
                    </div>
                </div>
                <button class="close-btn" onclick="closeChat()"><i class="fas fa-times"></i></button>
            </div>
            <div class="chat-messages" id="messages"></div>
            <div class="chat-input-area">
                <input type="text" class="chat-input" id="chatInput" placeholder="Tulis pesan...">
                <button class="send-btn" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <!-- Call Modal -->
    <div id="callModal" class="modal-overlay call-modal-overlay">
        <div class="call-container">
            <div class="call-avatar" id="callAvatar">👨‍⚕️</div>
            <div style="text-align: center;">
                <div class="call-name" id="callName">Dr. Konselor</div>
                <div class="call-status" id="callStatus">📞 Menghubungi...</div>
                <div class="call-timer" id="callTimer">00:00</div>
            </div>
            <div class="call-controls" id="callControls">
                <button class="call-btn" onclick="toggleMute()" title="Mute">
                    <i class="fas fa-microphone"></i>
                </button>
                <button class="call-btn" onclick="toggleVideo()" title="Video">
                    <i class="fas fa-video"></i>
                </button>
                <button class="call-btn" onclick="toggleVolume()" title="Volume">
                    <i class="fas fa-volume-up"></i>
                </button>
                <button class="call-end call-btn" onclick="endCall()" title="End Call">
                    <i class="fas fa-phone"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        const doctors = <?php echo json_encode($doctors); ?>;
        let currentDoctor = 0;
        let callActive = false;
        let callDuration = 0;
        let callTimer = null;
        let isCallType = 'audio';

        function selectDoctor(index) {
            currentDoctor = index;
            const doc = doctors[index];
            document.getElementById('doctorAvatar').textContent = doc.icon;
            document.getElementById('doctorName').textContent = doc.name;
            document.getElementById('doctorSpecialty').textContent = doc.specialty;
            document.getElementById('doctorDescription').textContent = doc.desc;

            document.querySelectorAll('.doctor-tab').forEach((t, i) => {
                t.classList.toggle('active', i === index);
            });
        }

        function startChat() {
            const doc = doctors[currentDoctor];
            document.getElementById('chatName').textContent = doc.name;
            document.getElementById('chatAvatar').textContent = doc.icon;
            document.getElementById('messages').innerHTML = `
                <div class="message consultant">
                    <div class="message-bubble">Halo! Apa yang bisa saya bantu?</div>
                </div>
            `;
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
            const userDiv = document.createElement('div');
            userDiv.className = 'message user';
            userDiv.innerHTML = `<div class="message-bubble">${msg}</div>`;
            msgs.appendChild(userDiv);
            input.value = '';
            msgs.scrollTop = msgs.scrollHeight;

            setTimeout(() => {
                const responses = [
                    'Saya memahami. Bisakah Anda ceritakan lebih detail?',
                    'Itu masalah yang umum. Bagaimana Anda mengatasi?',
                    'Terima kasih berbagi. Mari kita cari solusi.',
                    'Saya dengar Anda. Apakah ada lagi yang mengganggu?'
                ];
                const docDiv = document.createElement('div');
                docDiv.className = 'message consultant';
                docDiv.innerHTML = `<div class="message-bubble">${responses[Math.floor(Math.random() * responses.length)]}</div>`;
                msgs.appendChild(docDiv);
                msgs.scrollTop = msgs.scrollHeight;
            }, 800);
        }

        document.getElementById('chatInput')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });

        function startVideoCall() {
            isCallType = 'video';
            startCall();
        }

        function startAudioCall() {
            isCallType = 'audio';
            startCall();
        }

        function startCall() {
            const doc = doctors[currentDoctor];
            document.getElementById('callName').textContent = doc.name;
            document.getElementById('callAvatar').textContent = doc.icon;
            document.getElementById('callStatus').textContent = `📞 ${isCallType === 'video' ? 'Video' : 'Audio'} call...`;
            document.getElementById('callTimer').style.display = 'none';
            document.getElementById('callModal').classList.add('active');
            callActive = true;
            callDuration = 0;

            setTimeout(() => {
                document.getElementById('callStatus').textContent = '⏱️ Berlangsung';
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
            if (callTimer) clearInterval(callTimer);
            callActive = false;
            document.getElementById('callModal').classList.remove('active');
            const m = Math.floor(callDuration / 60);
            const s = callDuration % 60;
            alert(`✅ ${isCallType === 'video' ? 'Video' : 'Audio'} call berakhir\nDurasi: ${m}m ${s}s`);
        }

        function toggleMute() {
            const btn = document.querySelectorAll('.call-btn')[0];
            btn.style.opacity = btn.style.opacity === '0.5' ? '1' : '0.5';
            btn.innerHTML = btn.style.opacity === '0.5' ? '<i class="fas fa-microphone-slash"></i>' : '<i class="fas fa-microphone"></i>';
        }

        function toggleVideo() {
            const btn = document.querySelectorAll('.call-btn')[1];
            btn.style.opacity = btn.style.opacity === '0.5' ? '1' : '0.5';
        }

        function toggleVolume() {
            const btn = document.querySelectorAll('.call-btn')[2];
            btn.style.opacity = btn.style.opacity === '0.5' ? '1' : '0.5';
        }

        document.addEventListener('click', (e) => {
            if (e.target.id === 'chatModal') closeChat();
            if (e.target.id === 'callModal') endCall();
        });
    </script>
</body>
</html>
