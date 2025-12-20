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
    <div class="absolute top-20 right-10 md:right-1/4 w-24 h-24 md:w-48 md:h-48 z-20 animate-float">
        <img src="../img/planet2.png" class="w-full h-full object-contain drop-shadow-xl transform" />
    </div>
    
    <main class="container mx-auto px-4 flex flex-col items-center justify-center min-h-screen py-20 relative z-10">
        
        <!-- 1. 核心互動區：傳送門 -->

        <div id="portal-container" class="relative transition-all duration-700 ease-in-out">
            <div class="relative group">
                <!-- 傳送門外環動畫 - 改為橘黃色調 -->
                <div class="absolute -inset-1 bg-gradient-to-r from-orange-500 via-yellow-500 to-orange-600 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-pulse-fast"></div>
                
                <!-- 傳送門本體 - 移除背景色，使用透明背景 -->
                <button id="summon-btn" class="relative w-48 h-48 md:w-64 md:h-64 rounded-full flex items-center justify-center portal-glow animate-float overflow-visible">
                    <!-- 內部旋渦圖案 - 完整顯示，不裁切 -->
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

        <!-- 流星裝飾 -->
        <div class="absolute bottom-0 w-full">
            <div class="absolute bottom-20 right-10 w-32 h-32 md:w-56 md:h-56 z-20 animate-float">
                <img src="../img/meteor.png" class="w-full h-full object-contain drop-shadow-xl" />
            </div>

            <div class="absolute bottom-20 left-10 w-32 h-32 md:w-56 md:h-56 z-20 animate-float">
                <img src="../img/UFO.png" style="transform: rotate(-12deg);" class="w-full h-full object-contain" />
            </div>
        </div>




        <!-- 2. 星球資訊顯示區 (初始隱藏) -->
        <div id="result-section" class="hidden w-full animate-fade-in">
            <div class="w-full bg-gray-900/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-6 md:p-10 shadow-2xl">
    
                <!-- 星球視覺區 - 上方 -->
                <div class="relative w-full aspect-[21/9] md:aspect-[25/9] rounded-2xl overflow-hidden shadow-lg border border-gray-700 group mb-8">
                    <img id="planet-image" src="" alt="Planet" class="w-full h-full object-cover transform transition-transform duration-[20s] group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <h2 id="planet-name" class="text-3xl md:text-5xl font-display font-bold text-white mb-2 drop-shadow-lg"></h2>
                        <span id="planet-type" class="text-sm font-mono text-cyan-400 bg-cyan-900/30 px-3 py-1.5 rounded border border-cyan-800"></span>
                    </div>
                </div>

                <!-- 下方資訊區 - 橫向排列 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- 星球描述 -->
                    <div class="md:col-span-2 bg-black/20 rounded-xl p-5 border border-white/5">
                        <p id="planet-desc" class="text-gray-300 italic text-base leading-relaxed"></p>
                    </div>

                    <!-- 屬性數據 -->
                    <div class="bg-black/20 rounded-xl p-5 border border-white/5 space-y-4">
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">溫度</span>
                            <span id="planet-temp" class="text-2xl font-mono text-purple-300 font-bold"></span>
                        </div>
                        <div class="border-t border-gray-700/50 pt-4">
                            <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">距離</span>
                            <span id="planet-dist" class="text-2xl font-mono text-purple-300 font-bold"></span>
                        </div>
                    </div>
                </div>

                <!-- 許願建議 - 全寬底部 -->
                <div class="mt-6 flex items-center p-5 bg-gradient-to-r from-purple-900/30 to-transparent border-l-4 border-purple-500 rounded-r-lg">
                    <div class="mr-4 text-3xl">✨</div>
                    <div class="flex-1">
                        <p class="text-xs text-purple-300 font-bold uppercase tracking-wider mb-1.5">星象建議</p>
                        <p id="wish-suggestion" class="text-base font-medium text-white leading-relaxed"></p>
                    </div>
                </div>

            </div>
        </div>
    </main>
<?php
};

// 引入layout
require_once __DIR__ . '/../layouts/layout.php';