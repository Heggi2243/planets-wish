<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planets-wish | 行星之願</title>
    

    <link rel="stylesheet" href="../css/input.css">
    <link href="../src/output.css" rel="stylesheet">
    <!-- 引入 Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Noto+Sans+TC:wght@300;400;700&display=swap" rel="stylesheet">
    

</head>
<body class="min-h-screen flex flex-col relative">

 <!-- 星空背景層 -->
    <div class="stars"></div>
    <div class="twinkling"></div>


    <!-- Header (始終顯示) -->
    <header class="w-full p-6 text-center z-20 relative">
        <h1 class="font-orbitron text-2xl md:text-4xl font-bold tracking-[0.2em] text-transparent bg-clip-text bg-gradient-to-r from-neon-cyan to-neon-purple drop-shadow-[0_0_10px_rgba(0,242,255,0.5)]">
            PLANETS WISH
        </h1>
    </header>

    <!-- AUTH SECTION: 登入/註冊區塊 -->
    <main id="auth-section" class="flex-grow flex items-center justify-center p-4 z-10 transition-all duration-700">
        <div class="glass-panel w-full max-w-md rounded-2xl p-8 relative overflow-hidden animate-float">
            <!-- 裝飾線條 -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-neon-cyan to-transparent"></div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-neon-purple to-transparent"></div>

            <!-- Login View -->
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

                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-500 hover:to-blue-600 text-white font-bold rounded-lg shadow-[0_0_20px_rgba(6,182,212,0.4)] transition-all transform hover:scale-[1.02] font-orbitron tracking-wide">
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

            <!-- Register View (Hidden by default) -->
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

                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-700 hover:from-purple-500 hover:to-pink-600 text-white font-bold rounded-lg shadow-[0_0_20px_rgba(168,85,247,0.4)] transition-all transform hover:scale-[1.02] font-orbitron tracking-wide mt-2">
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
    </main>

    <footer class="text-center py-4 text-gray-600 text-xs font-mono relative z-10">
        星願 Planets-Wish © 2025
    </footer>

    <!-- JavaScript Logic -->
<script>
// DOM Elements
const loginView = document.getElementById('login-view');
const registerView = document.getElementById('register-view');

// 取得根目錄
const BASE_URL = window.location.origin; // http://localhost:8000

console.log('🔧 DEBUG: BASE_URL =', BASE_URL);

// View Switching Logic
function switchView(viewName) {
    const activeView = viewName === 'login' ? registerView : loginView;
    const nextView = viewName === 'login' ? loginView : registerView;

    activeView.style.opacity = '0';
    
    setTimeout(() => {
        activeView.classList.add('hidden');
        nextView.classList.remove('hidden');
        void nextView.offsetWidth;
        nextView.style.opacity = '0';
        
        requestAnimationFrame(() => {
            nextView.style.transition = 'opacity 0.5s ease';
            nextView.style.opacity = '1';
        });
    }, 300);
}

// Handle Login
async function handleLogin(e) {
    e.preventDefault();

    const form = e.target;
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerText;
    const username = form.querySelector('input[type="text"]').value;
    const password = form.querySelector('input[type="password"]').value;

    btn.innerText = '認證中...';
    btn.classList.add('opacity-75', 'cursor-wait');
    btn.disabled = true;

    const apiUrl = `${BASE_URL}/controllers/AuthController.php?action=login`;
    console.log('🔧 DEBUG: 登入 URL =', apiUrl);

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', 
            },
            body: JSON.stringify({
                username: username,
                password: password
            })
        });

        console.log('🔧 DEBUG: Response status =', response.status);
        console.log('🔧 DEBUG: Response headers =', Object.fromEntries(response.headers.entries()));

        // 取得完整回應文字
        const responseText = await response.text();
        console.log('🔧 DEBUG: Response text (前 500 字) =', responseText.substring(0, 500));
        console.log('🔧 DEBUG: Response text (完整) =', responseText);

        // 檢查是否為 JSON
        const contentType = response.headers.get('content-type');
        console.log('🔧 DEBUG: Content-Type =', contentType);

        if (!contentType || !contentType.includes('application/json')) {
            // 在頁面上顯示錯誤
            alert('❌ 伺服器回應格式錯誤\n\n請按 F12 查看 Console 的詳細錯誤訊息');
            
            // 建立錯誤顯示區域
            showDetailedError('登入失敗', responseText);
            throw new Error('伺服器回應不是 JSON 格式');
        }

        // 解析 JSON
        let result;
        try {
            result = JSON.parse(responseText);
            console.log('🔧 DEBUG: Parsed result =', result);
        } catch (parseError) {
            console.error('❌ JSON 解析失敗:', parseError);
            console.error('❌ 原始內容:', responseText);
            showDetailedError('JSON 解析失敗', responseText);
            throw parseError;
        }

        if(result.success) {
            window.location.href = 'wishCreate.php';
        } else {
            alert(result.message || '登入失敗，請檢查帳號密碼');
        }

    } catch (error) {
        console.error('❌ 登入錯誤:', error);
        console.error('❌ 錯誤堆疊:', error.stack);
    } finally {
        btn.innerText = originalText;
        btn.classList.remove('opacity-75', 'cursor-wait');
        btn.disabled = false;
    }
}

