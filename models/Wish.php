<?php
class Wish
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::connect();
    }
    
    /**
     * 取得會員的所有許願紀錄
     */
    public function getUserWishes($userId)
    {
        $sql = "SELECT w.*, p.name as planet_name, p.rpg_type, p.distance_ly
                FROM wishes w
                JOIN planets p ON w.planet_id = p.id
                WHERE w.user_id = ?
                ORDER BY w.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * 根據 ID 取得許願紀錄
     */
    public function getById($id)
    {
        $sql = "SELECT w.*, p.name as planet_name, p.rpg_type, p.distance_ly
                FROM wishes w
                JOIN planets p ON w.planet_id = p.id
                WHERE w.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * 建立新的許願
     */
    public function create($userId, $planetId, $wishContent, $arrivalAt)
    {
        $sql = "INSERT INTO wishes (
                    user_id, planet_id, wish_content, 
                    status, created_at, arrival_at, is_success
                ) VALUES (?, ?, ?, 'traveling', NOW(), ?, 0)";
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute([$userId, $planetId, $wishContent, $arrivalAt])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * 檢查並更新已抵達的許願
     */
    public function checkAndUpdateArrived($wishId)
    {
        $wish = $this->getById($wishId);
        
        if (!$wish) {
            return false;
        }
        
        // 檢查是否已抵達
        $now = new DateTime();
        $arrivalTime = new DateTime($wish['arrival_at']);
        
        if ($now >= $arrivalTime && $wish['status'] === 'traveling') {
            // 判斷是否成功（這裡需要你的邏輯）
            $isSuccess = $this->checkWishSuccess($wish);
            
            $status = $isSuccess ? 'arrived' : 'failed';
            
            $sql = "UPDATE wishes SET status = ?, is_success = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status, $isSuccess, $wishId]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 判斷許願是否成功（根據內容與行星屬性相符度）
     * 這裡需要你實作判斷邏輯
     */
    private function checkWishSuccess($wish)
    {
        // TODO: 實作你的判斷邏輯
        // 例如：用關鍵字判斷內容是否與行星屬性相符
        
        $content = $wish['wish_content'];
        $type = $wish['rpg_type'];
        
        // 簡單的關鍵字判斷範例
        $keywords = [
            '力量' => ['力量', '強壯', '健康', '體力', '肌肉'],
            '敏捷' => ['敏捷', '靈活', '速度', '快速', '反應'],
            '幸運' => ['幸運', '運氣', '中獎', '好運', '財運'],
            '智慧' => ['智慧', '學習', '考試', '知識', '聰明'],
            '速度' => ['速度', '快', '效率', '時間', '迅速']
        ];
        
        if (isset($keywords[$type])) {
            foreach ($keywords[$type] as $keyword) {
                if (mb_strpos($content, $keyword) !== false) {
                    return 1; // 成功
                }
            }
        }
        
        // 也可以給一個隨機機率
        return (rand(1, 100) <= 30) ? 1 : 0; // 30% 機率成功
    }
    
    /**
     * 取得旅行中的許願
     */
    public function getTravelingWishes($userId)
    {
        $sql = "SELECT w.*, p.name as planet_name, p.rpg_type
                FROM wishes w
                JOIN planets p ON w.planet_id = p.id
                WHERE w.user_id = ? AND w.status = 'traveling'
                ORDER BY w.arrival_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}