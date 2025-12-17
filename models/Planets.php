<?php
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
        $this->db = Database::connect();
    }

     /**
     * 根據API資料計算RPG屬性
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

        // 力=質量開根號*40
        $powerRaw = min(100, sqrt($mass) * 40); 

        // 敏=對數級距
        // log10(1)=0 => 120分; log10(10)=1 => 90分; log10(100)=2 => 60分
        if ($period > 0) {
            $logPeriod = log10($period);
            $dexRaw = 120 - (30 * $logPeriod);
        } else {
            $dexRaw = 0;
        }
        $dexRaw = min(100, max(0, $dexRaw));

        // 幸=溫度/25
        $luckRaw = min(100, $temperature / 25);

        // 智=半徑*50
        $intelRaw = min(100, $radius * 50);

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

        return [
            'rpg_type' => $rpgType,
            'power_stat' => round($powerStat),
            'dex_stat' => round($dexStat),
            'luck_stat' => round($luckStat),
            'intel_stat' => round($intelStat),
            'distance_ly' => $distanceLightYear
        ];
    }

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
                    distance_ly, description
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);

        $description = sprintf(
            "質量: %s | 半徑: %s | 公轉週期: %s天 | 溫度: %sK",
            $data['mass'] ?? 'N/A',
            $data['radius'] ?? 'N/A',
            $data['period'] ?? 'N/A',
            $data['temperature'] ?? 'N/A'
        );

        
        return $stmt->execute([
            $data['name'],
            $stats['rpg_type'] ?? '幸運',
            $stats['power_stat'] ?? 0,
            $stats['dex_stat'] ?? 0,
            $stats['luck_stat'] ?? 0,
            $stats['intel_stat'] ?? 0,
            $stats['distance_ly'] ?? 0,
            $$description ?? null
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