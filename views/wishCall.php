<?php
/**
 * wishCall.php 行星召喚頁面
 * 已套用layout
 */

session_start();

// TODO: 後續會改成從資料庫取得真實行星資料

// 模擬假資料
function generatePlanet() {
    $planets = [
        [
            'name' => '紫羅星',
            'name_en' => 'Violeta',
            'type' => '氣態巨行星',
            'image' => '../img/planet-purple.jpg',
            'temperature' => '-150°C',
            'distance' => '4.2 光年',
            'description' => '一顆擁有夢幻紫色大氣層的神秘行星，據說能感應到旅者內心最深處的渴望。',
            'suggestion' => '適合許下關於內心成長與自我探索的願望。'
        ],
        [
            'name' => '碧海星',
            'name_en' => 'Aquamaris',
            'type' => '海洋行星',
            'image' => '../img/planet-blue.jpg',
            'temperature' => '22°C',
            'distance' => '8.7 光年',
            'description' => '表面被廣闊海洋覆蓋的蔚藍行星，象徵著情感的流動與心靈的平靜。',
            'suggestion' => '適合許下關於情感、人際關係與心靈療癒的願望。'
        ],
        [
            'name' => '焰陽星',
            'name_en' => 'Pyrion',
            'type' => '熔岩行星',
            'image' => '../img/planet-red.jpg',
            'temperature' => '850°C',
            'distance' => '12.3 光年',
            'description' => '熾熱的熔岩在地表流動，象徵著熱情、勇氣與轉變的力量。',
            'suggestion' => '適合許下關於事業突破、勇氣挑戰與人生轉變的願望。'
        ]
    ];
    
    return $planets[array_rand($planets)];
}

// 檢查是否點擊太空人
$showPlanet = isset($_GET['summon']) && $_GET['summon'] === 'true';


// 如果要顯示行星，生成資料
$planetData = null;
if ($showPlanet) {
    $planetData = generatePlanet();
    
    // TODO: 後續會儲存到資料庫
    // 暫時存在 session
    $_SESSION['current_planet'] = $planetData;
}

// 頁面標題
$pageTitle = 'Planets-Wish | 邂逅行星';

$pageContent = function() use ($showPlanet, $planetData) {
?>
<main class="flex flex-col items-center justify-center min-h-[calc(100vh-180px)] relative">
    
    <?php if (!$showPlanet): ?>
    <!-- 太空人啟動畫面 (初始顯示) -->
    <div id="astronaut-intro" class="flex flex-col items-center justify-center animate-fade-in">
        <!-- 太空人圖片 -->
        <a href="?summon=true" class="relative group cursor-pointer">
            <!-- 發光效果 -->
            <div class="absolute -inset-4 rounded-full blur-xl opacity-75 group-hover:opacity-100 transition duration-500 animate-pulse"></div>
            
            <!-- 太空人 -->
            <div class="relative w-64 h-64 md:w-90 md:h-90 transform transition-all duration-500 group-hover:scale-110 group-hover:-translate-y-4">
                <img 
                    src="../img/astronaut03.png" 
                    alt="Astronaut" 
                    class="w-full h-full object-contain drop-shadow-2xl"
                />
            </div>
        </a>
        
        <!-- 提示文字 -->
        <p class="mt-8 text-gray-400 text-center animate-bounce">
            <span class="block text-lg md:text-xl font-orbitron text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400 font-bold mb-2">
                準備好了嗎？
            </span>
            <span class="text-sm md:text-base">
                點擊太空人，邂逅你的星願
            </span>
        </p>
        
        <!-- 裝飾性星星 -->
        <div class="absolute top-20 left-1/4 w-2 h-2 bg-yellow-400 rounded-full animate-ping"></div>
        <div class="absolute top-40 right-1/3 w-3 h-3 bg-cyan-400 rounded-full animate-pulse"></div>
        <div class="absolute bottom-32 left-1/3 w-2 h-2 bg-purple-400 rounded-full animate-ping" style="animation-delay: 0.5s;"></div>
    </div>
    
    <?php else: ?>
    <!-- 星球資訊區 (點擊太空人後顯示) -->
    <div id="result-section" class="w-full max-w-5xl animate-fade-in">
        <div class="w-full bg-gray-900/40 backdrop-blur-md border border-gray-700/50 rounded-2xl p-4 md:p-6 shadow-2xl">

            <!-- 星球視覺 -->
            <div class="relative w-full h-36 md:h-48 rounded-xl overflow-hidden shadow-lg border border-gray-700 group mb-4">
                <img src="<?php echo htmlspecialchars($planetData['image']); ?>" 
                     alt="<?php echo htmlspecialchars($planetData['name']); ?>" 
                     class="w-full h-full object-cover transform transition-transform duration-[20s] group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-3 left-3">
                    <h2 class="text-2xl md:text-3xl font-display font-bold text-white mb-1 drop-shadow-lg">
                        <?php echo htmlspecialchars($planetData['name']); ?>
                    </h2>
                    <span class="text-xs font-mono text-cyan-400 bg-cyan-900/30 px-2 py-1 rounded border border-cyan-800">
                        <?php echo htmlspecialchars($planetData['type']); ?>
                    </span>
                </div>
            </div>

            <!-- 下方資訊區 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- 星球描述 -->
                <div class="md:col-span-2 bg-black/20 rounded-lg p-4 border border-white/5">
                    <p class="text-gray-300 italic text-sm leading-relaxed">
                        <?php echo htmlspecialchars($planetData['description']); ?>
                    </p>
                </div>

                <!-- 屬性數據 -->
                <div class="bg-black/20 rounded-lg p-4 border border-white/5 space-y-3">
                    <div>
                        <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">溫度</span>
                        <span class="text-xl font-mono text-purple-300 font-bold">
                            <?php echo htmlspecialchars($planetData['temperature']); ?>
                        </span>
                    </div>
                    <div class="border-t border-gray-700/50 pt-3">
                        <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">距離</span>
                        <span class="text-xl font-mono text-purple-300 font-bold">
                            <?php echo htmlspecialchars($planetData['distance']); ?>
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
                        <?php echo htmlspecialchars($planetData['suggestion']); ?>
                    </p>
                </div>
            </div>

            <!-- 許願輸入區 -->
            <form method="POST" action="./processWish.php" class="mt-6 bg-black/30 rounded-xl p-4 border border-purple-500/30">
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
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('wish-content');
        const charCount = document.getElementById('char-count');
        
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = `${count} / 200`;
        });
    });
    </script>
    <?php endif; ?>
</main>

<?php
};

// 引入layout
require_once __DIR__ . '/../layouts/layout.php';