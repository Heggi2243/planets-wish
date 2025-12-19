<?php
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/models/Users.php';

$token = $_GET['token'] ?? '';
$message = '';
$success = false;

if (empty($token)) {
    $message = '無效的驗證連結';
} else {
    $userModel = new Users();
    $result = $userModel->verifyEmail($token);
    
    $success = $result['success'];
    $message = $result['message'];
    $username = $result['username'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email 驗證 | Planets-Wish</title>
    <link rel="stylesheet" href="css/input.css">
    <link href="src/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Noto+Sans+TC:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col relative">
    <!-- 星空背景 -->
    <div class="stars"></div>
    <div class="twinkling"></div>

    <!-- Header -->
    <header class="w-full p-6 text-center z-20 relative">
        <h1 class="font-orbitron text-2xl md:text-4xl font-bold tracking-[0.2em] text-transparent bg-clip-text bg-gradient-to-r from-neon-cyan to-neon-purple drop-shadow-[0_0_10px_rgba(0,242,255,0.5)]">
            PLANETS WISH
        </h1>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center p-4 z-10">
        <div class="glass-panel w-full max-w-md rounded-2xl p-8 relative overflow-hidden text-center">
            <!-- 裝飾線條 -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-neon-cyan to-transparent"></div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-neon-purple to-transparent"></div>

            <?php if ($success): ?>
                <!-- 成功訊息 -->
                <div class="mb-6">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-green-500/20 flex items-center justify-center">
                        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="font-orbitron text-2xl text-white mb-2">驗證成功！</h2>
                    <p class="text-cyan-300/80 mb-4"><?php echo htmlspecialchars($message); ?></p>
                    <p class="text-sm text-gray-400">歡迎加入，<?php echo htmlspecialchars($username); ?>！</p>
                </div>
                
                <a href="/views/index.php" class="inline-block px-8 py-3 bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-500 hover:to-blue-600 text-white font-bold rounded-lg shadow-[0_0_20px_rgba(6,182,212,0.4)] transition-all transform hover:scale-[1.02] font-orbitron tracking-wide">
                    前往登入
                </a>

            <?php else: ?>
                <!-- 失敗訊息 -->
                <div class="mb-6">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-red-500/20 flex items-center justify-center">
                        <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h2 class="font-orbitron text-2xl text-white mb-2">驗證失敗</h2>
                    <p class=" mb-2 text-red-400"><?php echo htmlspecialchars($message); ?></p>
                </div>
                
                <div class="space-y-3">

                    <button onclick="showResendForm()" class="w-full px-8 py-3 block px-8 py-3 bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-500 hover:to-blue-600 text-white font-bold rounded-lg shadow-[0_0_20px_rgba(6,182,212,0.4)] transition-all transform hover:scale-[1.02] font-orbitron tracking-wide">
                        重新發送驗證信
                    </button>
                </div>

                <!-- 重新發送表單 (隱藏) -->
                <div id="resend-form" class="hidden mt-6 pt-6 border-t border-white/10">
                    <form onsubmit="resendVerification(event)" class="space-y-4">
                        <input 
                            type="email" 
                            id="resend-email" 
                            placeholder="輸入您的 Email" 
                            class="sci-fi-input w-full px-4 py-3 rounded-lg font-mono"
                            required
                        >
                        <button type="submit" class="w-full px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-700 hover:from-purple-500 hover:to-pink-600 text-white font-bold rounded-lg shadow-[0_0_20px_rgba(168,85,247,0.4)] transition-all font-orbitron tracking-wide">
                            發送驗證信
                        </button>
                    </form>
                    <p id="resend-message" class="mt-4 text-sm text-cyan-300"></p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="text-center py-4 text-gray-600 text-xs font-mono relative z-10">
        星願 Planets-Wish © 2025
    </footer>

    <script>
        function showResendForm() {
            document.getElementById('resend-form').classList.remove('hidden');
        }

        async function resendVerification(e) {
            e.preventDefault();
            
            const email = document.getElementById('resend-email').value;
            const btn = e.target.querySelector('button');
            const message = document.getElementById('resend-message');
            
            btn.disabled = true;
            btn.innerText = '發送中...';
            message.textContent = '';
            
            try {
                const response = await fetch('/controllers/AuthController.php?action=resend-verification', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                
                const result = await response.json();
                
                message.textContent = result.message;
                message.className = result.success ? 
                    'mt-4 text-sm text-green-400' : 
                    'mt-4 text-sm text-red-400';
                    
                if (result.success) {
                    e.target.reset();
                }
                
            } catch (error) {
                message.textContent = '發送失敗，請稍後再試';
                message.className = 'mt-4 text-sm text-red-400';
            } finally {
                btn.disabled = false;
                btn.innerText = '發送驗證信';
            }
        }
    </script>
</body>
</html>