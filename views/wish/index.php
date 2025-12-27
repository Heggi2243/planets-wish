<?php
/**
 * wish/index.php 登入後主畫面
 * 已套用layout
 */

$pageScript = ['../../js/wishIndex.js'];

$pageContent = function() use ($hasWishedToday, $latestWish, $successMessage, $errorMessage, $isSummoned) {
?>
    <!-- 成功訊息提示 -->
    <?php if ($successMessage): ?>
    <div id="success-toast" class="fixed top-20 right-4 z-50 glass-panel rounded-lg p-4 shadow-2xl animate-slide-in-right">
        <div class="flex items-center gap-3">
            <span class="text-2xl">✨</span>
            <p class="text-white"><?= htmlspecialchars($successMessage) ?></p>
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('success-toast')?.remove();
        }, 5000);
    </script>
    <?php endif; ?>

    <!-- 綠色行星裝飾 + 倒數計時 -->
    <!-- 綠色行星裝飾 + 倒數計時 -->
    <div class="absolute top-20 right-10 md:right-1/4 z-20 flex flex-col items-center">
        <?php 
        // 檢查是否已抵達且未查看(只有traveling才發光)
        $isArrived = false;
        if ($latestWish && $latestWish['status'] === 'traveling' && $latestWish['arrival_at']) {
            $now = new DateTime();
            $arrivalTime = new DateTime($latestWish['arrival_at']);
            $isArrived = ($now >= $arrivalTime);
        }
        ?>
        
        <?php if ($isArrived): ?>
            <!-- 已抵達且未查看：發光特效 + 可點擊 -->
            <a href="/planets-wish/wish/result" class="block group relative">
                <!-- 發光外環 -->
                <div class="absolute -inset-4 bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-400 rounded-full blur-xl opacity-75 animate-pulse"></div>
                
                <!-- 行星圖片 -->
                <div class="relative w-24 h-24 md:w-32 md:h-32 animate-float transform transition-all duration-300 group-hover:scale-110">
                    <img src="../../img/planet2.png" class="w-full h-full object-contain drop-shadow-2xl" />
                </div>
                
                <!-- 提示文字 -->
                <p class="mt-2 text-xs text-white font-bold animate-bounce text-center">
                    點擊查看結果
                </p>
            </a>
        <?php else: ?>
            <!-- 其他情況：一般行星圖片（未抵達 / 已查看 / 無願望） -->
            <div class="w-24 h-24 md:w-32 md:h-32 animate-float">
                <img src="../../img/planet2.png" class="w-full h-full object-contain drop-shadow-xl transform" />
            </div>
        <?php endif; ?>
        
        <!-- 倒數計時：只在 traveling 且未抵達時顯示 -->
        <?php if ($latestWish && $latestWish['status'] === 'traveling' && $latestWish['arrival_at'] && !$isArrived): ?>
        <div class="mt-4 text-center animate-fade-in">
            <p class="text-xs text-white mb-2">行星抵達倒數</p>
            <div id="countdown" 
                 class="glass-panel rounded-lg px-4 py-2 font-mono text-cyan-400 font-bold text-sm md:text-base"
                 data-arrival="<?= htmlspecialchars($latestWish['arrival_at']) ?>">
                計算中...
            </div>
            <p class="text-xs text-white mt-2">
                <?= htmlspecialchars($latestWish['planet_name']) ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- 許願紀錄 -->
    <a href="/planets-wish/wish/record"
        class="group absolute top-30 left-10 w-32 h-32 md:w-64 md:h-64 z-20 animate-float transition-transform duration-300 cursor-pointer flex flex-col items-center text-decoration-none">
        <div class="w-32 h-32 md:w-56 md:h-56">
            <img
                src="../../img/satellite.png"
                class="w-full h-full object-contain transform transition-all duration-300 
                    drop-shadow-xl 
                    group-hover:drop-shadow-[0_0_20px_rgba(56,189,248,0.9)]
                    group-hover:scale-110
                    group-hover:-translate-y-2"
            />
        </div>
        <span class="mt-4 text-white font-orbitron text-sm tracking-widest
                 opacity-0 transform translate-y-4 transition-all duration-300 ease-out
                 group-hover:opacity-100 group-hover:translate-y-0">
            星願紀錄
        </span>
    </a>

   
    <main class="container mx-auto px-4 flex flex-col items-center justify-center relative z-10" 
          style="min-height: calc(100vh - 180px); max-height: calc(100vh - 180px);">
        
        <!-- 核心互動區: 傳送門 -->
        <div id="portal-container" class="relative transition-all duration-700 ease-in-out">
            <div class="relative group">
                <!-- 傳送門外環動畫 -->
                <div class="absolute -inset-1 bg-gradient-to-r from-orange-500 via-yellow-500 to-orange-600 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-pulse-fast"></div>
                
                <?php if ($hasWishedToday && !$isSummoned): ?>
                    <!-- 已許願且已送出->顯示提示框 -->
                    <button id="summon-btn" class="relative w-48 h-48 md:w-64 md:h-64 rounded-full flex items-center justify-center portal-glow animate-float overflow-visible cursor-pointer">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img 
                                src="../../img/planet3.png" 
                                alt="Planet Portal" 
                                class="w-full h-full object-contain animate-spin-slow"
                            />
                        </div>
                    </button>
                <?php else: ?>
                    <!-- 未許願or已召喚但未送出->可點擊跳轉 -->
                    <a href="/planets-wish/wish/create" class="relative w-48 h-48 md:w-64 md:h-64 rounded-full flex items-center justify-center portal-glow animate-float overflow-visible block">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img 
                                src="../../img/planet3.png" 
                                alt="Planet Portal" 
                                class="w-full h-full object-contain animate-spin-slow"
                            />
                        </div>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- 根據狀態顯示不同提示 -->
            <p class="text-center text-gray-400 mt-8 animate-bounce text-sm">
                <?php if ($isSummoned): ?>
                    你的行星已現身，請完成星願
                <?php elseif ($hasWishedToday): ?>
                    今天已許願，明日再與星辰相見
                <?php else: ?>
                    今天會與哪個行星邂逅呢？點擊許願
                <?php endif; ?>
            </p>
        </div>
        
    </main>

    <!-- 流星裝飾 -->
    <div class="absolute bottom-0 w-full">
        <div class="absolute bottom-20 right-30 w-32 h-32 md:w-56 md:h-56 z-20 animate-float">
            <img src="../../img/meteor.png" class="w-full h-full object-contain drop-shadow-xl" />
        </div>

        <div class="absolute bottom-20 left-10 w-32 h-32 md:w-56 md:h-56 z-20 animate-float">
            <img src="../../img/UFO.png" style="transform: rotate(-12deg);" class="w-full h-full object-contain" />
        </div>
    </div>

    <?php if ($hasWishedToday && !$isSummoned): ?>
    <!-- 背景遮罩(預設隱藏) - 只在已許願且已送出時顯示 -->
    <div id="daily-limit-modal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center">
        <!-- 提示框 -->
        <div class="glass-panel rounded-2xl p-8 max-w-md mx-4 shadow-2xl animate-fade-in">
            <div class="text-center mb-6">
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const summonBtn = document.getElementById('summon-btn');
            const modal = document.getElementById('daily-limit-modal');
            const closeBtn = document.getElementById('close-modal-btn');
            
            // 點擊傳送門:顯示提示
            summonBtn?.addEventListener('click', function() {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
            
            // 關閉Modal
            function closeModal() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
            
            closeBtn?.addEventListener('click', closeModal);
            modal?.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });
        });
        </script>

        <?php endif; ?>

        <?php if ($hasWishedToday && $latestWish && $latestWish['arrival_at']): ?>
        <script>
        (function() {
            const countdownEl = document.getElementById('countdown');
            
            if (!countdownEl) return;
            
            const arrivalTime = new Date(countdownEl.dataset.arrival).getTime();
            const wishStatus = '<?= $latestWish['status'] ?? '' ?>';  // 取得status
            
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = arrivalTime - now;
                
                if (distance < 0) {
                    // 只有在traveling狀態才自動刷新
                    if (wishStatus === 'traveling') {
                        location.reload();
                    } else {
                        // 已經查看過了，顯示已抵達
                        countdownEl.innerHTML = '<span class="text-white">✨ 已抵達</span>';
                        clearInterval(interval);
                    }
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                let timeStr = '';
                if (days > 0) timeStr += `${days}天 `;
                timeStr += `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                countdownEl.textContent = timeStr;
            }
            
            updateCountdown();
            const interval = setInterval(updateCountdown, 1000);
        })();
    </script>
    <?php endif; ?>
<?php
};

// 引入layout
require_once __DIR__ . '/../../layouts/layout.php';