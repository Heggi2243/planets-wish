<?php
namespace models;

require_once __DIR__ . '/../config/Database.php';

/**
 * Planets Model
 * 處理行星資料的table操作
 */

/**
 * Planets API JSON格式:
 *[
 * {
 *  "name": "Neptune", //海王星
 *  "mass": 0.0537, //質量
 *  "radius": 0.346, //半徑
 * "period": 59800,     //週期 or 公轉週期
 * "semi_major_axis": 30.07,  //半長軸(軌道橢圓長徑的一半，常用來代表行星與恆星的平均距離)
 * "temperature": 72, //溫度
 * "distance_light_year": 0.000478, //距離(光年)
 * "host_star_mass": 1, //母恆星質量
 * "host_star_temperature": 6000 //母恆星溫度
 * }
 *]
 */
class Planets
{
    private $db;
    
    public function __construct()
    {
        $this->db = \Database::connect();
    }

    public function getCategory($planetName) 
    {

         $firstChar = strtoupper(substr($planetName, 0, 1));

        
        if (preg_match('/[0-1]/', $firstChar)) return 'oO1';
        if (preg_match('/[2-4]/', $firstChar)) return 'o24';
        if (preg_match('/[5-7]/', $firstChar)) return 'o57';
        if (preg_match('/[8-9]/', $firstChar)) return 'o89';
        if (preg_match('/[A-D]/', $firstChar)) return 'oAD';
        if (preg_match('/[E]/', $firstChar)) return 'oE';
        if (preg_match('/[F-I]/', $firstChar)) return 'oFI';
        if (preg_match('/[J-M]/', $firstChar)) return 'oJM';
        if (preg_match('/[N-Q]/', $firstChar)) return 'oNQ';
        if (preg_match('/[R-U]/', $firstChar)) return 'oRU';
        if (preg_match('/[V-W]/', $firstChar)) return 'oVW';
        if (preg_match('/[X]/', $firstChar)) return 'oX';
        if (preg_match('/[Y]/', $firstChar)) return 'oY';
        if (preg_match('/[Z]/', $firstChar)) return 'oZ';
        
        return 'wormhole';
    }

    /**
     * 回傳圖片名稱
     * 這些字眼不會同時出現：
     * hot跟cold
     * fast跟slow
     * far跟close
     * heavy跟light
     */

    public function getCategoryByKeywords($keywords) 
    {
        // 如果傳入的是str，先轉陣列
            if (is_string($keywords)) {
                $keywords = array_map('trim', explode(',', $keywords));
            }

            // 1. 優先判斷多重組合
            if (in_array('hot', $keywords) && in_array('light', $keywords)) {
                return 'hot_light'; // 既熱又近的極端星球
            }
            
            if (in_array('cold', $keywords) && in_array('heavy', $keywords)) {
                return 'cold_heavy'; // 冰冷且巨大的氣體巨星
            }

            if (in_array('cold', $keywords) && in_array('fast', $keywords)) {
                return 'cold_fast'; 
            }

            if (in_array('cold', $keywords) && in_array('close', $keywords)) {
                return 'cold_close'; 
            }

            if (in_array('far', $keywords) && in_array('light', $keywords)) {
                return 'far_light'; 
            }

            if (in_array('slow', $keywords) && in_array('heavy', $keywords)) {
                return 'slow_heavy'; 
            }

            if (in_array('fast', $keywords) && in_array('close', $keywords)) {
                return 'fast_close'; 
            }

            if (in_array('fast', $keywords) && in_array('far', $keywords)) {
                return 'fast_far'; 
            }

            if (in_array('hot', $keywords) && in_array('close', $keywords)) {
                return 'hot_close'; 
            }

            if (in_array('slow', $keywords) && in_array('cold', $keywords)) {
                return 'slow_cold'; 
            }



            // 2. 接著判斷「單一關鍵字」
            if (in_array('hot', $keywords)) return 'hot';
            if (in_array('cold', $keywords)) return 'cold';
            if (in_array('fast', $keywords)) return 'fast';
            if (in_array('slow', $keywords)) return 'slow';
            if (in_array('far', $keywords)) return 'far';
            if (in_array('close', $keywords)) return 'close';
            if (in_array('heavy', $keywords)) return 'heavy';
            if (in_array('light', $keywords)) return 'light';

            // 3. 都沒匹配到時的預設值
            return 'wormhole';
    }

