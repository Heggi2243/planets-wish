<?php
require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>資料庫連線測試</title>
    <style>
        body { font-family: 'Courier New', monospace; padding: 20px; background: #1a1a1a; color: #0f0; }
        .success { color: #0f0; }
        .error { color: #f00; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #333; background: #000; }
        h2 { color: #0ff; }
        pre { background: #111; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>

<h1>🔌 資料庫連線測試</h1>

<!-- 測試 1：環境變數 -->
<div class="section">
    <h2>環境變數檢查</h2>
    <?php
    echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? '<span class="error">❌ 未設定</span>') . "<br>";
    echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? '<span class="error">❌ 未設定</span>') . "<br>";
    echo "DB_USER: " . ($_ENV['DB_USER'] ?? '<span class="error">❌ 未設定</span>') . "<br>";
    echo "DB_PASS: " . (isset($_ENV['DB_PASS']) ? '<span class="success">✅ 已設定（長度: ' . strlen($_ENV['DB_PASS']) . '）</span>' : '<span class="error">❌ 未設定</span>') . "<br>";
    ?>
</div>

<!-- 測試 2：原始 PDO 連線 -->
<div class="section">
    <h2>原始PDO連線測試</h2>
    <?php
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME'] ?? 'planets_wish';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASS'] ?? '';

    echo "嘗試連線到: {$username}@{$host}/{$dbname}<br>";
    
    try {
        $pdo = new PDO(
            "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo '<span class="success">✅ 原始 PDO 連線成功！</span><br>';
        
        // 顯示 MySQL 版本
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        echo "MySQL 版本: {$version}<br>";
        
    } catch (PDOException $e) {
        echo '<span class="error">❌ 連線失敗: ' . $e->getMessage() . '</span><br>';
    }
    ?>
</div>

<!-- 測試 3：Database 類別 -->
<div class="section">
    <h2>Database類別測試</h2>
    <?php
    try {
        require_once 'models/Database.php';
        $db = Database::connect();
        echo '<span class="success">✅ Database 類別連線成功！</span><br>';
        
        // 測試查詢
        $stmt = $db->query("SELECT DATABASE() as current_db");
        $result = $stmt->fetch();
        echo "當前資料庫: " . $result['current_db'] . "<br>";
        
    } catch (Exception $e) {
        echo '<span class="error">❌ Database 類別失敗: ' . $e->getMessage() . '</span><br>';
    }
    ?>
</div>

<!-- 測試 4：檢查資料表 -->
<div class="section">
    <h2>資料表檢查</h2>
    <?php
    if (isset($db)) {
        try {
            $stmt = $db->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($tables)) {
                echo '<span class="error">⚠️ 資料庫中沒有任何資料表</span><br>';
            } else {
                echo '<span class="success">✅ 找到 ' . count($tables) . ' 個資料表：</span><br>';
                echo '<ul>';
                foreach ($tables as $table) {
                    echo "<li>{$table}</li>";
                }
                echo '</ul>';
            }
            
            // 檢查必要的資料表
            $requiredTables = ['users', 'planets', 'items', 'inventory', 'wishes'];
            echo "<br><strong>必要資料表檢查：</strong><br>";
            foreach ($requiredTables as $table) {
                if (in_array($table, $tables)) {
                    echo '<span class="success">✅ ' . $table . '</span><br>';
                } else {
                    echo '<span class="error">❌ ' . $table . ' (不存在)</span><br>';
                }
            }
            
        } catch (Exception $e) {
            echo '<span class="error">❌ 查詢失敗: ' . $e->getMessage() . '</span><br>';
        }
    }
    ?>
</div>

<!-- 測試 5：檢查 users 表結構 -->
<div class="section">
    <h2>Users表結構</h2>
    <?php
    if (isset($db)) {
        try {
            $stmt = $db->query("DESCRIBE users");
            $columns = $stmt->fetchAll();
            
            echo '<pre>';
            foreach ($columns as $col) {
                echo sprintf("%-25s %-15s %s\n", 
                    $col['Field'], 
                    $col['Type'], 
                    $col['Null'] === 'NO' ? 'NOT NULL' : 'NULL'
                );
            }
            echo '</pre>';
            
            // 檢查使用者數量
            $stmt = $db->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch();
            echo "資料庫中有 <strong>" . $result['count'] . "</strong> 個使用者<br>";
            
        } catch (Exception $e) {
            echo '<span class="error">❌ users 表不存在或查詢失敗: ' . $e->getMessage() . '</span><br>';
        }
    }
    ?>
</div>

<!-- 測試 6：User Model -->
<div class="section">
    <h2>User Model測試</h2>
    <?php
    try {
        require_once 'models/Users.php';
        $userModel = new Users();
        echo '<span class="success">✅ User Model 建立成功</span><br>';
        
        // 測試查詢不存在的使用者
        $testUser = $userModel->getByUsername('test_nonexistent_user');
        if ($testUser === false) {
            echo '<span class="success">✅ getByUsername() 方法正常運作</span><br>';
        }
        
    } catch (Exception $e) {
        echo '<span class="error">❌ User Model 失敗: ' . $e->getMessage() . '</span><br>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
    ?>
</div>

<!-- 測試 7：測試註冊功能 -->
<div class="section">
    <h2>模擬註冊功能測試</h2>
    <?php
    if (isset($userModel)) {
        echo "測試帳號: test_" . time() . "<br>";
        echo "測試 Email: test@example.com<br>";
        echo "測試密碼: 123456<br><br>";
        
        $testUsername = 'test_' . time();
        
        try {
            // 檢查帳號是否存在
            $existing = $userModel->getByUsername($testUsername);
            if ($existing) {
                echo '<span class="error">⚠️ 測試帳號已存在</span><br>';
            } else {
                echo '<span class="success">✅ 測試帳號不存在，可以註冊</span><br>';
            }
            
            // 驗證 Email
            $testEmail = 'test@example.com';
            if (filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                echo '<span class="success">✅ Email 格式驗證通過</span><br>';
            }
            
            // 測試密碼加密
            $testPassword = '123456';
            $hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
            echo '<span class="success">✅ 密碼加密成功</span><br>';
            echo "加密後: " . substr($hashedPassword, 0, 30) . "...<br>";
            
            // 測試密碼驗證
            if (password_verify($testPassword, $hashedPassword)) {
                echo '<span class="success">✅ 密碼驗證功能正常</span><br>';
            }
            
        } catch (Exception $e) {
            echo '<span class="error">❌ 測試失敗: ' . $e->getMessage() . '</span><br>';
        }
    }
    ?>
</div>

</body>
</html>