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
     * 建立新的許願紀錄並且計算成功/失敗
     */
    public function create($userId, $planetId, $wishContent, $arrivalAt, $rpgType)
    {
        // 判斷許願內容是否符合行星類型
        $isSuccess = $this->checkWishSuccess($wishContent, $rpgType);

        // 決定最終狀態（但使用者要等到 arrival_at 才能看到）
        $finalStatus = $isSuccess ? 'arrived' : 'failed';

         $sql = "INSERT INTO wishes (
                    user_id, 
                    planet_id, 
                    wish_content, 
                    arrival_at,
                    status,
                    is_success,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([
            $userId,
            $planetId,
            $wishContent,
            $arrivalAt,
            $finalStatus,      // 提前存入最終狀態
            $isSuccess         // 提前存入是否成功
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
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
     * 取得會員的所有許願紀錄
     */
    // public function getUserWishes($userId)
    // {
    //     $sql = "SELECT w.*, p.name as planet_name, p.rpg_type, p.distance_ly
    //             FROM wishes w
    //             JOIN planets p ON w.planet_id = p.id
    //             WHERE w.user_id = ?
    //             ORDER BY w.created_at DESC";
        
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute([$userId]);
    //     return $stmt->fetchAll();
    // }
    
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