     /**
     * 根據API資料計算RPG屬性、拼接description、suggestion欄位內容
     */
    private function calculateRPGStats($planetData)
    {
        
        // 取得行星的各項數值
        $mass = $planetData['mass'] ?? 0.001;
        $radius = $planetData['radius'] ?? 0;
        $period = $planetData['period'] ?? 99999;
        $temperature = $planetData['temperature'] ?? 0;
        $distanceLightYear = $planetData['distance_light_year'] ?? 0;

        // 計算各項屬性(0-100的原始分數)

        // 力=質量開根號*60
        $powerRaw = min(100, sqrt($mass) * 60); 

        // 敏=對數級距
        // log10(1)=0 => 120分; log10(10)=1 => 90分; log10(100)=2 => 60分
        if ($period > 0) {
            $logPeriod = log10($period);
            $dexRaw = 100 - (30 * $logPeriod);
        } else {
            $dexRaw = 0;
        }
        $dexRaw = min(100, max(0, $dexRaw));

        // 幸=溫度/10
        $luckRaw = min(100, $temperature / 10);

        // 智=半徑*100
        $intelRaw = min(100, $radius * 100);

        // 將 0-100 的分數轉換為 0-3 的點數
        // 使用線性映射: (rawScore / 100) * 3
        $powerStat = ($powerRaw / 100) * 3;
        $dexStat = ($dexRaw / 100) * 3;
        $luckStat = ($luckRaw / 100) * 3;
        $intelStat = ($intelRaw / 100) * 3;

        $totalStats = $powerStat + $dexStat + $luckStat + $intelStat;

        // 如果總和超過6，按比例縮小
        if ($totalStats > 6) {
            $scale = 6 / $totalStats;
            $powerStat *= $scale;
            $dexStat *= $scale;
            $luckStat *= $scale;
            $intelStat *= $scale;

        // 如果總和小於1，給1的幸運值
        } elseif ($totalStats < 1 ) {
            $luckStat = 1;
        }

        // 四捨五入到小數點後一位
        $powerStat = round($powerStat, 1);
        $dexStat = round($dexStat, 1);
        $luckStat = round($luckStat, 1);
        $intelStat = round($intelStat, 1);

        // 找出最高的屬性來決定 rpg_type
        $stats = [
            '力量' => $powerStat,
            '敏捷' => $dexStat,
            '幸運' => $luckStat,
            '智慧' => $intelStat
        ];
        $rpgType = array_keys($stats, max($stats))[0];

        // ==================== suggestion邏輯 ====================

        // 透過rpg_type隨機選一個星象建議
        $suggestionPool = [
            // 對應 Mass (質量) / 力量
            '力量' => [
                "適合許下「努力」或「突破性改變」的願望。", 
                "這個行星有著巨大的引力，適合許下關於未來或事業重任的願望。",
                "今年對「健康」或「理想」有甚麼規劃嗎？訴說你的星願吧。",
                "這顆行星擁有極其穩定的內核，適合祈求這份「進步」能持之以恆。",
                "在大質量的引力加持下，許下一個關於你人生軌跡的星願吧。",
                "強大的重力場象徵著生命力，推薦關於「提升」或「突破自我」的願望。"
            ],
            // 對應 Period (週期) / 敏捷
            '敏捷' => [
                "它在軌道上疾馳，日子轉瞬即逝，適合許下貼近生活的願望。",
                "行星轉瞬即逝的軌道，及時訴說你的感情。",
                "「最近」有甚麼迫不及待想實現的星願嗎？",
                "在快節奏的公轉中，適合許下希望「最近」就能看到回報的小確幸。",
                "這裡的時間流速極快，適合許下關於「關係修復」或「團聚」的及時心願。",
                "能量就像光陰奔流，適合祈求你所珍視的「愛」與「生活」。"
            ],
            // 對應 Temperature (溫度) / 幸運
            '幸運' => [
                "熾熱的能量將加速你的夢想燃燒，祈求「好運」吧。",
                "這裡有著巨大的引力。適合許下關於增加財富或運氣的願望。",
                "星雲散發著溫暖的微光，適合許下與「幸福感」或「心理富足」相關的願望。",
                "高溫象徵著財氣匯聚，適合許下關於「財富爆發」或「中獎運勢」的星願。",
                "熾熱的星體會照亮陰影，適合祈求「好運」並帶走不順遂的負能量。",
                "這是一顆閃耀著金色光芒的星球，非常適合來點「意外驚喜」。"
            ],
            // 對應 Radius (半徑) / 智慧
            '智慧' => [
                "適合許下「遠大理想」或「追尋靈魂深處」的願望。",
                "讓願望隨星光穿越漫長星際，許下你期望的「未來」。",
                "適合許下關於釋放壓力、工作順利的願望。",
                "遼闊的行星半徑象徵寬廣的見識，適合許下關於「考試順利」或「學習進修」的願望。",
                "在深邃的星系中，適合祈求一份讓「事業發展」更為圓滿、流暢的智慧。",
                "寬廣的視野有利於長期規劃，適合許下關於「職業生涯」或「個人成長」的長遠星願。"
            ],
        ];

        if (isset($suggestionPool[$rpgType])) {
    
            //從屬性對應的陣列隨機取出一個Key
            $randomKey = array_rand($suggestionPool[$rpgType]);
            
            // 根據索引取得該句話，塞進$suggestion變數
            $suggestion = $suggestionPool[$rpgType][$randomKey];

        } else {

            $suggestion = "充滿神祕氣息的星球，適合訴說你內心深處最純粹的渴望。";
        }

        // ==================== description邏輯 ====================

        $keywordAry = [];

        //溫度判定
        if ($planetData['temperature'] > 800) {
            $keywordAry[] = 'hot';
        } elseif ($planetData['temperature'] < 200) {
            $keywordAry[] = 'cold';
        }

        if ($planetData['period'] > 10) {
            $keywordAry[] = 'fast';
        } elseif ($planetData['period'] < 0.5) {
            $keywordAry[] = 'slow';
        }

        //距離判定
        if ($planetData['distance_light_year'] > 1000) {
            $keywordAry[] = 'far';
        } elseif ($planetData['distance_light_year'] < 50 || $planetData['semi_major_axis'] < 0.1) {
            $keywordAry[] = 'close';
        }

        //質量判定
        if ($planetData['mass'] > 10 ) {
            $keywordAry[] = 'heavy';
        } elseif ($planetData['mass'] < 0.3 ){
            $keywordAry[] = 'light';
        }

        // 定義句子池
        $descriptionPool = [
            'hot'   => [
                    "地表翻騰著不穩定的熱浪", 
                    "這是一顆被永恆烈焰包圍的星球",
                    "大氣中充滿了沸騰的能量感", 
                    "地表溫度極高，岩漿流淌在乾涸的海床", 
                    "這是一顆永不熄滅的恆星之子"
                    ],
            'cold'  => [
                    "寂靜的冰霜覆蓋了所有文明遺跡", 
                    "這顆星球在絕對零度的邊緣徘徊",
                    "這裡被冰封在無盡的寒冬中",
                    "地表覆蓋著氮冰，靜謐而冷冽",
                    "微弱的星光照耀著這片極寒荒野", 
                    "寒氣穿透了太空艙的隔熱層"
                    ],
            'fast'  => [
                    "這顆行星像是在星海中失控的賽車，瘋狂地繞著母恆星旋轉",
                    "這裡的一年僅僅是地球上的數個小時，時間的流逝快得讓人眩暈",
                    "光陰在天際線忽明忽暗，白晝與黑夜的更迭快如眨眼"
                    ],
            'slow' => [
                    "光陰在此凝固成了琥珀，這顆行星以一種近乎永恆的緩慢姿態漫步",
                    "這裡的一季比地球上的文明興衰還要漫長，季節的輪轉是神話級的跨度",
                    "在深空中孤獨地完成漫長的長征"
                    ],
            'far'  => [
                    "它的存在僅僅是夜空中的一抹微光", 
                    "它是宇宙邊緣的孤獨守望者", 
                    "距離地球如此遙遠，時間彷彿在此失去了意義"
                    ],
            'close' => [
                    "它是地球的鄰居", 
                    "這顆行星的影子在望遠鏡中清晰可見", 
                    "僅需幾年的航行，我們就能抵達它的懷抱",
                    "緊貼著恆星運行，承受最強光芒"
                    ],
            'heavy' => [
                    "這顆星球擁有令人窒息的引力，連光線經過時都會微微彎曲",
                    "大氣被自身的重量壓成了如液體般的濃稠感，每一步都踏在厚實的時空褶皺上",
                    "這是一個無比沉穩的世界，巨大的質量讓它成為了星系中不可撼動的錨點"
            ],
            'light' => [
                    "這裡的引力微弱得如同夢境，一陣強風彷彿就能將地表的沙塵吹向宇宙",
                    "這顆星球輕盈得像是一顆漂浮在星海中的肥皂泡，充滿了靈動與自由",
                    "行走在荒原之上，身體輕盈得彷彿隨時能躍過那些纖細的山脈"
                    ]
        ];

        $keywords = implode(',', $keywordAry);

        $selectedSentences = [];

        // foreach關鍵字標籤，從池子中抽選句子
        foreach ($keywordAry as $tag) {
            if (isset($descriptionPool[$tag])) {
                //隨機選取該標籤下的一個句子索引
                $randomIndex = array_rand($descriptionPool[$tag]);
                //存入暫存陣列
                $selectedSentences[] = $descriptionPool[$tag][$randomIndex];
            }
        }

        $description = "";

        if (!empty($selectedSentences)) {

            // 使用「，」連接所有句子
            $description = implode('，', $selectedSentences);
            
            // 確保結尾是句號(先去掉末尾可能存在的標點，再統一補上句號)
            // 使用mb_substr處理多位元字元比較保險
            $description .= "。";
        } else {
            // 防呆
            $description = "這是一顆未知的行星，正靜靜地等待著旅人的造訪。";
        }



        return [
            'rpg_type' => $rpgType,
            'power_stat' => round($powerStat),
            'dex_stat' => round($dexStat),
            'luck_stat' => round($luckStat),
            'intel_stat' => round($intelStat),
            'distance_ly' => $distanceLightYear,
            'suggestion' => $suggestion,
            'description' => $description,
            'keywords' => $keywords
        ];
    }

