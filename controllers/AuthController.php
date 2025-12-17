<?php

require_once __DIR__ . '/../config.php';
require_once '../models/Database.php';
require_once '../models/Users.php';

// 設定json回應
header('Content-Type: application/json');

// 取得請求資料
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

$userModel = new Users();

// ======= 登入 ========
if ($action === 'login') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => '沒有收到資料'
        ]);
        exit;
    }

    // 嘗試登入
    $user = $userModel->login($username, $password);

    if ($user) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        echo json_encode([
            'success' => true,
            'message' => '登入成功'
        ]);

    } else {
        echo json_encode([
            'success' => false,
            'message' => '帳號或密碼錯誤'
        ]);
    }
    exit;
}

// ========= 處理註冊 ===========

if ($action === 'register') {
    $username = $input['username'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    // 驗證欄位
    // if (empty($username) || empty($email) || empty($password)) {
    //     echo json_encode([
    //         'success' => false,
    //         'message' => '請填寫完整資料'
    //     ]);
    //     exit;
    // }
    
    // 驗證帳號長度
    if (strlen($username) < 3) {
        echo json_encode([
            'success' => false,
            'message' => '帳號太短了，請至少輸入3個字元'
        ]);
        exit;
    }
    
    // 驗證密碼長度
    if (strlen($password) < 6) {
        echo json_encode([
            'success' => false,
            'message' => '密碼太短了，請至少輸入3個字元'
        ]);
        exit;
    }
    
    /* 使用filter_var()驗證email
        其實應該要檢查CDN，才能避免被註冊攻擊，但我好懶*/
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email格式不正確喔'
        ]);
        exit;
    }
    
    // 檢查帳號是否已存在
    if ($userModel->getByUsername($username)) {
        echo json_encode([
            'success' => false,
            'message' => '這個帳號已經被註冊走了，換一個吧'
        ]);
        exit;
    }
    
    // 註冊新會員
    $userId = $userModel->register($username, $password, $email);
    
    if ($userId) {
        echo json_encode([
            'success' => true,
            'message' => '註冊成功'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '註冊失敗，請稍後再試'
        ]);
    }
    exit;
}

// 無效的action
echo json_encode([
    'success' => false,
    'message' => '錯誤，無效的訪問'
]);