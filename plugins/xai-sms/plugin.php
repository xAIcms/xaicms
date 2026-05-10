<?php
/**
 * Plugin Name: SMS Verification
 * Description: Phone number verification via Tencent Cloud SMS. Requires Tencent Cloud credentials.
 * Version: 1.0.0
 * Author: xAIcms
 */

// Register SMS API endpoint
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if ($uri === '/api/send-sms' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../src/Config/Database.php';
    require_once __DIR__ . '/../../src/Models/Settings.php';
    require_once __DIR__ . '/../../src/Models/VerificationCode.php';
    require_once __DIR__ . '/src/TencentSmsService.php';

    header('Content-Type: application/json');

    $phone = $_POST['phone'] ?? '';
    $type = $_POST['type'] ?? 'register';

    if (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
        exit;
    }

    // Rate limit: 1 SMS per 60 seconds
    $recent = VerificationCode::findRecent($phone, $type, 60);
    if ($recent) {
        echo json_encode(['success' => false, 'message' => 'Please wait before requesting another code']);
        exit;
    }

    $code = rand(100000, 999999);
    VerificationCode::create($phone, $code, $type, 300); // 5 min expiry

    try {
        TencentSmsService::send($phone, $code, $type);
        echo json_encode(['success' => true, 'message' => 'Code sent']);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'SMS send failed: ' . $e->getMessage()]);
    }
    exit;
}
