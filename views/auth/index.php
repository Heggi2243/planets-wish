<?php
/**
 * 歡迎頁面(登入/註冊)
 * 已套用layout
 */

// 頁面標題
$pageTitle = 'Planets-Wish | 行星之願';

// 引入JS
$additionalJS = ['../../js/auth.js'];

$pageContent = function() {
?>
<main>
    <!-- AUTH:登入/註冊區塊 -->
    <div class="flex items-center justify-center p-4">
        <div class="glass-panel w-full max-w-md rounded-2xl p-8 relative overflow-hidden animate-float">
            <!-- 裝飾小線條 -->
            <div class="line-neon-top"></div>
            <div class="line-neon-bottom"></div>

            <!-- 登入 -->
            <div id="login-view" class="space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="font-orbitron text-2xl text-white">歡迎來到星願</h2>
                    <p class="text-xs text-cyan-300/60 tracking-widest uppercase">Identity Verification Required</p>
                </div>

                <form onsubmit="handleLogin(event)" class="space-y-5">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-cyan-500 uppercase tracking-wider ml-1">帳號</label>
                        <input type="text" placeholder="Enter your ID" class="sci-fi-input w-full px-4 py-3 rounded-lg font-mono" required>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-cyan-500 uppercase tracking-wider ml-1">密碼(英文+數字至少6位)</label>
                        <input type="password" placeholder="••••••••" class="sci-fi-input w-full px-4 py-3 rounded-lg font-mono" required>
                    </div>

                    <button type="submit" class="btn-secondary-cyan">
                        登入
                    </button>
                </form>

                <div class="text-center pt-4 border-t border-white/10">
                    <p class="text-sm text-gray-400">沒有帳號?</p>
                    <button onclick="switchView('register')" class="mt-2 text-neon-cyan hover:text-white underline underline-offset-4 decoration-neon-cyan/50 hover:decoration-white transition-all text-sm font-bold tracking-wide">
                        點擊註冊
                    </button>
                </div>
            </div>

            <!-- 註冊(預設隱藏) -->
            <div id="register-view" class="hidden space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="font-orbitron text-2xl text-white">註冊帳號</h2>
                    <p class="text-xs text-purple-300/60 tracking-widest uppercase">Register an account</p>
                </div>

                <form onsubmit="handleRegister(event)" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-purple-400 uppercase tracking-wider ml-1">帳號</label>
                        <input type="text" class="sci-fi-input w-full px-4 py-3 rounded-lg font-mono" required>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-purple-400 uppercase tracking-wider ml-1">Email</label>
                        <input type="email" class="sci-fi-input w-full px-4 py-3 rounded-lg font-mono" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-purple-400 uppercase tracking-wider ml-1">密碼</label>
                            <input type="password" class="sci-fi-input w-full px-4 py-3 rounded-lg font-mono" required>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-purple-400 uppercase tracking-wider ml-1">再次輸入密碼</label>
                            <input type="password" class="sci-fi-input w-full px-4 py-3 rounded-lg font-mono" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary-gradient">
                        驗證Email
                    </button>
                </form>

                <div class="text-center pt-4 border-t border-white/10">
                    <button onclick="switchView('login')" class="flex items-center justify-center w-full text-gray-400 hover:text-white transition-colors gap-2 text-sm group">
                        <span class="group-hover:-translate-x-1 transition-transform">←</span>
                        回到登入畫面
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
};

// 引入layout
require_once __DIR__ . '/../../layouts/layout.php';