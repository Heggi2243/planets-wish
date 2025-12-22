<?php
namespace controllers;

/* AuthController - auth.js跟verify-email.js用 
未來如果需要處理會員資料(個人資料、修改密碼、查詢...等)再來建UsersController */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/BaseController.php';

use models\Users;
use services\EmailService;

class AuthController extends BaseController
{

    private $userModel;
    private $emailService;

     public function __construct()
    {
        $this->userModel = new Users();
        $this->emailService = new EmailService();
    }

    /**
     * 登入/註冊頁面
     */
    public function index()
    {
        $this->view('auth/index', [
            'pageTitle' => 'Planets-Wish | 行星之願'
        ]);
    }


    /**
     *  登入
     */
    public function login()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            return $this->json([
                'success' => false,
                'message' => '請輸入帳號密碼'
            ]);
        }

        try {
            $result = $this->userModel->login($username, $password);
            
            if ($result['success']) {
                $_SESSION['user_id'] = $result['user_id'];
                $_SESSION['username'] = $result['username'];
                
                return $this->json([
                    'success' => true,
                    'message' => '登入成功'
                ]);
            } else {
                return $this->json($result);
            }
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => '登入失敗',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 處理註冊
     */
    public function register()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $username = $input['username'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        
        // 驗證
        if (strlen($username) < 3) {
            return $this->json([
                'success' => false,
                'message' => '帳號至少需要3個字元'
            ]);
        }
        
        if (strlen($password) < 6) {
            return $this->json([
                'success' => false,
                'message' => '密碼至少需要6個字元'
            ]);
        }
        
        try {
            // 檢查帳號是否存在
            if ($this->userModel->getByUsername($username)) {
                return $this->json([
                    'success' => false,
                    'message' => '此帳號已被註冊'
                ]);
            }
            
            $result = $this->userModel->register($username, $password, $email);
            
            if ($result) {
                // 發送驗證信
                $this->emailService->sendVerificationEmail(
                    $email,
                    $username,
                    $result['verification_token']
                );
                
                return $this->json([
                    'success' => true,
                    'message' => '註冊成功！請至信箱收取驗證信',
                    'email' => $email
                ]);
            }
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => '註冊失敗',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 登出
     */
    public function logout()
    {
        session_destroy();
        return $this->json([
            'success' => true,
            'message' => '登出成功'
        ]);
    }

    /**
     * 發送驗證信
     */
    private function sendVerificationEmail($email, $username, $token)
    {
        try {
            $emailSent = $this->emailService->sendVerificationEmail($email, $username, $token);
            
            if ($emailSent) {
                return [
                    'success' => true,
                    'message' => '驗證信已發送',
                    'email' => $email
                ];
            } else {
                error_log("Email發送失敗：未拋出異常但回傳false");
                return [
                    'success' => false,
                    'message' => '驗證信發送失敗',
                    'email' => $email,
                    'email_failed' => true
                ];
            }
        } catch (\Exception $e) {
            error_log("Email發送異常: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '驗證信發送失敗',
                'email' => $email,
                'email_failed' => true,
                'debug' => $e->getMessage()
            ];
        }
    }

    /**
     * 重新發送驗證信
     */
    public function resendVerification()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        
        error_log("重新發送驗證信 - Email: $email");
        
        if (empty($email)) {
            return $this->json([
                'success' => false,
                'message' => '請提供email'
            ]);
        }
        
        try {
            $result = $this->userModel->resendVerification($email);
            error_log("resendVerification 結果: " . print_r($result, true));
            
            if ($result) {
                $response = $this->sendVerificationEmail(
                    $result['email'],
                    $result['username'],
                    $result['verification_token']
                );
                
                if ($response['success']) {
                    $response['message'] = '驗證信已重新發送';
                }
                
                return $this->json($response);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => '找不到此Email，或已經驗證過了'
                ]);
            }
        } catch (\Exception $e) {
            error_log("重新發送異常: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => '重新發送失敗',
                'error' => $e->getMessage()
            ], 500);
        }
    }



}