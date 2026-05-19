<?php
// src/Models/Developer.php — Developer/plugin author model

class Developer {
    public static function register(int $userId, array $data): ?int {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO developers (user_id, name, description, wechat_account, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([
            $userId,
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['wechat_account'] ?? '',
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function findByUserId(int $userId): ?array {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM developers WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function find(int $id): ?array {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT d.*, u.name as user_name, u.email FROM developers d JOIN users u ON d.user_id = u.id WHERE d.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getBalance(int $userId): float {
        $dev = self::findByUserId($userId);
        return $dev ? (float)$dev['balance'] : 0;
    }

    public static function getTransactions(int $developerId, int $limit = 20): array {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM developer_transactions WHERE developer_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$developerId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Withdraw earnings to WeChat
     */
    public static function requestWithdraw(int $developerId, float $amount): bool {
        $dev = self::find($developerId);
        if (!$dev || $dev['balance'] < $amount || $amount <= 0) return false;

        $pdo = Database::getInstance()->getConnection();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE developers SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $developerId]);

            $stmt = $pdo->prepare("INSERT INTO developer_transactions (developer_id, amount, type, status, note, created_at) VALUES (?, ?, 'withdraw', 'pending', ?, NOW())");
            $stmt->execute([$developerId, $amount, '提现到微信: ' . ($dev['wechat_account'] ?? 'N/A')]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
