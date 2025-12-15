<?php
namespace controllers;

class BaseController
{
    /**
     * 渲染視圖
     */
    protected function view($view, $data = [])
    {
        // 將資料轉為變數
        extract($data);
        
        // 載入視圖檔案
        $viewPath = __DIR__ . "/../views/{$view}.php";
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("View not found: {$view}");
        }
    }
    
    /**
     * JSON 回應
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * 重導向
     */
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
}