<?php
// public/api/send_sms.php

require_once __DIR__ . '/../../src/Models/Settings.php';
require_once __DIR__ . '/../../src/Services/TencentSmsService.php';
require_once __DIR__ . '/../../src/Models/VerificationCode.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Parse JSON input
$input = json_decode(file_get_contents('php://input'), true);
$phone = $input['phone'] ?? '';
$type = $input['type'] ?? 'login'; // login or register

if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => '手机号不能为空']);
    exit;
}

// Simple validation for phone (assuming Chinese mobile)
if (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => '手机号格式不正确']);
    exit;
}

// Rate Limiting (60s)
if (VerificationCode::checkRateLimit($phone)) {
    echo json_encode(['success' => false, 'message' => '发送太频繁，请稍后再试']);
    exit;
}

// Generate Code (6 digits)
$code = (string)rand(100000, 999999);

// Get Settings
$settings = Settings::getAll();
$smsService = new TencentSmsService($settings);

// Determine Template ID based on type
$templateId = null;
switch ($type) {
    case 'register':
        $templateId = $settings['smsTemplateRegister'] ?? null;
        break;
    case 'login':
        // Reuse login/register or bind? Usually login uses the same as register or a generic verify one.
        // If no specific login template, maybe fallback to register or a 'login' one if I added it.
        // User screenshot had: Register, Forgot, Bind.
        // For login, maybe use Register (often "Login/Register") or I should add a Login one?
        // Let's assume Register template is general purpose verification or check if I should add Login.
        // The user requirement said: "短信验证码登录（默认方式）".
        // The screenshot didn't show "Login" template explicitly, but usually "Register" or "Common Verification" is used.
        // I'll fallback to 'smsTemplateRegister' if no login specific one, or add one.
        // Let's use 'smsTemplateRegister' for now as it's likely a generic "Verification Code" template.
        $templateId = $settings['smsTemplateRegister'] ?? null; 
        break;
    case 'forgot_password':
        $templateId = $settings['smsTemplateForgot'] ?? null;
        break;
    case 'bind':
        $templateId = $settings['smsTemplateBind'] ?? null;
        break;
    default:
        $templateId = $settings['smsTemplateRegister'] ?? null;
}

// Send SMS
$result = $smsService->sendVerificationCode($phone, $code, $templateId);

if ($result['status']) {
    // Save to DB
    if (VerificationCode::create($phone, $code, $type)) {
        error_log("SMS Sent Successfully to $phone (Type: $type)");
        echo json_encode(['success' => true, 'message' => '验证码已发送']);
    } else {
        echo json_encode(['success' => false, 'message' => '系统错误，无法保存验证码']);
    }
} else {
    // Log error
    error_log("SMS Send Failed: " . $result['message']);
    echo json_encode(['success' => false, 'message' => '短信发送失败: ' . $result['message']]);
}