// Handle Register
async function handleRegister(e) {
    e.preventDefault();

    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerText;

    const inputs = form.querySelectorAll('input');
    const username = inputs[0].value;
    const email = inputs[1].value;
    const password = inputs[2].value;
    const confirmPassword = inputs[3].value;

    // 前端驗證
    if (password !== confirmPassword) {
        alert('兩次輸入的密碼不一致！');
        return;
    }

    if (password.length < 6) {
        alert('密碼長度至少需要6個字元');
        return;
    }

    btn.innerText = '處理中...';
    btn.classList.add('opacity-75', 'cursor-wait');
    btn.disabled = true;

    const apiUrl = `${BASE_URL}/controllers/AuthController.php?action=register`;
    console.log('🔧 DEBUG: 註冊 URL =', apiUrl);

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', 
            },
            body: JSON.stringify({
                username: username,
                email: email,
                password: password
            })
        });

        console.log('🔧 DEBUG: Response status =', response.status);
        console.log('🔧 DEBUG: Response headers =', Object.fromEntries(response.headers.entries()));

        // 取得完整回應文字
        const responseText = await response.text();
        console.log('🔧 DEBUG: Response text (前 500 字) =', responseText.substring(0, 500));
        console.log('🔧 DEBUG: Response text (完整) =', responseText);

        // 檢查是否為 JSON
        const contentType = response.headers.get('content-type');
        console.log('🔧 DEBUG: Content-Type =', contentType);

        if (!contentType || !contentType.includes('application/json')) {
            alert('❌ 伺服器回應格式錯誤\n\n請按 F12 查看 Console 的詳細錯誤訊息');
            showDetailedError('註冊失敗', responseText);
            throw new Error('伺服器回應不是 JSON 格式');
        }

        // 解析 JSON
        let result;
        try {
            result = JSON.parse(responseText);
            console.log('🔧 DEBUG: Parsed result =', result);
        } catch (parseError) {
            console.error('❌ JSON 解析失敗:', parseError);
            console.error('❌ 原始內容:', responseText);
            showDetailedError('JSON 解析失敗', responseText);
            throw parseError;
        }

        if (result.success) {
            alert('註冊成功！請登入');
            form.reset();
            switchView('login');
        } else {
            alert(result.message || '註冊失敗');
        }

    } catch (error) {
        console.error('❌ 註冊錯誤:', error);
        console.error('❌ 錯誤堆疊:', error.stack);
    } finally {
        btn.innerText = originalText;
        btn.classList.remove('opacity-75', 'cursor-wait');
        btn.disabled = false;
    }
}

// 在頁面上顯示詳細錯誤
function showDetailedError(title, content) {
    const errorDiv = document.createElement('div');
    errorDiv.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border: 3px solid red;
        padding: 20px;
        max-width: 90%;
        max-height: 80%;
        overflow: auto;
        z-index: 10000;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        font-family: monospace;
        font-size: 12px;
    `;
    
    errorDiv.innerHTML = `
        <div style="color: red; font-weight: bold; font-size: 16px; margin-bottom: 10px;">
            ${title}
        </div>
        <div style="margin-bottom: 10px;">
            <strong>完整錯誤內容：</strong>
        </div>
        <pre style="background: #f5f5f5; padding: 10px; border: 1px solid #ddd; white-space: pre-wrap; word-wrap: break-word;">${escapeHtml(content)}</pre>
        <button onclick="this.parentElement.remove()" style="margin-top: 10px; padding: 10px 20px; background: red; color: white; border: none; cursor: pointer; font-weight: bold;">
            關閉
        </button>
    `;
    
    document.body.appendChild(errorDiv);
}

// HTML 轉義
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

loginView.style.transition = 'opacity 0.5s ease';
registerView.style.transition = 'opacity 0.5s ease';

console.log('✅ JavaScript 載入完成');
</script>
</body>
</html>