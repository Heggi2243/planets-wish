<?php
/**
 * Database 類別
 * 處理資料庫連線
 */
class Database
{
    private static $pdo = null;
    
    /**
     * 建立資料庫連線（靜態方法）
     */
    public static function connect()
    {
        if (self::$pdo === null) {
            try {
                // 讀取環境變數
                $host = $_ENV['DB_HOST'] ?? null;
                $dbname = $_ENV['DB_NAME'] ?? null;
                $username = $_ENV['DB_USER'] ?? null;
                $password = $_ENV['DB_PASS'] ?? '';
                
                // ✅ 檢查必要的設定是否存在
                if (empty($host) || empty($dbname) || empty($username)) {
                    // 記錄詳細的錯誤資訊
                    error_log("=== 資料庫連線失敗 ===");
                    error_log("DB_HOST: " . ($host ?? '❌ 未設定'));
                    error_log("DB_NAME: " . ($dbname ?? '❌ 未設定'));
                    error_log("DB_USER: " . ($username ?? '❌ 未設定'));
                    error_log("DB_PASS: " . (empty($password) ? '空字串' : '已設定'));
                    error_log("當前目錄: " . getcwd());
                    error_log(".env 檔案位置: " . __DIR__ . '/../.env');
                    error_log(".env 檔案存在: " . (file_exists(__DIR__ . '/../.env') ? '是' : '否'));
                    
                    throw new Exception('資料庫設定不完整，請檢查 .env 檔案');
                }
                
                $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
                
                // 記錄連線嘗試（不包含密碼）
                error_log("嘗試連線到資料庫: {$host} / {$dbname}");
                
                self::$pdo = new PDO(
                    $dsn,
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                
                error_log("✅ 資料庫連線成功");
                
            } catch (PDOException $e) {
                // 詳細的 PDO 錯誤訊息
                error_log("❌ PDO 連線失敗: " . $e->getMessage());
                error_log("錯誤碼: " . $e->getCode());
                error_log("DSN: mysql:host={$host};dbname={$dbname}");
                
                
                $errorMessage = '資料庫連線失敗';
                
                if ($e->getCode() == 1045) {
                    $errorMessage .= '：帳號或密碼錯誤';
                } elseif ($e->getCode() == 2002) {
                    $errorMessage .= '：無法連接到資料庫伺服器（檢查 MySQL 是否啟動）';
                } elseif ($e->getCode() == 1049) {
                    $errorMessage .= '：資料庫不存在';
                }
                
                throw new Exception($errorMessage . ' (' . $e->getMessage() . ')');
            }
        }
        
        return self::$pdo;
    }
    
    /**
     * 實例方法（向後相容）
     */
    public function getConnection()
    {
        return self::connect();
    }
}