<?php
session_start();

// 使用絕對路徑載入 .env
$envPath = __DIR__ . '/.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // 跳過註解
        if (strpos(trim($line), '#') === 0) continue;
        
        // 跳過沒有 = 的行
        if (strpos($line, '=') === false) continue;
        
        // 分割 key 和 value
        list($key, $value) = explode('=', $line, 2);
        
        // 移除前後空白和引號
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, '"\''); // 移除引號
        
        $_ENV[$key] = $value;
    }
} else {
    error_log(".env 檔案不存在於: " . $envPath);
}

// 簡單的自動載入
spl_autoload_register(function ($class) {
    $file = __DIR__ . "/{$class}.php";
    if (file_exists($file)) {
        require $file;
    }
});

// 開發環境顯示錯誤
error_reporting(E_ALL);
ini_set('display_errors', 1);