<?php
namespace controllers;

class BaseController
{
    protected function view($view, $data = [])
    {
        // // 除錯用
        // echo '<div style="position: fixed; top: 0; left: 0; z-index: 99999; background: yellow; color: black; padding: 10px; border: 3px solid red;">';
        // echo "BaseController::view() 被呼叫了！<br>";
        // echo "view = " . $view . "<br>";
        // echo "data = ";
        // var_dump($data);
        // echo "</div>";
        
        $viewPath = __DIR__ . "/../views/{$view}.php";
        
        // echo '<div style="position: fixed; top: 150px; left: 0; z-index: 99999; background: orange; color: black; padding: 10px; border: 3px solid red;">';
        // echo "viewPath = " . $viewPath . "<br>";
        // echo "file_exists = " . (file_exists($viewPath) ? 'YES' : 'NO') . "<br>";
        // echo "</div>";
        
        if (!file_exists($viewPath)) {
            die("View not found: {$view}");
        }
        
        //  在 require 之前 extract
        extract($data, EXTR_SKIP);
        
        // 測試：確認 extract 後變數存在
        // echo '<div style="position: fixed; top: 300px; left: 0; z-index: 99999; background: lime; color: black; padding: 10px; border: 3px solid red;">';
        // echo "extract 後，當前作用域的變數：<br>";
        // echo "isset(\$pageTitle) = " . (isset($pageTitle) ? 'YES' : 'NO') . "<br>";
        // echo "isset(\$hasWishedToday) = " . (isset($hasWishedToday) ? 'YES' : 'NO') . "<br>";
        // echo "get_defined_vars() = <pre>";
        // print_r(array_keys(get_defined_vars()));
        // echo "</pre></div>";
        
        require $viewPath;
    }
    
    protected function json($data, $statusCode = 200)
    {
        if (ob_get_length()) {
            ob_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
}