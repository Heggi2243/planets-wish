<?php

/**
 * Email 驗證頁面
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/models/Users.php';

$token = $_GET['token'] ?? '';
$message = '';
$success = false;

//記錄驗證嘗試
error_log("=== Email 驗證嘗試 ===");
error_log("Token: " . $token);
error_log("IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

if (empty($token)) {
    error_log("驗證失敗：Token 為空");
    $message = '無效的驗證連結';
} else {
    try {
        $userModel = new Users();
        $result = $userModel->verifyEmail($token);
        
        error_log("驗證結果: " . print_r($result, true));
        
        $success = $result['success'];
        $message = $result['message'];
        $username = $result['username'] ?? '';
        
    } catch (Exception $e) {
        error_log("驗證異常: " . $e->getMessage());
        error_log("堆疊: " . $e->getTraceAsString());
        
        $success = false;
        $message = '系統錯誤：' . $e->getMessage();
    }
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
        <div class="glass-panel glass-panel-card">
            <!-- 裝飾線條 -->
            <div class="line-neon-top"></div>
            <div class="line-neon-bottom"></div>

            <?php if ($success): ?>
                <!-- 成功訊息 -->
                <div class="mb-6">
                    <h2 class="font-orbitron text-2xl text-white mb-2"><?php echo htmlspecialchars($message); ?></h2>
                    <p class="text-sm text-gray-400">歡迎加入，<?php echo htmlspecialchars($username); ?>！</p>
                    </br>
                </div>
                
                <a href="/views/welcome.php" class="btn-primary-gradient">
                    前往登入
                </a>

            <?php else: ?>
                <!-- 失敗訊息 -->
                <div class="mb-6" id="failMsg">
                    <h2 class="font-orbitron text-2xl text-white mb-2">驗證失敗</h2>
                    <p class="text-white mb-4"><?php echo htmlspecialchars($message); ?></p>
                </div>
                
                <div id="action-buttons" class="space-y-3">
                    <a href="/views/welcome.php" class="mb-2 btn-secondary-cyan">
                        返回首頁
                    </a>
                    <button onclick="showResendForm()" class="btn-primary-gradient">
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
                        <button type="submit" class="btn-primary-gradient">
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
        const BASE_URL = window.location.origin;

        function showResendForm() {
            document.getElementById('action-buttons').classList.add('hidden');
            document.getElementById('resend-form').classList.remove('hidden');
            document.querySelector('#failMsg h2').textContent = "重新驗證信箱";
            document.querySelector('#failMsg p').classList.add('hidden');
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
                const response = await fetch(`${BASE_URL}/controllers/AuthController.php?action=resend-verification`, {
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