    //     [
    // {
    //     "name": "Neptune",
    //     "mass": 0.0537,
    //     "radius": 0.346,
    //     "period": 59800,
    //     "semi_major_axis": 30.07,
    //     "temperature": 72,
    //     "distance_light_year": 0.000478,
    //     "host_star_mass": 1,
    //     "host_star_temperature": 6000
    // }
    // ]

    /**
     * 檢查行星是否已存在
     */
    public function exists($name)
    {
        $stmt = $this->db->prepare("SELECT id FROM planets WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch() !== false;
    }

    /**
     * 新增行星(從API)
     */
    public function create($data)
    {

        // 跳過已存在行星
        if ($this->exists($data['name'])) {
            return false;
        }

         // 計算RPG屬性
        $stats = $this->calculateRPGStats($data);

        $sql = "INSERT INTO planets (
                    name, rpg_type,
                    power_stat, dex_stat, luck_stat, intel_stat,
                    distance_ly, description, suggestion, keywords,
                    mass, radius, period, temperature, semi_major_axis
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);

        
        return $stmt->execute([
            $data['name'],
            $stats['rpg_type'] ?? '幸運',
            $stats['power_stat'] ?? 0,
            $stats['dex_stat'] ?? 0,
            $stats['luck_stat'] ?? 0,
            $stats['intel_stat'] ?? 0,
            $stats['distance_ly'] ?? 0,
            $stats['description'],
            $stats['suggestion'],
            $stats['keywords'],
            $data['mass'],
            $data['radius'],
            $data['period'],
            $data['temperature'],
            $data['semi_major_axis']
        ]);
    }

