<?php
/**
 * wish/create.php - 行星召喚/許願頁面
 */


var_dump($categoryImg);
$pageContent = function() use ($showPlanet, $planetData, $categoryImg) {
?>
<main class="flex flex-col items-center justify-center min-h-[calc(100vh-180px)] relative">
    
    <?php if (!$showPlanet): ?>
    <!-- 太空人啟動畫面 -->
    <div id="astronaut-intro" class="flex flex-col items-center justify-center animate-fade-in">
        <a href="/planets-wish/wish/create?summon=true" class="relative group cursor-pointer">
            <div class="absolute -inset-4 rounded-full blur-xl opacity-75 group-hover:opacity-100 transition duration-500 animate-pulse"></div>
            
            <div class="relative w-64 h-64 md:w-90 md:h-90 transform transition-all duration-500 group-hover:scale-110 group-hover:-translate-y-4">
                <img 
                    src="/img/astronaut03.png" 
                    alt="Astronaut" 
                    class="w-full h-full object-contain drop-shadow-2xl"
                />
            </div>
        </a>
        
        <p class="mt-8 text-gray-400 text-center animate-bounce">
            <span class="block text-lg md:text-xl font-orbitron text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400 font-bold mb-2">
                準備好了嗎？
            </span>
            <span class="text-sm md:text-base">
                點擊太空人，邂逅你的星願
            </span>
        </p>
        
        <div class="absolute top-20 left-1/4 w-2 h-2 bg-yellow-400 rounded-full animate-ping"></div>
        <div class="absolute top-40 right-1/3 w-3 h-3 bg-cyan-400 rounded-full animate-pulse"></div>
        <div class="absolute bottom-32 left-1/3 w-2 h-2 bg-purple-400 rounded-full animate-ping" style="animation-delay: 0.5s;"></div>
    </div>
    
    <?php else: ?>
    <!-- 星球資訊區 -->
    <div id="result-section" class="w-full max-w-5xl animate-fade-in px-4">
        <div class="w-full bg-gray-900/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-3 md:p-4 shadow-2xl">

            <!-- 行星標題與基本資訊 - 緊湊版 -->
            <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-700/50">
                <!-- 左側：標題 + 行星圖片 -->
                <div class="flex items-center gap-4">
                    <!-- 標題區 -->
                    <div class="ml-40">
                        <h2 class="text-xl md:text-2xl mb-4 font-display font-bold text-white drop-shadow-lg">
                            <?= htmlspecialchars($planetData['name']) ?>
                        </h2>
                        <span class="text-xs font-mono mb-2 text-cyan-400 bg-cyan-900/30 px-2 py-0.5 rounded border border-cyan-800 mt-1 inline-block">
                            今日星願
                        </span>
                    </div>
                    
                    <!-- 漂浮行星圖片 -->
                    <div class="absolute -left-10 w-32 h-32 md:w-50 md:h-50 animate-float">
                        <img 
                            id="planet-icon"
                            src="../../img/<?= $categoryImg ? 'planet_' . $categoryImg : 'wormhole' ?>.png"
                            alt="<?= htmlspecialchars($planetData['name']) ?>"
                            class="w-full h-full object-contain drop-shadow-2xl"
                            onerror="this.src='../img/wormhole.png'"
                        />
                    </div>
                </div>
                
                <!-- 右側：屬性數據 - 橫向排列 -->
                <div class="flex gap-3 md:gap-4 text-right">
                    <div>
                        <span class="block text-xs text-gray-500 uppercase">溫度</span>
                        <span class="text-sm md:text-base font-mono text-cyan-400 font-bold">
                            <?= htmlspecialchars($planetData['temperature']??'未知') ?>
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 uppercase">距離</span>
                        <span class="text-sm md:text-base font-mono text-cyan-400 font-bold">
                            <?= htmlspecialchars($planetData['distance_ly']) ?> 光年
                        </span>
                    </div>
                </div>
            </div>

            <!-- 資訊區 - 單列 -->
            <div class="space-y-3">
                
                <!-- 星球描述 -->
                <div class="bg-black/20 rounded-lg p-3 border border-white/5">
                    <p class="text-gray-300 text-sm leading-relaxed line-clamp-2">
                        <?= htmlspecialchars($planetData['description']) ?>
                    </p>
                </div>

                <!-- 許願建議 -->
                <div class="flex items-center p-3 bg-gradient-to-r from-cyan-400/50 to-transparent border-l-4 border-cyan-400 rounded-r-lg">
                    <div class="mr-2 text-xl">✨</div>
                    <div class="flex-1">
                        <p class="text-xs text-white font-bold uppercase tracking-wider mb-0.5">星象建議</p>
                        <p class="text-sm font-medium text-white line-clamp-2">
                            <?= htmlspecialchars($planetData['suggestion']) ?>
                        </p>
                    </div>
                </div>

                <!-- 許願輸入區 - 緊湊版 -->
                <form id="wish-form" class="bg-black/30 rounded-xl p-3 border border-cyan-400/30">
                    <label class="block text-cyan-400 font-orbitron text-sm mb-2">
                        ✨ 在此許下你的願望
                    </label>
                    <textarea 
                        name="wish_content"
                        id="wish-content"
                        class="w-full bg-gray-800/50 border border-gray-600 rounded-lg p-2 text-white text-sm
                            focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/50 
                            resize-none transition-all duration-300"
                        rows="2"
                        placeholder="向這顆行星傾訴你的願望..."
                        maxlength="200"
                        required
                    ></textarea>
                    <div class="flex justify-between items-center mt-2">
                        <span id="char-count" class="text-xs text-gray-500">0 / 200</span>
                        <button 
                            type="submit"
                            class="!w-48 btn-secondary-cyan">
                            送出願望
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
        
    <script>
    // 字數統計
    const textarea = document.getElementById('wish-content');
    const charCount = document.getElementById('char-count');
    
    textarea.addEventListener('input', function() {
        charCount.textContent = `${this.value.length} / 200`;
    });

    // 處理表單提交
    document.getElementById('wish-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const wishContent = textarea.value;
        
        try {
            const response = await fetch('/planets-wish/wish/store', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ wish_content: wishContent })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                window.location.href = '/planets-wish/wish';
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('送出失敗:', error);
            alert('系統錯誤');
        }
    });
    </script>
    <?php endif; ?>
</main>
<?php
};

require_once __DIR__ . '/../../layouts/layout.php';