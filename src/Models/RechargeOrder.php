<?php
// src/Models/RechargeOrder.php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/PointPackage.php';
require_once __DIR__ . '/User.php';

class RechargeOrder {
    public static function ensureTable() {
        $db = Database::getInstance()->getConnection();
        $sql = "CREATE TABLE IF NOT EXISTS `recharge_orders` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED NOT NULL,
            `package_id` INT UNSIGNED NOT NULL,
            `amount` DECIMAL(10, 2) NOT NULL,
            `points` INT UNSIGNED NOT NULL,
            `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
            `user_remark` TEXT,
            `admin_remark` TEXT,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_user_status` (`user_id`, `status`),
            KEY `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->exec($sql);

        // Auto-migration: Check if user_remark column exists
        $checkSql = "SHOW COLUMNS FROM `recharge_orders` LIKE 'user_remark'";
        $stmt = $db->query($checkSql);
        if ($stmt->rowCount() == 0) {
            $alterSql = "ALTER TABLE `recharge_orders` ADD COLUMN `user_remark` TEXT AFTER `status`";
            $db->exec($alterSql);
        }
    }

    public static function create($userId, $packageId, $userRemark = '') {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        
        $package = PointPackage::findById($packageId);
        if (!$package) {
            throw new Exception("套餐不存在");
        }
        
        // Calculate total points with bonus
        $basePoints = $package['points'];
        $bonusPercent = $package['bonus_percent'];
        $totalPoints = $basePoints + floor($basePoints * ($bonusPercent / 100));
        
        $stmt = $db->prepare("INSERT INTO recharge_orders (user_id, package_id, amount, points, status, user_remark) VALUES (?, ?, ?, ?, 'pending', ?)");
        if ($stmt->execute([
            $userId,
            $packageId,
            $package['price'],
            $totalPoints,
            $userRemark
        ])) {
            return $db->lastInsertId();
        }
        return false;
    }

    public static function findById($id) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        // Join with users and packages for display
        $sql = "SELECT r.*, u.name as user_name, u.email as user_email, u.phone as user_phone, p.name as package_name 
                FROM recharge_orders r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN point_packages p ON r.package_id = p.id 
                WHERE r.id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getAll($limit = 20, $offset = 0, $status = null) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT r.*, u.name as user_name, u.email as user_email, u.phone as user_phone, p.name as package_name 
                FROM recharge_orders r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN point_packages p ON r.package_id = p.id";
        
        $params = [];
        if ($status) {
            $sql .= " WHERE r.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public static function countAll($status = null) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM recharge_orders";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public static function getByUser($userId, $limit = 20, $offset = 0) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT r.*, p.name as package_name 
                FROM recharge_orders r 
                LEFT JOIN point_packages p ON r.package_id = p.id 
                WHERE r.user_id = ? 
                ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function updateRemark($id, $adminRemark) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE recharge_orders SET admin_remark = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$adminRemark, $id]);
    }

    public static function approve($id, $adminRemark = '') {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        
        $order = self::findById($id);
        if (!$order || $order['status'] !== 'pending') {
            throw new Exception("订单不存在或已处理");
        }
        
        try {
            $db->beginTransaction();
            
            // Update order status
            $stmt = $db->prepare("UPDATE recharge_orders SET status = 'approved', admin_remark = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$adminRemark, $id]);
            
            if (method_exists('User', 'addPoints')) {
                User::addPoints($order['user_id'], $order['points'], "充值订单 #{$id}");
            } else {
                // Fallback implementation
                $stmt = $db->prepare("UPDATE users SET points = points + ? WHERE id = ?");
                $stmt->execute([$order['points'], $order['user_id']]);
                // Log if possible
                if (method_exists('User', 'logActivity')) {
                    User::logActivity($order['user_id'], 'add_points', "充值订单 #{$id}", 'System');
                }
            }
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function reject($id, $adminRemark = '') {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("UPDATE recharge_orders SET status = 'rejected', admin_remark = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$adminRemark, $id]);
    }
}
