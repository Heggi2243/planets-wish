<?php
/* Users model */
class Users
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
     * 密碼加密、產生email驗證碼
     */
    public function register($username, $password, $email)
    {   
        //產生驗證Token(64字元隨機字串)
        $verificationToken = bin2hex(random_bytes(32));
        //24小時過期
        $tokenExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        //密碼hash
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password_hash, email, 
                email_verified, verification_token, token_expires_at, created_at) 
                VALUES (?, ?, ?, 0, ?, ?, NOW())";

        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([$username, $passwordHash, $email, $verificationToken, $tokenExpiresAt]);
            return [
                'user_id' => $this->db->lastInsertId(),
                'verification_token' => $verificationToken
            ];
        } catch (PDOException $e) {
            return false;
        }
    }

     /**
     * 驗證email token
     */
    public function verifyEmail($token)
    {
        /// 尋找目前持有email驗證碼，但是還沒驗證的人
        $sql = "SELECT id, username, email FROM users
                WHERE verification_token = ?
                AND token_expires_at > NOW() 
                AND email_verified = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            return [
                'success' => false,
                'message' => '驗證連結已過期'
            ];
        }

        // 更新為已驗證
        $updateSql = "UPDATE users 
                      SET email_verified = 1, 
                          verification_token = NULL, 
                          token_expires_at = NULL 
                      WHERE id = ?";
        
        $updateStmt = $this->db->prepare($updateSql);

        if ($updateStmt->execute([$user['id']])) {
            return [
                'success' => true,
                'message' => 'Email 驗證成功！',
                'username' => $user['username']
            ];
        }

        return [
            'success' => false,
            'message' => '驗證失敗'
        ];
    }

    /**
     * 重新發送驗證信
     */
    public function resendVerification($email)
    {
        // 檢查使用者是否存在&&未驗證
        $sql = "SELECT id, username, email FROM users
                WHERE email = ? AND email_verified = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }
        
        // 產生新的驗證token
        $verificationToken = bin2hex(random_bytes(32));
        $tokenExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $updateSql = "UPDATE users
                    SET verification_token = ?, 
                        token_expires_at = ?
                    WHERE id = ?";
        
        $updateStmt = $this->db->prepare($updateSql);

        if ($updateStmt->execute([$verificationToken, $tokenExpiresAt, $user['id']])) {
            return [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'verification_token' => $verificationToken
            ];
        }

        return false;
    }

    /**
     * 驗證登入
     */
    public function login($username, $password)
    {
        $user = $this->getByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'message' => '帳號或密碼錯誤'
            ];
        }

        // 檢查是否已驗證email
        if ($user['email_verified'] == 0) {
            return [
                'success' => false,
                'message' => '請先驗證Email',
                'needs_verification' => true,
                'email' => $user['email']  // 回傳email供重新發送使用
            ];
        }
        
        return [
            'success' => true,
            'user_id' => $user['id'],
            'username' => $user['username']
        ];
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