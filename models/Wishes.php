<?php
namespace models;

require_once __DIR__ . '/../config/Database.php';

class Wishes
{
    private $db;
    
    public function __construct()
    {
        $this->db = \Database::connect();
    }
    
    /**
     * 建立 traveling 狀態的許願紀錄（還沒填寫願望內容，也還沒計算抵達時間）
     * 
     * @param int $userId 用戶 ID
     * @param int $planetId 行星 ID
     * @return int|false 新增的wish_id
     */
    public function createTravelingWish($userId, $planetId)
    {
        $sql = "INSERT INTO wishes (
                    user_id, 
                    planet_id, 
                    wish_content,
                    arrival_at,
                    is_success,
                    created_at
                ) VALUES (?, ?, 'NULL', NULL, 0, NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([
            $userId,
            $planetId
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * 更新許願內容、抵達時間並計算成功/失敗
     * 
     * @param int $wishId 許願ID
     * @param string $wishContent 願望內容
     * @param string $arrivalAt 預計抵達時間
     * @param string $rpgType 行星屬性類型
     * @return bool 是否成功
     */
    public function updateWishContent($wishId, $wishContent, $arrivalAt, $rpgType)
    {
        // 計算成功/失敗
        $isSuccess = $this->checkWishSuccess($wishContent, $rpgType);
        
        $sql = "UPDATE wishes 
                SET wish_content = ?, 
                    arrival_at = ?,
                    status = ?, 
                    is_success = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $wishContent,
            $arrivalAt,
            'traveling',
            $isSuccess,
            $wishId
        ]);
    }

    /**
     * 判斷許願是否成功
     * 
     * @param string $wishContent 願望內容
     * @param string $rpgType 行星屬性類型
     * @return int 1=成功, 0=失敗
     */
    private function checkWishSuccess($wishContent, $rpgType)
    {
        
        // 關鍵字判斷
        $keywords = [
            '力量' => ['力量', '健康', '理想',  '進步', '努力', '改變'],
            '敏捷' => ['敏捷', '感情', '生活', '最近', '朋友', '家人', '愛'],
            '幸運' => ['幸運', '運', '財', '錢', '富', '幸福'],
            '智慧' => ['智慧', '學習', '考試', '工作', '未來', '事業', '順'],
        ];
        
        // 檢查是否有對應關鍵字
        if (isset($keywords[$rpgType])) {
            foreach ($keywords[$rpgType] as $keyword) {
                if (mb_strpos($wishContent, $keyword) !== false) {
                    return 1; // 找到關鍵字=成功
                }
            }
        }
        
        // 沒有關鍵字，給隨機機率30%
        return (rand(1, 100) <= 30) ? 1 : 0;
        
    }

    /**
     * 取得今天最新的願望(用於倒數計時)
     * 
     * @param int $userId 用戶id
     * @return array|false 願望資料
     */
    public function getLatestTodayWish($userId)
    {
        $sql = "SELECT w.*, p.name as planet_name, p.rpg_type
                FROM wishes w
                LEFT JOIN planets p ON w.planet_id = p.id
                WHERE w.user_id = ?
                AND DATE(w.created_at) = CURDATE()
                ORDER BY w.created_at DESC
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetch();
    }

    /**
     * 更新願望狀態
     * 
     * @param int $wishId 願望 ID
     * @param string $status新狀態 (summoned, traveling, arrived, checked)
     * @return bool 是否成功
     */
    public function updateStatus($wishId, $status)
    {
        $sql = "UPDATE wishes SET status = ? WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $wishId]);
    }


    /**
     * 取得會員的所有許願紀錄 (wish/record.php)
     */
    public function getUserWishes($userId)
    {
        $sql = "SELECT w.*, p.name as planet_name, p.rpg_type, p.distance_ly, p.keywords
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
    // public function getById($id)
    // {
    //     $sql = "SELECT w.*, p.name as planet_name, p.rpg_type, p.distance_ly
    //             FROM wishes w
    //             JOIN planets p ON w.planet_id = p.id
    //             WHERE w.id = ?";
        
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute([$id]);
    //     return $stmt->fetch();
    // }
    
    
    /**
     * 取得旅行中的許願
     */
    // public function getTravelingWishes($userId)
    // {
    //     $sql = "SELECT w.*, p.name as planet_name, p.rpg_type
    //             FROM wishes w
    //             JOIN planets p ON w.planet_id = p.id
    //             WHERE w.user_id = ? AND w.status = 'traveling'
    //             ORDER BY w.arrival_at ASC";
        
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute([$userId]);
    //     return $stmt->fetchAll();
    // }
}