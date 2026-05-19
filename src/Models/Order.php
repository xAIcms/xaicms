<?php
// src/Models/Order.php — Payment order model

class Order {
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    public static function create(array $data): int {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO orders (order_no, user_id, plan, amount, amount_cents, status, payment_method, description, developer_id, revenue_share, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $data['order_no'],
            $data['user_id'] ?? 0,
            $data['plan'] ?? 'pro',
            $data['amount'] ?? 0,
            $data['amount_cents'] ?? 0,
            self::STATUS_PENDING,
            $data['payment_method'] ?? 'wechat',
            $data['description'] ?? '',
            $data['developer_id'] ?? null,
            $data['revenue_share'] ?? 0,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function findByOrderNo(string $orderNo): ?array {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_no = ? LIMIT 1");
        $stmt->execute([$orderNo]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function markPaid(string $orderNo, string $transactionId, array $rawData = []): bool {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            UPDATE orders SET status = ?, transaction_id = ?, paid_at = NOW(), raw_data = ?, updated_at = NOW()
            WHERE order_no = ? AND status = ?
        ");
        $result = $stmt->execute([self::STATUS_PAID, $transactionId, json_encode($rawData), $orderNo, self::STATUS_PENDING]);

        if ($result && $stmt->rowCount() > 0) {
            // Activate the plan
            $order = self::findByOrderNo($orderNo);
            if ($order && !empty($order['plan']) && !empty($order['user_id'])) {
                self::activatePlan($order['user_id'], $order['plan']);
            }
            // Credit developer revenue
            if ($order && !empty($order['developer_id']) && $order['revenue_share'] > 0) {
                self::creditDeveloperRevenue($order['developer_id'], $order['revenue_share'], $order['order_no']);
            }
        }
        return $result;
    }

    private static function activatePlan(int $userId, string $plan): void {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE users SET plan = ?, plan_activated_at = NOW() WHERE id = ?");
        $stmt->execute([$plan, $userId]);
        // Also update global settings if admin user
        $user = User::find($userId);
        if ($user && ($user['role'] ?? '') === 'admin') {
            Settings::update('plan', $plan);
        }
    }

    private static function creditDeveloperRevenue(int $developerId, float $amount, string $orderNo): void {
        $pdo = Database::getInstance()->getConnection();
        // Add to developer balance
        $stmt = $pdo->prepare("UPDATE developers SET balance = balance + ?, total_earned = total_earned + ? WHERE id = ?");
        $stmt->execute([$amount, $amount, $developerId]);
        // Log the transaction
        $stmt = $pdo->prepare("INSERT INTO developer_transactions (developer_id, order_no, amount, type, created_at) VALUES (?, ?, ?, 'revenue', NOW())");
        $stmt->execute([$developerId, $orderNo, $amount]);
    }

    public static function getOrdersByUser(int $userId, int $limit = 20): array {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public static function generateOrderNo(): string {
        return 'XAI' . date('YmdHis') . strtoupper(bin2hex(random_bytes(4)));
    }
}
