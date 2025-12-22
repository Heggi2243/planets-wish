<?php
/**
 * wish/create.php - 行星召喚/許願頁面
 */



$pageContent = function() use ($showPlanet, $planetData) {
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
    <div id="result-section" class="w-full max-w-5xl animate-fade-in">
        <div class="w-full bg-gray-900/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-4 md:p-6 shadow-2xl">

            <!-- 星球視覺 -->
            <div class="relative w-full h-36 md:h-48 rounded-xl overflow-hidden shadow-lg border border-gray-700 group mb-4">
                <img src="<?= htmlspecialchars($planetData['image']) ?>" 
                     alt="<?= htmlspecialchars($planetData['name']) ?>" 
                     class="w-full h-full object-cover transform transition-transform duration-[20s] group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-3 left-3">
                    <h2 class="text-2xl md:text-3xl font-display font-bold text-white mb-1 drop-shadow-lg">
                        <?= htmlspecialchars($planetData['name']) ?>
                    </h2>
                    <span class="text-xs font-mono text-cyan-400 bg-cyan-900/30 px-2 py-1 rounded border border-cyan-800">
                        <?= htmlspecialchars($planetData['type']) ?>
                    </span>
                </div>
            </div>

            <!-- 下方資訊區 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2 bg-black/20 rounded-lg p-4 border border-white/5">
                    <p class="text-gray-300 italic text-sm leading-relaxed">
                        <?= htmlspecialchars($planetData['description']) ?>
                    </p>
                </div>

                <div class="bg-black/20 rounded-lg p-4 border border-white/5 space-y-3">
                    <div>
                        <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">溫度</span>
                        <span class="text-xl font-mono text-purple-300 font-bold">
                            <?= htmlspecialchars($planetData['temperature']) ?>
                        </span>
                    </div>
                    <div class="border-t border-gray-700/50 pt-3">
                        <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">距離</span>
                        <span class="text-xl font-mono text-purple-300 font-bold">
                            <?= htmlspecialchars($planetData['distance_ly']) ?> 光年
                        </span>
                    </div>
                </div>
            </div>

            <!-- 許願建議 -->
            <div class="mt-4 flex items-center p-4 bg-gradient-to-r from-purple-900/30 to-transparent border-l-4 border-purple-500 rounded-r-lg">
                <div class="mr-3 text-2xl">✨</div>
                <div class="flex-1">
                    <p class="text-xs text-purple-300 font-bold uppercase tracking-wider mb-1">星象建議</p>
                    <p class="text-sm font-medium text-white leading-relaxed">
                        <?= htmlspecialchars($planetData['suggestion']) ?>
                    </p>
                </div>
            </div>

            <!-- 許願輸入區 -->
            <form id="wish-form" class="mt-6 bg-black/30 rounded-xl p-4 border border-purple-500/30">
                <label class="block text-purple-300 font-orbitron text-sm mb-2">
                    ✨ 在此許下你的願望
                </label>
                <textarea 
                    name="wish_content"
                    id="wish-content"
                    class="w-full bg-gray-800/50 border border-gray-600 rounded-lg p-3 text-white 
                           focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 
                           resize-none transition-all duration-300"
                    rows="3"
                    placeholder="向這顆行星傾訴你的願望..."
                    maxlength="200"
                    required
                ></textarea>
                <div class="flex justify-between items-center mt-2">
                    <span id="char-count" class="text-xs text-gray-500">0 / 200</span>
                    <button 
                        type="submit"
                        class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600
                               text-white font-orbitron font-bold py-2 px-6 rounded-lg
                               transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-purple-500/50">
                        送出願望
                    </button>
                </div>
            </form>

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