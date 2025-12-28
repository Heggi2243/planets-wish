<?php
/**
 * wish/record.php - 星願紀錄頁面
 */

$pageTitle = 'Planets-Wish | 星願紀錄';

$pageContent = function() use ($wishes, $collectedCount, $totalTypes, $collectedTypes) {
?>
<div class="min-h-screen relative">
    <!-- 星星背景 -->
    <div id="star-container" class="fixed inset-0 pointer-events-none z-0"></div>
    
    <div class="relative z-10 container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="text-center mb-8">
            
            <!-- 回首頁 -->
            <a href="/planets-wish/wish" 
               class="absolute top-0 left-0 group flex items-center gap-2 glass-panel rounded-xl px-4 py-2 
                      border border-white/30 hover:border-cyan-400 transition-all duration-300
                      hover:shadow-[0_0_20px_rgba(34,211,238,0.5)]">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="w-5 h-5 text-white group-hover:text-cyan-400 transition-colors" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="text-white text-sm font-orbitron group-hover:text-cyan-400 transition-colors">
                    返回首頁
                </span>
            </a>

            <div class="absolute top-0 right-0 glass-panel rounded-xl px-4 py-2">
                <div class="flex items-center gap-2">
                    <span class="text-cyan-400">星種蒐集</span>
                    <span class="text-white font-bold"><?= $collectedCount ?> / <?= $totalTypes ?></span>
                </div>
                <!-- 進度條 -->
                <div class="bg-gray-700 rounded-full h-2">
                    <div class="bg-gradient-to-r from-cyan-400 to-purple-400 h-2 rounded-full" 
                        style="width: <?= round(($collectedCount / $totalTypes) * 100) ?>%"></div>
                </div>
            </div>

           <?php if ($collectedCount >= 1): ?>
                <div class="rounded-2xl p-4">
                    <div class="flex flex-wrap gap-6 justify-center items-center">
                        <?php foreach ($collectedTypes as $type): ?>
                            <div class="group relative">
                                <div class="w-20 h-20 md:w-24 md:h-24 animate-float">
                                    <img class="w-full h-full object-contain drop-shadow-xl transition-transform duration-300 group-hover:scale-110"
                                        src="../../img/planet_<?= $type ?>.png" 
                                        alt="collected planet" />
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filter Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mt-4 mb-12">
                <button data-filter="all" class="filter-btn active px-4 py-2 rounded-xl border border-white/40 bg-white/20 text-white shadow-lg scale-105 transition-all">
                    全部紀錄
                </button>
                <button data-filter="summoned" class="filter-btn px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-white hover:bg-white/10 transition-all">
                    已召喚
                </button>
                <button data-filter="traveling" class="filter-btn px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-white hover:bg-white/10 transition-all">
                    航行中
                </button>
                <button data-filter="success" class="filter-btn px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-white hover:bg-white/10 transition-all">
                    蒐集成功
                </button>
                <button data-filter="failed" class="filter-btn px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-white hover:bg-white/10 transition-all">
                    蒐集失敗
                </button>
            </div>
        </header>

        <!-- Wishes Grid -->
        <main class="max-w-6xl mx-auto">
            <div id="wishes-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($wishes)): ?>
                    <div class="col-span-full text-center py-20 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-md">
                        <p class="text-white">還沒有許願紀錄，快去召喚行星吧！</p>
                        <a href="/planets-wish/wish" class="mt-6 inline-block btn-primary-gradient">
                            前往許願
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($wishes as $wish): ?>
                        <?php
                        // 狀態配置
                        $statusConfig = [
                            'summoned' => [
                                'label' => '已召喚',
                                'color' => 'text-white',
                                'dot' => 'bg-gray-400',
                                'border' => 'from-gray-400 to-gray-600',
                                'glow' => ''
                            ],
                            'traveling' => [
                                'label' => '航行中',
                                'color' => 'text-cyan-400',
                                'dot' => 'bg-cyan-400',
                                'border' => 'from-cyan-400 to-blue-500',
                                'glow' => 'hover:shadow-[0_0_30px_rgba(34,211,238,0.3)]'
                            ],
                            'success' => [
                                'label' => '蒐集成功',
                                'color' => 'text-purple-400',
                                'dot' => 'bg-purple-400',
                                'border' => 'from-green-400 to-emerald-500',
                                'glow' => 'hover:shadow-[0_0_30px_rgba(74,222,128,0.3)]'
                            ],
                            'failed' => [
                                'label' => '蒐集失敗',
                                'color' => 'text-gray-400',
                                'dot' => 'bg-gray-400',
                                'border' => 'from-red-400 to-pink-500',
                                'glow' => 'hover:shadow-[0_0_30px_rgba(248,113,113,0.3)]'
                            ]
                        ];

                        // 蒐集成功/蒐集失敗：只有當使用者checked結果才會顯示，不提前公布結果
                        $displayStatus = $wish['status'];
                        if ($wish['status'] === 'checked') {
                            $displayStatus = $wish['is_success'] == 1 ? 'success' : 'failed';
                        }

                        $status = $statusConfig[$displayStatus] ?? $statusConfig['summoned'];
                        $dateStr = explode(' ', $wish['created_at'])[0];
                        ?>
                        
                        <div 
                            data-status="<?= $wish['status'] ?>"
                            data-is-success="<?= $wish['is_success'] ?>"
                            data-wish-id="<?= $wish['id'] ?>"
                            data-display-status="<?= $displayStatus ?>"
                            onclick="showWishDetail(<?= $wish['id'] ?>)"
                            class="wish-card relative group cursor-pointer transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]
                                bg-white/10 backdrop-blur-lg border border-white/10 rounded-2xl p-6 overflow-hidden flex flex-col gap-4 <?= $status['glow'] ?>">
                                                        
                            <div class="flex justify-between items-start">
                                <span class="px-3 py-1 text-xs font-medium rounded-full border border-cyan-400/30 bg-cyan-900/30 text-cyan-400">
                                    <?= htmlspecialchars($wish['rpg_type']) ?>型行星
                                </span>
                                <span class="text-gray-400 text-xs font-mono">
                                    <?= $dateStr ?>
                                </span>
                            </div>

                            <div class="flex-grow">
                                <h3 class="text-white font-bold mb-2">
                                    <?= htmlspecialchars($wish['planet_name']) ?>
                                </h3>
                                <p class="text-gray-200 text-sm leading-relaxed line-clamp-3">
                                    <?= htmlspecialchars($wish['wish_content'] ?? '尚未填寫願望') ?>
                                </p>
                            </div>

                            <div class="flex items-center gap-2 pt-2">
                                <div class="w-2 h-2 rounded-full <?= $status['dot'] ?> <?= in_array($displayStatus, ['traveling', 'success']) ? 'animate-pulse' : '' ?>"></div>
                                <span class="text-xs font-bold tracking-wider uppercase <?= $status['color'] ?>">
                                    <?= $status['label'] ?>
                                </span>
                            </div>

                            <!-- 裝飾線 -->
                            <div class="line-neon-top"></div>
                            <div class="line-neon-bottom"></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Empty State -->
            <div id="empty-state" class="hidden text-center py-20 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-md">
                <p class="text-white">目前沒有對應狀態的星願。</p>
            </div>
        </main>
    </div>

    <!-- Modal Detail Window -->
    <div id="wish-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" id="modal-backdrop"></div>
        <div class="relative w-full max-w-lg bg-slate-900/90 border border-white/20 backdrop-blur-xl rounded-3xl overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300" id="modal-content">
            <div id="modal-accent" class="h-1 w-full bg-gradient-to-r"></div>
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <div id="modal-header-info" class="flex items-center gap-3"></div>
                    <button id="close-modal" class="text-gray-400 hover:text-white transition-colors p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <h3 id="modal-planet" class="text-white text-xl font-bold mb-2"></h3>
                <p id="modal-date" class="text-gray-400 text-sm font-mono mb-4"></p>
                <div class="bg-white/5 rounded-2xl p-6 border border-white/10 mb-4">
                    <p id="modal-text" class="text-gray-100 text-lg leading-relaxed whitespace-pre-wrap italic"></p>
                </div>
                <div id="modal-arrival" class="text-sm text-gray-400 mb-6"></div>
                <div class="flex justify-center">
                    <button id="close-modal-btn" class="px-8 py-3 bg-white/10 hover:bg-white/20 text-white rounded-full border border-white/20 transition-all font-bold">
                        關閉星窗
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    const wishesData = <?= json_encode($wishes) ?>;

    // 星星背景生成
    const starContainer = document.getElementById('star-container');
    for (let i = 0; i < 150; i++) {
        const star = document.createElement('div');
        star.className = 'absolute bg-white rounded-full';
        const size = Math.random() * 2 + 1 + 'px';
        star.style.width = size;
        star.style.height = size;
        star.style.left = Math.random() * 100 + '%';
        star.style.top = Math.random() * 100 + '%';
        star.style.opacity = Math.random() * 0.5 + 0.3;
        star.style.animation = `twinkle ${Math.random() * 3 + 2}s infinite`;
        starContainer.appendChild(star);
    }

    // 篩選功能
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.filter;
            
            // 更新按鈕樣式
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('bg-white/20', 'border-white/40', 'shadow-lg', 'scale-105', 'active');
                b.classList.add('bg-white/5', 'border-white/10');
            });
            btn.classList.add('bg-white/20', 'border-white/40', 'shadow-lg', 'scale-105', 'active');
            btn.classList.remove('bg-white/5', 'border-white/10');

            // 篩選卡片
            const cards = document.querySelectorAll('.wish-card');
            const emptyState = document.getElementById('empty-state');
            let visibleCount = 0;

            cards.forEach(card => {
                const status = card.dataset.status;
                const isSuccess = card.dataset.isSuccess;
                const displayStatus = card.dataset.displayStatus;
                
                let shouldShow = false;
                
                if (filter === 'all') {
                    shouldShow = true;
                } else if (filter === 'summoned' && status === 'summoned') {
                    shouldShow = true;
                } else if (filter === 'traveling' && status === 'traveling') {
                    shouldShow = true;
                } else if (filter === 'success' && status === 'checked' && isSuccess == '1') {
                    shouldShow = true;
                } else if (filter === 'failed' && status === 'checked' && isSuccess == '0') {
                    shouldShow = true;
                }
                
                if (shouldShow) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // 顯示/隱藏空狀態
            if (visibleCount === 0) {
                document.getElementById('wishes-container').style.display = 'none';
                emptyState.classList.remove('hidden');
            } else {
                document.getElementById('wishes-container').style.display = 'grid';
                emptyState.classList.add('hidden');
            }
        });
    });

    // 顯示願望詳情
    function showWishDetail(wishId) {
        const wish = wishesData.find(w => w.id == wishId);
        if (!wish) return;

        const modal = document.getElementById('wish-modal');
        const modalContent = document.getElementById('modal-content');
        
        // 填充內容
        document.getElementById('modal-planet').textContent = wish.planet_name;
        document.getElementById('modal-date').textContent = wish.created_at;
        document.getElementById('modal-text').textContent = wish.wish_content || '尚未填寫願望';
        
        if (wish.arrival_at) {
            document.getElementById('modal-arrival').textContent = `抵達時間：${wish.arrival_at}`;
        } else {
            document.getElementById('modal-arrival').textContent = '';
        }

        // 顯示彈窗
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // 關閉彈窗
    function closeModal() {
        const modal = document.getElementById('wish-modal');
        const modalContent = document.getElementById('modal-content');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('close-modal').addEventListener('click', closeModal);
    document.getElementById('close-modal-btn').addEventListener('click', closeModal);
    document.getElementById('modal-backdrop').addEventListener('click', closeModal);
    </script>

    <style>
    @keyframes twinkle {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 1; }
    }
    </style>
</div>
<?php
};

require_once __DIR__ . '/../../layouts/layout.php';