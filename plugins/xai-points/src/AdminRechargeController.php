<?php
// src/Controllers/AdminRechargeController.php

require_once __DIR__ . '/../Models/RechargeOrder.php';
require_once __DIR__ . '/../Utils/Csrf.php';

class AdminRechargeController {
    
    private static function checkAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            exit("Access Denied");
        }
    }

    public static function index() {
        self::checkAdmin();
        
        $status = $_GET['status'] ?? 'all';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filterStatus = ($status === 'all') ? null : $status;
        
        $orders = RechargeOrder::getAll($limit, $offset, $filterStatus);
        $total = RechargeOrder::countAll($filterStatus);
        $totalPages = ceil($total / $limit);
        
        require __DIR__ . '/../../templates/admin/recharge_orders.php';
    }

    public static function approve() {
        self::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            $id = $_POST['id'] ?? 0;
            $remark = $_POST['remark'] ?? '';
            
            try {
                RechargeOrder::approve($id, $remark);
                $_SESSION['success'] = "订单 #{$id} 已通过，积分已发放。";
            } catch (Exception $e) {
                $_SESSION['error'] = "操作失败: " . $e->getMessage();
            }
            
            header('Location: /admin/recharge-orders');
            exit;
        }
    }

    public static function reject() {
        self::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            $id = $_POST['id'] ?? 0;
            $remark = $_POST['remark'] ?? '';
            
            try {
                RechargeOrder::reject($id, $remark);
                $_SESSION['success'] = "订单 #{$id} 已拒绝。";
            } catch (Exception $e) {
                $_SESSION['error'] = "操作失败: " . $e->getMessage();
            }
            
            header('Location: /admin/recharge-orders');
            exit;
        }
    }
    
    public static function updateRemark() {
        self::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            $id = $_POST['id'] ?? 0;
            $remark = $_POST['remark'] ?? '';
            
            try {
                RechargeOrder::updateRemark($id, $remark);
                $_SESSION['success'] = "订单 #{$id} 备注已更新。";
            } catch (Exception $e) {
                $_SESSION['error'] = "更新失败: " . $e->getMessage();
            }
            
            header('Location: /admin/recharge-orders');
            exit;
        }
    }
}
