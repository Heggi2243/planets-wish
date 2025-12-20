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


    <div class="glass-panel glass-panel-card">
        <div class="line-neon-top"></div>
        <div class="line-neon-bottom"></div>
        <div class="mb-6">
            <h2 class="font-orbitron text-2xl text-white mb-2">註冊成功</h2>
            <p class="text-sm text-gray-400">歡迎加入，123！</p>
        </div>
        
        <a href="/views/welcome.php" class="inline-block px-8 py-3 bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-500 hover:to-blue-600 text-white font-bold rounded-lg shadow-[0_0_20px_rgba(6,182,212,0.4)] transition-all transform hover:scale-[1.02] font-orbitron tracking-wide">
            前往登入
        </a>
    </div>

</body>
</html>