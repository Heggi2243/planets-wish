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
        $this->mailer = new PHOMailer(true);
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



}

