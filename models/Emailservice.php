<?php
/**
 * Email Service
 * 處理所有 Email 相關功能
 */

// 懶得裝composer，手動引入
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    /**
     * 設定SMTP
     */
    private function configure()
    {

        try {
            //server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $_ENV['SMTP_USERNAME'] ?? '';
            $this->mailer->Password   = $_ENV['SMTP_PASSWORD'] ?? '';
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = $_ENV['SMTP_PORT'] ?? 587;
            
        } catch (Exception $e) {

            error_log("Email configuration error: " . $e->getMessage());
        }
    }

    /**
     * 發送驗證信
     */
    public function sendVerificationEmail($email, $username, $token)
    {
        try {
            //驗證link
            $verifyUrl = $this->getBaseUrl() . "/verify-email.php?token=" .  urlencode($token);

            $this->mailer->addAddress($email, $username);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = '【Planets-Wish】Email驗證';

            //mail內容
            $this->mailer->Body = $this->getVerificationEmailTemplate($username, $verifyUrl);
            $this->mailer->AltBody = "您好 {$username}，\n\n請點擊以下連結驗證您的Email：\n{$verifyUrl}\n\n此連結將在24小時後失效。";
            
            $this->mailer->send();
            return true;
            
        } catch(Exception $e) {

            error_log("Email sending failed: " . $this->mailer->ErrorInfo);
            return false;

        }
    }

    /**
     * 取得網站基礎URL
     */
    private function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }

    /**
     * Email 模板
     */
    private function getVerificationEmailTemplate($username, $verifyUrl)
    {
        return <<<HTML
    <!DOCTYPE html>
    <html lang="zh-TW">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                margin: 0;
                padding: 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .container {
                max-width: 600px;
                margin: 40px auto;
                background: rgba(255, 255, 255, 0.95);
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }
            .header {
                background: linear-gradient(135deg, #00f2ff 0%, #a855f7 100%);
                padding: 40px 20px;
                text-align: center;
            }
            .header h1 {
                margin: 0;
                color: white;
                font-size: 32px;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
                letter-spacing: 0.2em;
            }
            .content {
                padding: 40px 30px;
                color: #333;
            }
            .content h2 {
                color: #667eea;
                margin-top: 0;
            }
            .button {
                display: inline-block;
                padding: 16px 40px;
                background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
                color: white;
                text-decoration: none;
                border-radius: 50px;
                font-weight: bold;
                margin: 20px 0;
                box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
                transition: transform 0.2s;
            }
            .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(6, 182, 212, 0.5);
            }
            .footer {
                background: #f8f9fa;
                padding: 20px;
                text-align: center;
                color: #666;
                font-size: 12px;
            }
            .note {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin: 20px 0;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>PLANETS WISH</h1>
            </div>
            <div class="content">
                <h2>哈囉, {$username}! 👋</h2>
                <p>歡迎加入 Planets-Wish 星願！</p>
                <p>請點擊下方按鈕驗證您的 Email 地址：</p>
                
                <div style="text-align: center;">
                    <a href="{$verifyUrl}" class="button">驗證 Email</a>
                </div>
                
                <div class="note">
                    <strong>⚠️ 注意事項：</strong>
                    <ul style="margin: 10px 0;">
                        <li>此驗證連結將在 <strong>24 小時後</strong>失效</li>
                        <li>如果您沒有註冊 Planets-Wish，請忽略此信</li>
                    </ul>
                </div>
                
                <p style="color: #666; font-size: 14px; margin-top: 30px;">
                    如果按鈕無法點擊，請複製以下連結到瀏覽器：<br>
                    <a href="{$verifyUrl}" style="color: #06b6d4; word-break: break-all;">{$verifyUrl}</a>
                </p>
            </div>
            <div class="footer">
                <p>© 2025 Planets-Wish. All rights reserved.</p>
                <p>此信件由系統自動發送，請勿直接回覆。</p>
            </div>
        </div>
    </body>
    </html>
    HTML;
        }



}

