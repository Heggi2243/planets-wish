<?php

require_once __DIR__ . '/../config.php';
require_once '../models/Database.php';
require_once '../models/User.php';

// 設定json回應
header('Content-Type: application/json');

// 取得請求資料
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

$userModel = new User();

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

    $user = $userModel->login($username, $password);

    if ($user) {

    } else {
        echo json_encode([
            'success' => false,
            'message' => '帳號或密碼錯誤'
        ]);
    }
}