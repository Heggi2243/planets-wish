<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planets-wish | 星願</title>
    
    <link rel="stylesheet" href="../css/input.css">
    <link href="../src/output.css" rel="stylesheet">
    <!-- 引入 Google Fonts: Orbitron (科技感字體) -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Noto+Sans+TC:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="text-white font-body antialiased flex flex-col items-center justify-center relative">

    <!-- 星空背景層 -->
    <div class="stars"></div>
    <div class="twinkling"></div>

    <!-- Header -->
    <header class="w-full p-6 text-center z-20 relative">
        <h1 class="font-orbitron text-2xl md:text-4xl font-bold tracking-[0.2em] text-transparent bg-clip-text bg-gradient-to-r from-neon-cyan to-neon-purple drop-shadow-[0_0_10px_rgba(0,242,255,0.5)]">
            PLANETS WISH
        </h1>
    </header>

    <!-- Main Container -->
    <main class="container mx-auto px-4 flex flex-col items-center justify-center min-h-screen py-20 relative z-10">
        
        <!-- 1. 核心互動區：傳送門 -->
        <div id="portal-container" class="relative transition-all duration-700 ease-in-out">
            <div class="relative group">
                <!-- 傳送門外環動畫 -->
                <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-pulse-fast"></div>
                
                <!-- 傳送門本體 -->
                <button id="summon-btn" class="relative w-48 h-48 md:w-64 md:h-64 rounded-full bg-black flex items-center justify-center portal-glow animate-float overflow-hidden">
                    <!-- 內部旋渦圖案 (CSS模擬) -->
                    <div class="absolute inset-0 bg-[url('https://cdn.pixabay.com/photo/2017/04/04/14/26/pluto-2201446_1280.png')] bg-cover opacity-90 animate-spin-slow"></div>
                    
                    <span class="relative z-10 font-orbitron text-xl md:text-2xl text-white font-bold tracking-widest pointer-events-none group-hover:scale-110 transition-transform duration-300">
                        
                    </span>
                </button>
            </div>
            <p class="text-center text-gray-400 mt-8 animate-bounce text-sm">今天會與哪個行星邂逅呢？點擊開啟</p>
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

    <!-- JavaScript 邏輯 -->
    <script>
        // --- 1. 假資料 (Mock Data) ---
        const planetsData = [
            {
                name: "Kepler-186f",
                type: "類地行星",
                image: "https://images.unsplash.com/photo-1614730341194-75c60740a0d3?q=80&w=2000&auto=format&fit=crop",
                desc: "位於適居帶的神秘紅色星球，雖然光照只有地球的三分之一，但充滿了未知的生命潛力。",
                temp: "180 K",
                dist: "582 光年",
                suggestion: "這顆星球適合「沈澱心靈」或「重新開始」的願望。",
                recommendedType: "other"
            },
            {
                name: "55 Cancri e",
                type: "超級地球 / 鑽石星球",
                image: "https://images.unsplash.com/photo-1614313511387-1436a4480ebb?q=80&w=2000&auto=format&fit=crop",
                desc: "表面覆蓋著石墨和鑽石的炙熱行星，象徵著堅硬與永恆的價值。",
                temp: "2700 K",
                dist: "41 光年",
                suggestion: "強烈建議許下關於「財富積累」或「堅定愛情」的願望。",
                recommendedType: "wealth"
            },
            {
                name: "Proxima Centauri b",
                type: "紅矮星系行星",
                image: "https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2000&auto=format&fit=crop",
                desc: "距離太陽系最近的系外行星，受潮汐鎖定影響，一面永晝一面永夜。",
                temp: "234 K",
                dist: "4.2 光年",
                suggestion: "適合許下關於「鄰里關係」、「短程旅行」或「快速見效」的願望。",
                recommendedType: "career"
            },
            {
                name: "Gliese 581g",
                type: "岩石行星",
                image: "https://images.unsplash.com/photo-1462331940025-496dfbfc7564?q=80&w=2000&auto=format&fit=crop",
                desc: "被稱為『Zarmina的世界』，位於天秤座，氣候環境極其穩定。",
                temp: "250 K",
                dist: "20.3 光年",
                suggestion: "星象顯示有利於「健康療癒」與「家庭和諧」。",
                recommendedType: "health"
            },
            {
                name: "HD 189733b",
                type: "熱木星",
                image: "https://images.unsplash.com/photo-1545156521-77bd85671d30?q=80&w=2000&auto=format&fit=crop",
                desc: "美麗的深藍色星球，但大氣中下著玻璃雨，風速極快，象徵混亂中的秩序。",
                temp: "1100 K",
                dist: "64.5 光年",
                suggestion: "適合許下「突破困境」或「學業猛進」這種需要強大動力的願望。",
                recommendedType: "academic"
            }
        ];

        // --- 2. DOM 元素選取 ---
        const portalBtn = document.getElementById('summon-btn');
        const portalContainer = document.getElementById('portal-container');
        const resultSection = document.getElementById('result-section');

        // 星球資訊 DOM
        const planetImage = document.getElementById('planet-image');
        const planetName = document.getElementById('planet-name');
        const planetType = document.getElementById('planet-type');
        const planetDesc = document.getElementById('planet-desc');
        const planetTemp = document.getElementById('planet-temp');
        const planetDist = document.getElementById('planet-dist');
        const wishSuggestion = document.getElementById('wish-suggestion');

        // --- 3. 互動邏輯 ---

        // 點擊傳送門
        portalBtn.addEventListener('click', () => {
            // 1. 動畫：傳送門縮小並淡出
            portalContainer.classList.add('opacity-0', 'scale-0', 'pointer-events-none');
            
            // 2. 顯示結果區域
            setTimeout(() => {
                portalContainer.classList.add('hidden');
                resultSection.classList.remove('hidden');
                
                // 執行選取星球邏輯
                summonPlanet();
                
                // 捲動到頂部
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 500);
        });

        // --- 4. 核心功能函式 ---

        function summonPlanet() {
            // 隨機選取一個星球
            const randomPlanet = planetsData[Math.floor(Math.random() * planetsData.length)];
            
            // 更新 UI
            updatePlanetUI(randomPlanet);
        }

        function updatePlanetUI(planet) {
            planetImage.src = planet.image;
            planetName.textContent = planet.name;
            planetType.textContent = planet.type;
            planetDesc.textContent = planet.desc;
            planetTemp.textContent = planet.temp;
            planetDist.textContent = planet.dist;
            wishSuggestion.textContent = planet.suggestion;
        }

    </script>
</body>
</html>

<!-- <?php
// index.php 範例

require_once 'db.php'; // 載入連線檔，現在你有了 $pdo 變數

// 查詢資料庫
$stmt = $pdo->query('SELECT * FROM users');
$users = $stmt->fetchAll();

// 顯示測試結果
print_r($users);
?> -->