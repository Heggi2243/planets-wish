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
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);


// 檢查JSON解析是否成功
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON 解析錯誤: " . json_last_error_msg());
    echo json_encode([
        'success' => false,
        'message' => 'JSON 格式錯誤',
        'error' => json_last_error_msg()
    ]);
    exit;
}

try {
    $userModel = new Users();
    $emailService = new EmailService();
} catch (Exception $e) {
    error_log("初始化失敗: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => '系統初始化失敗',
        'error' => $e->getMessage()
    ]);
    exit;
}

/**
 * 發送驗證信的統一方法
 */
function sendVerificationEmailAndRespond($email, $username, $token)
{
    global $emailService;
    
    try {
        $emailSent = $emailService->sendVerificationEmail($email, $username, $token);
        
        if ($emailSent) {
            return [
                'success' => true,
                'message' => '驗證信已發送',
                'email' => $email
            ];
        } else {
            error_log("Email 發送失敗 - 未拋出異常但回傳 false");
            return [
                'success' => false,
                'message' => '驗證信發送失敗',
                'email' => $email,
                'email_failed' => true
            ];
        }
    } catch (Exception $e) {
        error_log("Email 發送異常: " . $e->getMessage());
        return [
            'success' => false,
            'message' => '驗證信發送失敗',
            'email' => $email,
            'email_failed' => true,
            'debug' => $e->getMessage()
        ];
    }
}

// ======= 登入 ========
if ($action === 'login') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    error_log("登入嘗試 - 帳號: $username");

    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => '沒有收到資料'
        ]);
        exit;
    }

    try {
        $result = $userModel->login($username, $password);
        error_log("登入結果: " . print_r($result, true));

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
    } catch (Exception $e) {
        error_log("登入異常: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => '登入失敗',
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// ========= 處理註冊 ===========
if ($action === 'register') {
    $username = $input['username'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    error_log("註冊嘗試 - 帳號: $username, Email: $email");
    
    // 驗證帳號長度
    if (strlen($username) < 3) {
        error_log("註冊失敗 - 帳號太短");
        echo json_encode([
            'success' => false,
            'message' => '帳號太短了，請至少輸入3個字元'
        ]);
        exit;
    }
    
    // 驗證密碼長度
    if (strlen($password) < 6) {
        error_log("註冊失敗 - 密碼太短");
        echo json_encode([
            'success' => false,
            'message' => '密碼太短了，請至少輸入6個字元'
        ]);
        exit;
    }
    
    // 驗證email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("註冊失敗 - Email 格式錯誤");
        echo json_encode([
            'success' => false,
            'message' => 'Email格式不正確喔'
        ]);
        exit;
    }
    
    try {
        // 檢查帳號是否已存在
        if ($userModel->getByUsername($username)) {
            error_log("註冊失敗 - 帳號已存在");
            echo json_encode([
                'success' => false,
                'message' => '這個帳號已經被註冊走了，換一個吧'
            ]);
            exit;
        }
        
        // 註冊新會員
        error_log("開始註冊新會員...");
        $result = $userModel->register($username, $password, $email);
        error_log("註冊結果: " . print_r($result, true));
        
        if ($result) {
            error_log("註冊成功，準備發送驗證信");
            
            // 發送驗證信
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
            
            error_log("最終回應: " . print_r($response, true));
            echo json_encode($response);
        } else {
            error_log("註冊失敗 - register() 回傳 false");
            echo json_encode([
                'success' => false,
                'message' => '註冊失敗，請稍後再試',
                'debug' => 'register() returned false - 可能是資料庫錯誤'
            ]);
        }
    } catch (Exception $e) {
        error_log("註冊異常: " . $e->getMessage());
        error_log("堆疊: " . $e->getTraceAsString());
        echo json_encode([
            'success' => false,
            'message' => '註冊失敗',
            'error' => $e->getMessage(),
            'debug' => $e->getFile() . ':' . $e->getLine()
        ]);
    }
    exit;
}

// ========== 重新發送驗證信 ========== 
if ($action === 'resend-verification') {
    $email = $input['email'] ?? '';
    
    error_log("重新發送驗證信 - Email: $email");
    
    if (empty($email)) {
        echo json_encode([
            'success' => false,
            'message' => '請提供email'
        ]);
        exit;
    }
    
    try {
        $result = $userModel->resendVerification($email);
        error_log("resendVerification 結果: " . print_r($result, true));
        
        if ($result) {
            $response = sendVerificationEmailAndRespond(
                $result['email'],
                $result['username'],
                $result['verification_token']
            );
            
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
    } catch (Exception $e) {
        error_log("重新發送異常: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => '重新發送失敗',
            'error' => $e->getMessage()
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
error_log("無效的 action: $action");
echo json_encode([
    'success' => false,
    'message' => '錯誤，無效的訪問',
    'debug' => "action = $action"
]);