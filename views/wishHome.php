<?php
/**
 * wishHome.php 登入後主畫面
 * 已套用layout
 */

session_start();

// 檢查今日是否已許願
$today = date('Y-m-d');
$lastSummonDate = $_SESSION['last_daily_summon_date'] ?? null;
$hasWishedToday = ($lastSummonDate === $today);


// 頁面標題
$pageTitle = 'Planets-Wish';


$pageScript = ['../js/wishHome.js'];

// 無JS

$pageContent = function()  use ($hasWishedToday) {
?>
    <!-- 綠色行星裝飾 -->
    <div class="absolute top-20 right-10 md:right-1/4 w-24 h-24 md:w-32 md:h-32 z-20 animate-float">
        <img src="../img/planet2.png" class="w-full h-full object-contain drop-shadow-xl transform" />
    </div>
    
    <!-- 許願紀錄 -->
    <a href="./wishRecord.php"
        class="group absolute top-30 left-10 w-24 h-24 md:w-64 md:h-64 z-20 animate-float transition-transform duration-300 cursor-pointer flex flex-col items-center text-decoration-none">
        <div class="w-24 h-24 md:w-56 md:h-56">
            <img
                src="../img/satellite.png"
                class="w-full h-full object-contain transform transition-all duration-300 
                    drop-shadow-xl 
                    group-hover:drop-shadow-[0_0_20px_rgba(56,189,248,0.9)]
                    group-hover:scale-110
                    group-hover:-translate-y-2"
            />
        </div>
        <span class="mt-4 text-white font-orbitron text-sm md:text-lg tracking-widest
                 opacity-0 transform translate-y-4 transition-all duration-300 ease-out
                 group-hover:opacity-100 group-hover:translate-y-0">
            星願紀錄
        </span>
    </a>

   
    <main class="container mx-auto px-4 flex flex-col items-center justify-center relative z-10" 
          style="min-height: calc(100vh - 180px); max-height: calc(100vh - 180px);">
        
        <!-- 1. 核心互動區：傳送門  -->
        <div id="portal-container" class="relative transition-all duration-700 ease-in-out">
            <div class="relative group">
                <!-- 傳送門外環動畫 -->
                <div class="absolute -inset-1 bg-gradient-to-r from-orange-500 via-yellow-500 to-orange-600 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-pulse-fast"></div>
                
                <?php if ($hasWishedToday): ?>
                    <!-- 今天已許願: 彈出提示 -->
                    <button id="summon-btn" class="relative w-48 h-48 md:w-64 md:h-64 rounded-full flex items-center justify-center portal-glow animate-float overflow-visible cursor-pointer">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img 
                                src="../img/planet3.png" 
                                alt="Planet Portal" 
                                class="w-full h-full object-contain animate-spin-slow"
                            />
                        </div>
                    </button>
                <?php else: ?>
                    <!-- 今天未許願: 跳轉 -->
                    <a href="./wishCall.php" class="relative w-48 h-48 md:w-64 md:h-64 rounded-full flex items-center justify-center portal-glow animate-float overflow-visible block">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img 
                                src="../img/planet3.png" 
                                alt="Planet Portal" 
                                class="w-full h-full object-contain animate-spin-slow"
                            />
                        </div>
                    </a>
                <?php endif; ?>
</a>
            </div>
            <p class="text-center text-gray-400 mt-8 animate-bounce text-sm">今天會與哪個行星邂逅呢？點擊許願</p>
        </div>
        
    </main>

    <!-- 流星裝飾 -->
    <div class="absolute bottom-0 w-full">
        <div class="absolute bottom-20 right-10 w-32 h-32 md:w-56 md:h-56 z-20 animate-float">
            <img src="../img/meteor.png" class="w-full h-full object-contain drop-shadow-xl" />
        </div>

        <div class="absolute bottom-20 left-10 w-32 h-32 md:w-56 md:h-56 z-20 animate-float">
            <img src="../img/UFO.png" style="transform: rotate(-12deg);" class="w-full h-full object-contain" />
        </div>
    </div>

    <?php if ($hasWishedToday): ?>
    <!-- 背景遮罩 -->
    <div id="daily-limit-modal" class=" fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center">
        <!-- 提示框 -->
        <div class="glass-panel rounded-2xl p-8 max-w-md mx-4 shadow-2xl animate-fade-in">
            <div class="text-center mb-6">
                <div class="inline-block rounded-full mb-4">
                </div>
                <h3 class="text-2xl font-orbitron font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">
                    今日已許願
                </h3>
            </div>
            <p class="text-gray-300 text-center mb-6 leading-relaxed">
                你今天已經許過願囉！<br>
                每位旅者每天只能召喚一次行星。<br>
                <span class="text-cyan-400 font-bold">明天再來吧！</span>
            </p>
            <button id="close-modal-btn" class="btn-primary-gradient">
                我知道了
            </button>
        </div>
    </div>
    <?php endif; ?>
<?php
};

// 引入layout
require_once __DIR__ . '/../layouts/layout.php';