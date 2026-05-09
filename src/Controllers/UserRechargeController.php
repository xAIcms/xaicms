<?php
// src/Controllers/UserRechargeController.php

require_once __DIR__ . '/../Models/PointPackage.php';
require_once __DIR__ . '/../Models/RechargeOrder.php';
require_once __DIR__ . '/../Utils/Csrf.php';

class UserRechargeController {
    
    private static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function index() {
        self::requireLogin();
        
        require_once __DIR__ . '/../Models/User.php';
        require_once __DIR__ . '/../Models/Settings.php';
        $user = User::findById($_SESSION['user_id']);
        $settings = Settings::getAll();
        $packages = PointPackage::getAll(true); // Only active
        $recentOrders = RechargeOrder::getByUser($_SESSION['user_id'], 5);
        
        require __DIR__ . '/../../templates/user/recharge.php';
    }

    public static function create() {
        self::requireLogin();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $packageId = $input['package_id'] ?? 0;
        $remark = $input['remark'] ?? '';
        $csrfToken = $input['csrf_token'] ?? '';
        
        if (!Csrf::check($csrfToken)) {
            echo json_encode(['success' => false, 'message' => '无效的 CSRF 令牌']);
            exit;
        }
        
        try {
            $orderId = RechargeOrder::create($_SESSION['user_id'], $packageId, $remark);
            if ($orderId) {
                echo json_encode(['success' => true, 'message' => '订单提交成功，请等待管理员审核', 'order_id' => $orderId]);
            } else {
                echo json_encode(['success' => false, 'message' => '订单创建失败']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
