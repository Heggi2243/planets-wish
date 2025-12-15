<?php
class Planet
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::connect();
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
     * 新增行星(從API)
     */
    public function create($data)
    {
        $sql = "INSERT INTO planets (
                    name, api_data_id, rpg_type, 
                    power_stat, dex_stat, luck_stat, intel_stat, 
                    distance_ly, image_url, description
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['name'],
            $data['api_data_id'] ?? null,
            $data['rpg_type'] ?? '幸運',
            $data['power_stat'] ?? 0,
            $data['dex_stat'] ?? 0,
            $data['luck_stat'] ?? 0,
            $data['intel_stat'] ?? 0,
            $data['distance_ly'] ?? 0,
            $data['image_url'] ?? null,
            $data['description'] ?? null
        ]);
    }
    
    /**
     * 檢查行星是否已存在（根據 API ID）
     */
    public function existsByApiId($apiDataId)
    {
        $stmt = $this->db->prepare("SELECT id FROM planets WHERE api_data_id = ?");
        $stmt->execute([$apiDataId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * 計算抵達時間（根據距離）
     * 距離光年 * 10 分鐘（可調整係數）
     */
    public function calculateArrivalTime($distanceLy)
    {
        // 最少 10 分鐘，最多 120 分鐘
        $minutes = min(max(round($distanceLy * 10), 10), 120);
        
        $arrivalTime = new DateTime();
        $arrivalTime->modify("+{$minutes} minutes");
        
        return $arrivalTime->format('Y-m-d H:i:s');
    }
}