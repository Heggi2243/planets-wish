<?php
/**
 * index.php
 * 入口檔案:處理所有請求
 */


session_start();

require_once __DIR__ . '/config/config.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// 移除專案路徑前綴
$baseDir = '/planets-wish'; 
if (strpos($uri, $baseDir) === 0) {
    $uri = substr($uri, strlen($baseDir));
}

// 處理空路徑（根目錄）
if ($uri === '' || $uri === '/') {
    $uri = '/auth';
}

$routes = [
    // 驗證相關
    'GET:/auth' => ['AuthController', 'index'],
    'POST:/auth/login' => ['AuthController', 'login'],
    'POST:/auth/register' => ['AuthController', 'register'],
    'POST:/auth/logout' => ['AuthController', 'logout'],
    'POST:/auth/resend-verification' => ['AuthController', 'resendVerification'],  
    
    // 許願相關
    'GET:/wish' => ['WishController', 'index'],
    'GET:/wish/create' => ['WishController', 'create'],
    'POST:/wish/summon' => ['WishController', 'store'],
    'GET:/wish/record' => ['WishController', 'record'],
];

// 路由匹配
$routeKey = "{$method}:{$uri}";

// Debug輸出
error_log("=== 路由調試 ===");
error_log("原始 URI: " . $_SERVER['REQUEST_URI']);
error_log("處理後 URI: {$uri}");
error_log("HTTP Method: {$method}");
error_log("路由 Key: {$routeKey}");
error_log("__DIR__: " . __DIR__);

if (isset($routes[$routeKey])) {
    [$controllerName, $action] = $routes[$routeKey];
    
    $controllerFile = __DIR__ . "/controllers/{$controllerName}.php";
    
    error_log("尋找 Controller: {$controllerFile}");
    error_log("檔案是否存在: " . (file_exists($controllerFile) ? '是' : '否'));
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        // 檢查是否使用命名空間
        $controllerClass = "controllers\\{$controllerName}";
        
        error_log("嘗試建立 Controller: {$controllerClass}");
        
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            $controller->$action();
        } else {
            // 如果沒有命名空間，直接使用類別名稱
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                $controller->$action();
            } else {
                http_response_code(500);
                echo "Class not found: {$controllerClass} 或 {$controllerName}";
                error_log("類別不存在");
            }
        }
    } else {
        http_response_code(404);
        echo "<h1>Controller not found</h1>";
        echo "<p>尋找的路徑: <strong>{$controllerFile}</strong></p>";
        echo "<p>請確認檔案是否存在於此位置</p>";
        
        // 列出實際存在的檔案
        $controllerDir = __DIR__ . "/controllers";
        if (is_dir($controllerDir)) {
            echo "<h3>Controllers 目錄內容：</h3><ul>";
            $files = scandir($controllerDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    echo "<li>{$file}</li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p>Controllers 目錄不存在: {$controllerDir}</p>";
        }
    }
} else {
    http_response_code(404);
    echo "<h1>404 - Page not found</h1>";
    echo "<p>找不到路由: <strong>{$routeKey}</strong></p>";
    echo "<h3>可用路由：</h3><ul>";
    foreach ($routes as $route => $handler) {
        echo "<li>{$route}</li>";
    }
    echo "</ul>";
}