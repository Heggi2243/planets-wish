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