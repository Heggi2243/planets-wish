<?php
/**
 * 初始化日誌目錄
 * 執行: php init-logs.php
 */

$logsDir = __DIR__ . '/logs';

if (!file_exists($logsDir)) {
    mkdir($logsDir, 0755, true);
    echo "✅ 建立 logs 目錄成功\n";
} else {
    echo "ℹ️  logs 目錄已存在\n";
}

$authLog = $logsDir . '/auth_errors.log';
if (!file_exists($authLog)) {
    touch($authLog);
    chmod($authLog, 0644);
    echo "✅ 建立 auth_errors.log 檔案成功\n";
} else {
    echo "ℹ️  auth_errors.log 已存在\n";
}

echo "\n目錄結構:\n";
echo $logsDir . "\n";
echo "└── auth_errors.log\n";