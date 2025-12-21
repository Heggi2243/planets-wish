<?php

/**
 * Email 驗證頁面
 * 已套用layout
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Users.php';

$token = $_GET['token'] ?? '';
$message = '';
$success = false;
$alreadyVerified = false;
$username = '';

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

// 頁面標題
$pageTitle = 'Planets-Wish | Email驗證';

// 引入JS
$additionalJS = ['/js/verify-email.js'];

$pageContent = function() use ($success, $message, $username, $alreadyVerified) {
?>
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
            
            <a href="/views/auth/index.php" class="btn-primary-gradient">
                前往登入
            </a>

        <?php else: ?>
            <!-- 失敗訊息 -->
            <div class="mb-6" id="failMsg">
                <h2 class="font-orbitron text-2xl text-white mb-2">驗證失敗</h2>
                <p class="text-white mb-4"><?php echo htmlspecialchars($message); ?></p>
            </div>
            
            <div id="action-buttons" class="space-y-3">
                <a href="/views/auth/index.php" class="mb-2 btn-secondary-cyan">
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
<?php
};

// 引入layout
require_once __DIR__ . '/layouts/layout.php';