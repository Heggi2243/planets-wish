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

        // View Switching Logic
        function switchView(viewName) {
            // Add fade-out effect
            const activeView = viewName === 'login' ? registerView : loginView;
            const nextView = viewName === 'login' ? loginView : registerView;

            activeView.style.opacity = '0';
            
            setTimeout(() => {
                activeView.classList.add('hidden');
                nextView.classList.remove('hidden');
                void nextView.offsetWidth;
                nextView.style.opacity = '0';
                
                // Fade-in animation
                requestAnimationFrame(() => {
                    nextView.style.transition = 'opacity 0.5s ease';
                    nextView.style.opacity = '1';
                });
            }, 300); // Wait fade out
        }

        // Handle Login(用ajax)
        async function handleLogin(e) {

            e.preventDefault();

            const form = e.target;
            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            const username = form.querySelector('input[type="text"]').value;
            const password = form.querySelector('input[type="password"]').value;

            // Loading state
            btn.innerText = '認證中...';
            btn.classList.add('opacity-75', 'cursor-wait');
            btn.disabled = true;


            try {
                //傳到後端
                const response = await fetch('/controllers/AuthController.php?action=login',{
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', 
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });

                const result = await response.json();

                if(result.success) {
                    window.location.href = 'wishCreate.php';
                }else{
                    alert(result.message || '登入失敗，請檢查帳號密碼');
                }

            } catch (error) {
                console.log('錯誤:', error);
                alert('系統錯誤，請稍後再試');

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

            // 前端先驗證
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

            try {
                const response = await fetch('/controllers/AuthController.php?action=register',{
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

                const result = await response.json();

                if (result.success) {
                    alert('註冊成功！請登入');
                    form.reset();
                    switchView('login');
                } else {
                    alert(result.message || '註冊失敗');
                }

            } catch (error) {
                console.log('錯誤', error);
                alert('系統錯誤，請稍後再試');

            } finally {
                btn.innerText = originalText;
                btn.classList.remove('opacity-75', 'cursor-wait');
                btn.disabled = false;
            }

        }

        loginView.style.transition = 'opacity 0.5s ease';
        registerView.style.transition = 'opacity 0.5s ease';
    </script>
</body>
</html>