    /**
     * 批次新增行星
     */
    public function createBatch($planetsData)
    {
        $successCount = 0;
        $skipCount = 0;

        foreach ($planetsData as $data) {

            if (!is_array($data) || empty($data['name'])) {
                continue;
            }


            if ($this->create($data)) {
                $successCount++;
                echo "成功新增: {$data['name']}\n";
            } else {
                $skipCount++;
                echo "跳過: {$data['name']}\n";
            }
        }

        return [
            'success' => $successCount,
            'skipped' => $skipCount,
            'total' => count($planetsData)
        ];
    }


    
    /**
     * 取得所有行星
     */
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM planets ORDER BY name");
        return $stmt->fetchAll();
    }
    
    /**
     * 根據id取得行星
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM planets WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * 根據屬性分類取得行星
     */
    public function getByType($rpgType)
    {
        $stmt = $this->db->prepare("SELECT * FROM planets WHERE rpg_type = ?");
        $stmt->execute([$rpgType]);
        return $stmt->fetchAll();
    }
    
    /**
     * 隨機取得一顆行星
     */
    public function getRandom()
    {
        $stmt = $this->db->query("SELECT * FROM planets ORDER BY RAND() LIMIT 1");
        return $stmt->fetch();
    }
    
    /**
     * 隨機取得指定屬性的行星
     */
    public function getRandomByType($rpgType)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM planets WHERE rpg_type = ? ORDER BY RAND() LIMIT 1"
        );
        $stmt->execute([$rpgType]);
        return $stmt->fetch();
    }
    

    
    /**
     * 計算抵達時間
     * 距離光年*10分鐘
     */
    public function calculateArrivalTime($distanceLy)
    {
        // 最少10分鐘，最多60分鐘
        $minutes = min(max(round($distanceLy * 10), 10), 60);
        
        $arrivalTime = new DateTime();
        $arrivalTime->modify("+{$minutes} minutes");
        
        return $arrivalTime->format('Y-m-d H:i:s');
    }
}