<?php
/**
 * 行星許願頁面
 * 已套用layout
 */

// 頁面標題
$pageTitle = 'Planets-Wish | 星願紀錄';

// 引入JS
$additionalJS = ['../js/wishRecord.js'];

$pageContent = function() {
?>

            <!-- Filter Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mt-8 mb-12" id="filter-container">
                <button data-filter="all" class="filter-btn px-4 py-2 rounded-xl border border-white/40 bg-white/20 shadow-lg scale-105 transition-all">全部紀錄</button>
                <button data-filter="traveling" class="filter-btn px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-gray-400 hover:bg-white/10 transition-all">航行中</button>
                <button data-filter="arrived" class="filter-btn px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-gray-400 hover:bg-white/10 transition-all">已抵達</button>
                <button data-filter="failed" class="filter-btn px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-gray-400 hover:bg-white/10 transition-all">已失聯</button>
            </div>
        </header>

        <!-- Wishes Grid -->
        <main class="max-w-6xl mx-auto">
            <div id="wishes-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Cards will be injected here -->
            </div>
            <div id="empty-state" class="hidden text-center py-20 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-md">
                <div class="text-5xl mb-4">✨</div>
                <p class="text-gray-400">目前沒有對應狀態的願望。</p>
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
                    <div id="modal-header-info" class="flex items-center gap-3">
                        <!-- Type and status injected here -->
                    </div>
                    <button id="close-modal" class="text-gray-400 hover:text-white transition-colors p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <h3 id="modal-date" class="text-gray-400 text-sm font-mono mb-2"></h3>
                <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                    <p id="modal-text" class="text-gray-100 text-lg leading-relaxed whitespace-pre-wrap italic"></p>
                </div>
                <div class="mt-8 flex justify-center">
                    <button id="close-modal-btn" class="px-8 py-3 bg-white/10 hover:bg-white/20 text-white rounded-full border border-white/20 transition-all font-bold">關閉星窗</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Star background generation
        const starContainer = document.getElementById('star-container');
        for (let i = 0; i < 150; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            const size = Math.random() * 2 + 1 + 'px';
            star.style.width = size;
            star.style.height = size;
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.setProperty('--duration', (Math.random() * 3 + 2) + 's');
            starContainer.appendChild(star);
        }
    </script>
<?php
};

// 引入layout
require_once __DIR__ . '/../layouts/layout.php';