<?php
class Inventory
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::connect();
    }
    
    /**
     * 取得會員的所有道具
     */
    public function getUserItems($userId)
    {
        $sql = "SELECT i.*, it.name, it.code, it.description 
                FROM inventory i
                JOIN items it ON i.item_id = it.id
                WHERE i.user_id = ? AND i.quantity > 0
                ORDER BY it.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * 取得特定道具數量
     */
    public function getItemQuantity($userId, $itemId)
    {
        $stmt = $this->db->prepare(
            "SELECT quantity FROM inventory WHERE user_id = ? AND item_id = ?"
        );
        $stmt->execute([$userId, $itemId]);
        $result = $stmt->fetch();
        
        return $result ? $result['quantity'] : 0;
    }
    
    /**
     * 增加道具
     */
    public function addItem($userId, $itemId, $quantity = 1)
    {
        // 先檢查是否已有此道具
        $existing = $this->db->prepare(
            "SELECT id, quantity FROM inventory WHERE user_id = ? AND item_id = ?"
        );
        $existing->execute([$userId, $itemId]);
        $row = $existing->fetch();
        
        if ($row) {
            // 更新數量
            $sql = "UPDATE inventory SET quantity = quantity + ? 
                    WHERE user_id = ? AND item_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$quantity, $userId, $itemId]);
        } else {
            // 新增道具
            $sql = "INSERT INTO inventory (user_id, item_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $itemId, $quantity]);
        }
    }
    
    /**
     * 使用道具（扣除數量）
     */
    public function useItem($userId, $itemId, $quantity = 1)
    {
        // 先檢查數量是否足夠
        $currentQuantity = $this->getItemQuantity($userId, $itemId);
        
        if ($currentQuantity < $quantity) {
            return false;
        }
        
        $sql = "UPDATE inventory SET quantity = quantity - ? 
                WHERE user_id = ? AND item_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $userId, $itemId]);
    }
}