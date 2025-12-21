<?php
/**
 * 行星許願頁面
 * 已套用layout
 */

// 頁面標題
$pageTitle = 'Planets-Wish | 邂逅行星';

// 引入JS
$additionalJS = ['../js/wishCreate.js'];

$pageContent = function() {
?>
    <!-- 綠色行星裝飾 - 保持原本位置 -->
    <div class="absolute top-20 right-10 md:right-1/4 w-24 h-24 md:w-32 md:h-32 z-20 animate-float">
        <img src="../img/planet2.png" class="w-full h-full object-contain drop-shadow-xl transform" />
    </div>
    
    <!-- 許願紀錄 - 保持原本位置和大小 -->
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

    <!-- ✅ 主容器：調整高度，但不改變內部元素 -->
    <main class="container mx-auto px-4 flex flex-col items-center justify-center relative z-10" 
          style="min-height: calc(100vh - 180px); max-height: calc(100vh - 180px);">
        
        <!-- 1. 核心互動區：傳送門 - 保持原本大小 -->
        <div id="portal-container" class="relative transition-all duration-700 ease-in-out">
            <div class="relative group">
                <!-- ✅ 傳送門外環動畫 - 位置正確 -->
                <div class="absolute -inset-1 bg-gradient-to-r from-orange-500 via-yellow-500 to-orange-600 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-pulse-fast"></div>
                
                <!-- 傳送門本體 - 保持原本大小 -->
                <button id="summon-btn" class="relative w-48 h-48 md:w-64 md:h-64 rounded-full flex items-center justify-center portal-glow animate-float overflow-visible">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img 
                            src="../img/planet3.png" 
                            alt="Planet Portal" 
                            class="w-full h-full object-contain animate-spin-slow"
                        />
                    </div>
                    
                    <span class="relative z-10 font-orbitron text-xl md:text-2xl text-white font-bold tracking-widest pointer-events-none group-hover:scale-110 transition-transform duration-300">
                        
                    </span>
                </button>
            </div>
            <p class="text-center text-gray-400 mt-8 animate-bounce text-sm">今天會與哪個行星邂逅呢？點擊開啟</p>
        </div>

        <!-- 2. 星球資訊顯示區 - 優化尺寸但保持比例 -->
        <div id="result-section" class="hidden w-full max-w-5xl animate-fade-in mt-8">
            <div class="w-full bg-gray-900/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-4 md:p-6 shadow-2xl">
    
                <!-- 星球視覺區 - 縮小高度 -->
                <div class="relative w-full h-36 md:h-48 rounded-xl overflow-hidden shadow-lg border border-gray-700 group mb-4">
                    <img id="planet-image" src="" alt="Planet" class="w-full h-full object-cover transform transition-transform duration-[20s] group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-3 left-3">
                        <h2 id="planet-name" class="text-2xl md:text-3xl font-display font-bold text-white mb-1 drop-shadow-lg"></h2>
                        <span id="planet-type" class="text-xs font-mono text-cyan-400 bg-cyan-900/30 px-2 py-1 rounded border border-cyan-800"></span>
                    </div>
                </div>

                <!-- 下方資訊區 - 緊湊布局 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <!-- 星球描述 -->
                    <div class="md:col-span-2 bg-black/20 rounded-lg p-4 border border-white/5">
                        <p id="planet-desc" class="text-gray-300 italic text-sm leading-relaxed line-clamp-3"></p>
                    </div>

                    <!-- 屬性數據 -->
                    <div class="bg-black/20 rounded-lg p-4 border border-white/5 space-y-3">
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">溫度</span>
                            <span id="planet-temp" class="text-xl font-mono text-purple-300 font-bold"></span>
                        </div>
                        <div class="border-t border-gray-700/50 pt-3">
                            <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">距離</span>
                            <span id="planet-dist" class="text-xl font-mono text-purple-300 font-bold"></span>
                        </div>
                    </div>
                </div>

                <!-- 許願建議 - 緊湊版 -->
                <div class="mt-4 flex items-center p-4 bg-gradient-to-r from-purple-900/30 to-transparent border-l-4 border-purple-500 rounded-r-lg">
                    <div class="mr-3 text-2xl">✨</div>
                    <div class="flex-1">
                        <p class="text-xs text-purple-300 font-bold uppercase tracking-wider mb-1">星象建議</p>
                        <p id="wish-suggestion" class="text-sm font-medium text-white leading-relaxed line-clamp-2"></p>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- 流星裝飾 - 保持原本位置 -->
    <div class="absolute bottom-0 w-full">
        <div class="absolute bottom-20 right-10 w-32 h-32 md:w-56 md:h-56 z-20 animate-float">
            <img src="../img/meteor.png" class="w-full h-full object-contain drop-shadow-xl" />
        </div>

        <div class="absolute bottom-20 left-10 w-32 h-32 md:w-56 md:h-56 z-20 animate-float">
            <img src="../img/UFO.png" style="transform: rotate(-12deg);" class="w-full h-full object-contain" />
        </div>
    </div>
<?php
};

// 引入layout
require_once __DIR__ . '/../layouts/layout.php';