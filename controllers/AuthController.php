<?php
/* AuthController */

require_once __DIR__ . '/../config.php';
require_once '../models/Database.php';
require_once '../models/Users.php';
require_once '../models/EmailService.php';

// 設定json回應
header('Content-Type: application/json');

// 取得請求資料
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

$userModel = new Users();
$emailService = new EmailService();

/**
 * 發送驗證信
 * @param string $email
 * @param string $username 
 * @param string $token 
 * @return array success和message內容
 */
function sendVerificationEmailAndRespond($email, $username, $token)
{
    global $emailService;
    
    $emailSent = $emailService->sendVerificationEmail($email, $username, $token);
    
    if ($emailSent) {
        return [
            'success' => true,
            'message' => '驗證信已發送',
            'email' => $email
        ];
    } else {
        return [
            'success' => false,
            'message' => '驗證信發送失敗，請稍後再試',
            'email' => $email,
            'email_failed' => true
        ];
    }
}

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
    $result = $userModel->login($username, $password);

    if ($result['success']) {
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['username'] = $result['username'];
        
        echo json_encode([
            'success' => true,
            'message' => '登入成功'
        ]);
    } else {
        echo json_encode($result);
    }
    exit;
}

// ========= 處理註冊 ===========
if ($action === 'register') {
    $username = $input['username'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
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
            'message' => '密碼太短了，請至少輸入6個字元'
        ]);
        exit;
    }
    
    // 驗證email
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
    $result = $userModel->register($username, $password, $email);
    
    if ($result) {
        // 使用統一的發送驗證信方法
        $response = sendVerificationEmailAndRespond(
            $email,
            $username,
            $result['verification_token']
        );
        
        // 客製化訊息
        if ($response['success']) {
            $response['message'] = '註冊成功！請至信箱收取驗證信';
        } else {
            $response['message'] = '註冊成功，但驗證信發送失敗。請稍後使用「重新發送」功能';
            $response['success'] = true; // 註冊本身是成功的
        }
        
        echo json_encode($response);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '註冊失敗，請稍後再試'
        ]);
    }
    exit;
}

// ========== 重新發送驗證信 ========== 
if ($action === 'resend-verification') {
    $email = $input['email'] ?? '';
    
    if (empty($email)) {
        echo json_encode([
            'success' => false,
            'message' => '請提供email'
        ]);
        exit;
    }
    
    // 取得使用者資訊並產生新token
    $result = $userModel->resendVerification($email);
    
    if ($result) {
        // 使用統一的發送驗證信方法
        $response = sendVerificationEmailAndRespond(
            $result['email'],
            $result['username'],
            $result['verification_token']
        );
        
        // 客製化訊息
        if ($response['success']) {
            $response['message'] = '驗證信已重新發送';
        }
        
        echo json_encode($response);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '找不到此Email，或已經驗證過了'
        ]);
    }
    exit;
}

// ========== 登出 ========== 
if ($action === 'logout') {
    session_destroy();
    echo json_encode([
        'success' => true,
        'message' => '登出成功'
    ]);
    exit;
}

// 無效的action
echo json_encode([
    'success' => false,
    'message' => '錯誤，無效的訪問'
]);