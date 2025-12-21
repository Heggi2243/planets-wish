<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/input.css">
    <link href="../src/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Noto+Sans+TC:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col relative">
    <div class="glass-panel glass-panel-card">
            <div class="line-neon-top"></div>
            <div class="line-neon-bottom"></div>
            <h2 class="font-orbitron text-2xl text-white mb-3">註冊成功！</h2>
            <p class="text-white mb-2">請至您的信箱收取驗證信</p>
            <p class="text-sm text-gray-400 mb-6">${email}</p>
            <button onclick="closeModal('verification-modal'); switchView('login')" class="btn-primary-gradient">
                返回登入頁
            </button>
        </div>


    <div id="daily-limit-modal" class=" fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="glass-panel rounded-2xl p-8 max-w-md mx-4 shadow-2xl animate-fade-in">
            <!-- 標題 -->
            <div class="text-center mb-6">
                <div class="inline-block rounded-full mb-4">
                </div>
                <h3 class="text-2xl font-orbitron font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">
                    今日已許願
                </h3>
            </div>
            
            <!-- 內容 -->
            <p class="text-gray-300 text-center mb-6 leading-relaxed">
                你今天已經許過願囉！<br>
                每位旅者每天只能召喚一次行星。<br>
                <span class="text-cyan-400 font-bold">明天再來吧！</span>
            </p>
            
            <!-- 按鈕 -->
            <button id="close-modal-btn" class="btn-primary-gradient">
                我知道了
            </button>
        </div>
    </div>
</body>
</html>