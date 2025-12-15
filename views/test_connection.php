<?php
echo "<h3>測試 1：直接讀取 .env 檔案</h3>";
if (file_exists('.env')) {
    echo "✅ .env 檔案存在<br><br>";
    echo "<pre>";
    echo htmlspecialchars(file_get_contents('.env'));
    echo "</pre>";
} else {
    echo "❌ .env 檔案不存在<br>";
}

echo "<hr>";

echo "<h3>測試 2：手動載入 .env</h3>";
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? '未讀取到') . "<br>";
echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? '未讀取到') . "<br>";
echo "DB_USER: " . ($_ENV['DB_USER'] ?? '未讀取到') . "<br>";
echo "DB_PASS: " . ($_ENV['DB_PASS'] ?? '未讀取到') . "<br>";

echo "<hr>";

echo "<h3>測試 3：嘗試連線資料庫</h3>";
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'planets_wish';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';

echo "使用的連線資訊：<br>";
echo "Host: {$host}<br>";
echo "Database: {$dbname}<br>";
echo "Username: {$username}<br>";
echo "Password: " . (empty($password) ? '(空)' : '有密碼（長度: ' . strlen($password) . '）') . "<br><br>";

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password
    );
    echo "✅ 連線成功！<br>";
} catch (PDOException $e) {
    echo "❌ 連線失敗: " . $e->getMessage() . "<br>";
}