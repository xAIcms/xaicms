<?php
// public/api/payment/wechat-notify.php — WeChat Pay V3 callback

require_once __DIR__ . '/../../src/Config/Database.php';
require_once __DIR__ . '/../../src/Models/Settings.php';
require_once __DIR__ . '/../../src/Models/Order.php';
require_once __DIR__ . '/../../src/Services/WechatPayService.php';

$body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_WECHATPAY_SIGNATURE'] ?? '';
$timestamp = $_SERVER['HTTP_WECHATPAY_TIMESTAMP'] ?? '';
$nonce = $_SERVER['HTTP_WECHATPAY_NONCE'] ?? '';
$serial = $_SERVER['HTTP_WECHATPAY_SERIAL'] ?? '';

$wechat = new WechatPayService();

// Verify signature
if (!$wechat->verifySignature($body, $signature, $timestamp, $nonce)) {
    http_response_code(401);
    echo json_encode(['code' => 'FAIL', 'message' => 'Signature verification failed']);
    exit;
}

// Parse notification
$data = json_decode($body, true);
if (!$data || ($data['event_type'] ?? '') !== 'TRANSACTION.SUCCESS') {
    http_response_code(200);
    echo json_encode(['code' => 'SUCCESS']);
    exit;
}

$resource = $data['resource'] ?? [];
$decrypted = $wechat->decryptResource(
    $resource['associated_data'] ?? '',
    $resource['nonce'] ?? '',
    $resource['ciphertext'] ?? ''
);

if (!$decrypted) {
    http_response_code(500);
    echo json_encode(['code' => 'FAIL', 'message' => 'Decrypt failed']);
    exit;
}

$txn = json_decode($decrypted, true);
$orderNo = $txn['out_trade_no'] ?? '';
$txnId = $txn['transaction_id'] ?? '';

if ($orderNo && $txnId) {
    Order::markPaid($orderNo, $txnId, $txn);
}

http_response_code(200);
echo json_encode(['code' => 'SUCCESS']);
