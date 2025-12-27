<?php
/**
 * wish/result.php - 召喚結果頁面
 */

$pageContent = function() use ($wish, $planetData, $categoryImg) {
    $isSuccess = $wish['is_success'] == 1;
?>
<main class="flex items-center justify-center h-[calc(100vh-180px)] relative px-4 overflow-hidden">
    
    <!-- 結果容器 -->
    <div class="w-full max-w-3xl animate-fade-in">
        
        <!-- 行星主視覺 - 縮小版 -->
        <div class="text-center mb-4">
            <div class="relative inline-block">
                <!-- 外環光暈 -->
                <div class="absolute -inset-4 bg-gradient-to-r from-<?= $isSuccess ? 'cyan' : 'purple' ?>-400 via-<?= $isSuccess ? 'purple' : 'pink' ?>-400 to-<?= $isSuccess ? 'pink' : 'red' ?>-400 rounded-full blur-xl opacity-50 animate-pulse"></div>
                
                <!-- 行星圖片 - 縮小尺寸 -->
                <div class="relative w-40 h-40 md:w-64 md:h-64 animate-float">
                    <img 
                        src="../../img/<?= $categoryImg ? 'planet_' . $categoryImg : 'wormhole' ?>.png"
                        alt="<?= htmlspecialchars($planetData['name']) ?>"
                        class="w-full h-full object-contain drop-shadow-2xl"
                        onerror="this.src='../../img/wormhole.png'"
                    />
                </div>
            </div>
            
            <!-- 行星名稱 - 緊湊間距 -->
            <h1 class="mt-3 text-2xl md:text-3xl font-display font-bold text-white drop-shadow-lg">
                <?= htmlspecialchars($planetData['name']) ?>
            </h1>
            <p class="mt-1 text-gray-400 text-xs">
                距離地球 <?= htmlspecialchars($planetData['distance_ly']) ?> 光年
            </p>
        </div>
        
        <!-- 結果卡片 - 緊湊版 -->
        <div class="glass-panel rounded-2xl p-4 md:p-6 shadow-2xl mb-3">
            <?php if ($isSuccess): ?>
                <!-- 成功結果 -->
                <div class="text-center mb-4">

                    <h2 class="text-xl md:text-2xl font-orbitron font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400 mb-1">
                        星願成真！
                    </h2>
                    <p class="text-gray-300 text-sm">
                        你的願望與 <?= htmlspecialchars($planetData['name']) ?> 的能量完美共鳴，該行星已被你成功蒐集。
                    </p>
                </div>
            <?php else: ?>
                <!-- 失敗結果 -->
                <div class="text-center mb-4">

                    <h2 class="text-xl md:text-2xl font-orbitron font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 mb-1">
                        能量未能共鳴
                    </h2>
                    <p class="text-gray-300 text-sm">
                    蒐集失敗，<?= htmlspecialchars($planetData['name']) ?> 的能量與你的願望方向不同，明天再來邂逅新的行星吧。
                    </p>
                </div>
            <?php endif; ?>
            
            <!-- 返回按鈕 -->
            <div class="text-center mt-4">
                <a href="/planets-wish/wish" class="btn-primary-gradient inline-block px-8 py-2.5">
                    返回主頁
                </a>
            </div>
        </div>
        
    </div>
    
    <!-- 星星裝飾 -->
    <div class="absolute top-10 left-10 w-2 h-2 bg-yellow-400 rounded-full animate-ping"></div>
    <div class="absolute top-20 right-20 w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></div>
    <div class="absolute bottom-20 left-20 w-2 h-2 bg-purple-400 rounded-full animate-ping"></div>
    <div class="absolute bottom-10 right-10 w-2 h-2 bg-pink-400 rounded-full animate-pulse"></div>
</main>
<?php
};

require_once __DIR__ . '/../../layouts/layout.php';