<?php
class User
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::connect();
    }
    
    /**
     * 根據 ID 取得會員資料
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * 根據帳號取得會員資料
     */
    public function getByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * 註冊新會員
     */
    public function register($username, $password, $email)
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password_hash, email, coins, created_at) 
                VALUES (?, ?, ?, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([$username, $passwordHash, $email]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * 驗證登入
     */
    public function login($username, $password)
    {
        $user = $this->getByUsername($username);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * 檢查今天是否已經免費召喚過
     */
    public function hasUsedDailySummon($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT last_daily_summon_date FROM users WHERE id = ?"
        );
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !$user['last_daily_summon_date']) {
            return false;
        }
        
        return $user['last_daily_summon_date'] === date('Y-m-d');
    }
    
    /**
     * 更新最後免費召喚日期
     */
    public function updateDailySummonDate($userId)
    {
        $sql = "UPDATE users SET last_daily_summon_date = CURDATE() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    /**
     * 取得會員金幣
     */
    public function getCoins($userId)
    {
        $stmt = $this->db->prepare("SELECT coins FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result ? $result['coins'] : 0;
    }
    
    /**
     * 增加金幣
     */
    public function addCoins($userId, $amount)
    {
        $sql = "UPDATE users SET coins = coins + ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$amount, $userId]);
    }
    
    /**
     * 扣除金幣
     */
    public function deductCoins($userId, $amount)
    {
        // 先檢查金幣是否足夠
        $currentCoins = $this->getCoins($userId);
        
        if ($currentCoins < $amount) {
            return false;
        }
        
        $sql = "UPDATE users SET coins = coins - ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$amount, $userId]);
    }
}