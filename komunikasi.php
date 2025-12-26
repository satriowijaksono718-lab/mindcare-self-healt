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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komunikasi - Mind Care</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f7fc;
            min-height: 100vh;
        }

        .navbar {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 600;
            color: #2d3e50;
            text-decoration: none;
        }

        .logo-icon {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #4a5f8f 0%, #6b7fa8 100%);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
        }

        .back-link {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #7c3aed;
        }

        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            text-align: center;
            font-size: 48px;
            font-weight: 900;
            color: #1a2332;
            margin-bottom: 50px;
            font-style: italic;
        }

        .communication-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .communication-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-align: center;
            cursor: pointer;
            border-top: 5px solid #8b5cf6;
        }

        .communication-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .card-icon {
            font-size: 60px;
            margin-bottom: 20px;
            display: block;
        }

        .card-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a2332;
            margin-bottom: 15px;
        }

        .card-description {
            color: #666;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .card-button {
            padding: 12px 25px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .card-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        /* Chat Interface */
        .chat-interface {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .chat-interface.active {
            display: flex;
        }

        .chat-container {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            height: 600px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .chat-header {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 20px;
            border-radius: 20px 20px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .consultant-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .consultant-details h3 {
            font-size: 16px;
            margin-bottom: 4px;
        }

        .online-status {
            font-size: 12px;
            opacity: 0.9;
        }

        .close-chat-btn {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9f9f9;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            animation: slideIn 0.3s ease;
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

        .message.user {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 12px;
            word-wrap: break-word;
        }

        .message.user .message-bubble {
            background: #8b5cf6;
            color: white;
            border-radius: 12px 0 12px 12px;
        }

        .message.consultant .message-bubble {
            background: #e8e8e8;
            color: #333;
            border-radius: 0 12px 12px 12px;
        }

        .message-time {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 4px;
        }

        .chat-input-area {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
            font-family: inherit;
        }

        .chat-input:focus {
            outline: none;
            border-color: #8b5cf6;
        }

        .chat-send-btn {
            padding: 12px 20px;
            background: #8b5cf6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .chat-send-btn:hover {
            background: #7c3aed;
        }

        /* Call Interface */
        .call-interface {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #1a2332;
            z-index: 1000;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .call-interface.active {
            display: flex;
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
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            box-shadow: 0 10px 40px rgba(139, 92, 246, 0.3);
        }

        .call-info {
            text-align: center;
        }

        .call-info h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .call-status {
            font-size: 16px;
            opacity: 0.8;
        }

        .call-timer {
            font-size: 24px;
            font-weight: 700;
            margin-top: 10px;
        }

        .call-controls {
            display: flex;
            gap: 20px;
        }

        .call-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .call-btn.mute {
            background: #4a5f8f;
            color: white;
        }

        .call-btn.mute:hover {
            background: #5a6f9f;
        }

        .call-btn.speaker {
            background: #4a5f8f;
            color: white;
        }

        .call-btn.speaker:hover {
            background: #5a6f9f;
        }

        .call-btn.end {
            background: #ff5252;
            color: white;
        }

        .call-btn.end:hover {
            background: #ff3333;
            transform: scale(1.1);
        }

        /* Video Call Interface */
        .video-interface {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 1000;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .video-interface.active {
            display: flex;
        }

        .video-container {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .video-frame {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
        }

        .video-main {
            flex: 1;
            height: 100%;
            max-height: 600px;
        }

        .video-small {
            width: 150px;
            height: 150px;
            border: 3px solid white;
        }

        .video-controls {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
            z-index: 1001;
        }

        .video-btn {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .video-btn.camera {
            background: #4a5f8f;
            color: white;
        }

        .video-btn.camera:hover {
            background: #5a6f9f;
        }

        .video-btn.mic {
            background: #4a5f8f;
            color: white;
        }

        .video-btn.mic:hover {
            background: #5a6f9f;
        }

        .video-btn.end {
            background: #ff5252;
            color: white;
        }

        .video-btn.end:hover {
            background: #ff3333;
            transform: scale(1.1);
        }

        .video-info {
            position: fixed;
            top: 30px;
            right: 30px;
            color: white;
            text-align: right;
            z-index: 1001;
        }

        .video-info h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .video-timer {
            font-size: 14px;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 32px;
            }

            .communication-grid {
                grid-template-columns: 1fr;
            }

            .chat-container {
                max-width: 100%;
                height: 500px;
            }

            .video-container {
                flex-direction: column;
            }

            .video-main {
                width: 100%;
                max-height: 400px;
            }

            .video-controls {
                bottom: 20px;
            }

            .video-info {
                top: 20px;
                right: 20px;
            }
        }

        .typing-indicator {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #8b5cf6;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                opacity: 0.3;
                transform: translateY(0);
            }
            30% {
                opacity: 1;
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php" class="logo">
            <div class="logo-icon">⊙</div>
            <span>Wardiere Inc.</span>
        </a>
        <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
    </div>

    <div class="container">
        <h1 class="page-title">Komunikasi 💬</h1>

        <div class="communication-grid">
            <!-- Chat Card -->
            <div class="communication-card">
                <span class="card-icon">💬</span>
                <div class="card-title">Chat</div>
                <div class="card-description">Berbincang dengan konselor melalui chat real-time. Sempurna untuk pertanyaan cepat dan dukungan teks.</div>
                <button class="card-button" onclick="startChat()">Mulai Chat</button>
            </div>

            <!-- Phone Call Card -->
            <div class="communication-card">
                <span class="card-icon">☎️</span>
                <div class="card-title">Panggilan Telepon</div>
                <div class="card-description">Hubungi konselor via telepon untuk konsultasi yang lebih personal dan mendalam.</div>
                <button class="card-button" onclick="startCall()">Mulai Panggilan</button>
            </div>

            <!-- Video Call Card -->
            <div class="communication-card">
                <span class="card-icon">📹</span>
                <div class="card-title">Video Call</div>
                <div class="card-description">Konsultasi tatap muka virtual dengan konselor untuk pengalaman yang paling personal.</div>
                <button class="card-button" onclick="startVideoCall()">Mulai Video Call</button>
            </div>
        </div>
    </div>

    <!-- Chat Interface -->
    <div class="chat-interface" id="chatInterface">
        <div class="chat-container">
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="consultant-avatar">👨‍⚕️</div>
                    <div class="consultant-details">
                        <h3>Dr. Konselor</h3>
                        <div class="online-status">🟢 Online</div>
                    </div>
                </div>
                <button class="close-chat-btn" onclick="closeChat()">✕</button>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="message consultant">
                    <div>
                        <div class="message-bubble">Halo! Selamat datang. Bagaimana kabar Anda hari ini? 😊</div>
                        <div class="message-time">10:30 AM</div>
                    </div>
                </div>
            </div>

            <div class="chat-input-area">
                <input type="text" class="chat-input" id="chatInput" placeholder="Ketik pesan Anda...">
                <button class="chat-send-btn" onclick="sendChatMessage()">Kirim</button>
            </div>
        </div>
    </div>

    <!-- Phone Call Interface -->
    <div class="call-interface" id="callInterface">
        <div class="call-container">
            <div class="call-avatar">👨‍⚕️</div>
            <div class="call-info">
                <h2>Dr. Konselor</h2>
                <div class="call-status" id="callStatus">Menghubungi...</div>
                <div class="call-timer" id="callTimer" style="display: none;">00:00</div>
            </div>
            <div class="call-controls">
                <button class="call-btn mute" onclick="toggleMute()" title="Mute/Unmute">🔇</button>
                <button class="call-btn speaker" onclick="toggleSpeaker()" title="Speaker">🔊</button>
                <button class="call-btn end" onclick="endCall()">🔴</button>
            </div>
        </div>
    </div>

    <!-- Video Call Interface -->
    <div class="video-interface" id="videoInterface">
        <div class="video-info">
            <h3>Dr. Konselor</h3>
            <div class="video-timer" id="videoTimer">00:00</div>
        </div>

        <div class="video-container">
            <div class="video-frame video-main">
                👨‍⚕️
            </div>
            <div class="video-frame video-small">
                👤
            </div>
        </div>

        <div class="video-controls">
            <button class="video-btn camera" onclick="toggleCamera()" title="Camera">📹</button>
            <button class="video-btn mic" onclick="toggleVideoMic()" title="Microphone">🎤</button>
            <button class="video-btn end" onclick="endVideoCall()">🔴</button>
        </div>
    </div>

    <script>
        let callDuration = 0;
        let videoDuration = 0;
        let callTimer = null;
        let videoTimer = null;
        let isMuted = false;
        let isSpeaker = false;
        let isCamera = true;
        let isMic = true;

        function startChat() {
            document.getElementById('chatInterface').classList.add('active');
        }

        function closeChat() {
            document.getElementById('chatInterface').classList.remove('active');
            document.getElementById('chatMessages').innerHTML = `
                <div class="message consultant">
                    <div>
                        <div class="message-bubble">Halo! Selamat datang. Bagaimana kabar Anda hari ini? 😊</div>
                        <div class="message-time">10:30 AM</div>
                    </div>
                </div>
            `;
            document.getElementById('chatInput').value = '';
        }

        function sendChatMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            
            if (!message) return;

            const messagesDiv = document.getElementById('chatMessages');
            const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            // User message
            messagesDiv.innerHTML += `
                <div class="message user">
                    <div>
                        <div class="message-bubble">${message}</div>
                        <div class="message-time">${time}</div>
                    </div>
                </div>
            `;

            input.value = '';
            messagesDiv.scrollTop = messagesDiv.scrollHeight;

            // Consultant typing
            messagesDiv.innerHTML += `
                <div class="message consultant">
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            `;
            messagesDiv.scrollTop = messagesDiv.scrollHeight;

            // Consultant response after delay
            setTimeout(() => {
                const responses = [
                    "Terimakasih sudah berbagi. Saya mengerti perasaan Anda.",
                    "Itu adalah langkah yang bagus untuk mencari dukungan.",
                    "Mari kita diskusikan lebih dalam tentang hal ini.",
                    "Bagaimana perasaan Anda tentang hal tersebut?",
                    "Apakah ada yang ingin Anda tambahkan?"
                ];
                const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                const time2 = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                messagesDiv.innerHTML = messagesDiv.innerHTML.replace(
                    `<div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>`,
                    `<div class="message-bubble">${randomResponse}</div>
                    <div class="message-time">${time2}</div>`
                );
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }, 2000);
        }

        function startCall() {
            document.getElementById('callInterface').classList.add('active');
            callDuration = 0;
            document.getElementById('callStatus').textContent = 'Menghubungi...';
            document.getElementById('callTimer').style.display = 'none';

            setTimeout(() => {
                document.getElementById('callStatus').textContent = '⏱️ Sedang berlangsung';
                document.getElementById('callTimer').style.display = 'block';
                
                if (callTimer) clearInterval(callTimer);
                callTimer = setInterval(() => {
                    callDuration++;
                    const minutes = Math.floor(callDuration / 60);
                    const seconds = callDuration % 60;
                    document.getElementById('callTimer').textContent = 
                        String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                }, 1000);
            }, 3000);
        }

        function endCall() {
            document.getElementById('callInterface').classList.remove('active');
            if (callTimer) clearInterval(callTimer);
            alert(`Panggilan berakhir. Durasi: ${Math.floor(callDuration / 60)} menit ${callDuration % 60} detik`);
        }

        function toggleMute() {
            isMuted = !isMuted;
            const btn = event.target.closest('button');
            btn.textContent = isMuted ? '🔇' : '🔊';
            btn.style.opacity = isMuted ? '0.5' : '1';
        }

        function toggleSpeaker() {
            isSpeaker = !isSpeaker;
            const btn = event.target.closest('button');
            btn.style.opacity = isSpeaker ? '1' : '0.5';
        }

        function startVideoCall() {
            document.getElementById('videoInterface').classList.add('active');
            videoDuration = 0;
            
            if (videoTimer) clearInterval(videoTimer);
            videoTimer = setInterval(() => {
                videoDuration++;
                const minutes = Math.floor(videoDuration / 60);
                const seconds = videoDuration % 60;
                document.getElementById('videoTimer').textContent = 
                    String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }, 1000);
        }

        function endVideoCall() {
            document.getElementById('videoInterface').classList.remove('active');
            if (videoTimer) clearInterval(videoTimer);
            alert(`Video call berakhir. Durasi: ${Math.floor(videoDuration / 60)} menit ${videoDuration % 60} detik`);
        }

        function toggleCamera() {
            isCamera = !isCamera;
            const btn = event.target.closest('button');
            btn.style.opacity = isCamera ? '1' : '0.5';
        }

        function toggleVideoMic() {
            isMic = !isMic;
            const btn = event.target.closest('button');
            btn.textContent = isMic ? '🎤' : '🔇';
            btn.style.opacity = isMic ? '1' : '0.5';
        }

        // Close interfaces on background click
        document.getElementById('chatInterface').addEventListener('click', function(e) {
            if (e.target === this) {
                closeChat();
            }
        });

        // Enter key to send chat message
        document.getElementById('chatInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendChatMessage();
            }
        });
    </script>
</body>
</html>
