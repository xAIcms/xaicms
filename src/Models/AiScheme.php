<?php
// src/Models/AiScheme.php

require_once __DIR__ . '/../Config/Database.php';

class AiScheme {
    public static function create($data) {
        self::checkInit();
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO ai_schemes (user_id, name, config, target_count, daily_limit, cost_per_post, frozen_points, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['user_id'],
            $data['name'],
            json_encode($data['config']),
            $data['target_count'],
            $data['daily_limit'] ?? 0,
            $data['cost_per_post'],
            $data['frozen_points'],
            'pending'
        ]);
        return $db->lastInsertId();
    }
    
    public static function update($id, $data) {
        self::checkInit();
        $db = Database::getInstance()->getConnection();
        
        $fields = [];
        $values = [];
        
        if (isset($data['name'])) {
            $fields[] = 'name = ?';
            $values[] = $data['name'];
        }
        if (isset($data['config'])) {
            $fields[] = 'config = ?';
            $values[] = json_encode($data['config']);
        }
        if (isset($data['target_count'])) {
            $fields[] = 'target_count = ?';
            $values[] = $data['target_count'];
        }
        if (isset($data['daily_limit'])) {
            $fields[] = 'daily_limit = ?';
            $values[] = $data['daily_limit'];
        }
        if (isset($data['frozen_points'])) {
            $fields[] = 'frozen_points = ?';
            $values[] = $data['frozen_points'];
        }
        
        if (empty($fields)) return true;
        
        $values[] = $id;
        $sql = "UPDATE ai_schemes SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public static function delete($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM ai_schemes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function updateStatusAdmin($id, $status, $notes = '') {
        self::checkInit();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE ai_schemes SET status = ?, admin_notes = ? WHERE id = ?");
        return $stmt->execute([$status, $notes, $id]);
    }

    public static function updateStatus($id, $status, $schemeKey = null) {
        self::checkInit();
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE ai_schemes SET status = ?, updated_at = NOW()";
        $params = [$status];
        
        if ($schemeKey) {
            $sql .= ", scheme_key = ?";
            $params[] = $schemeKey;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function updateProgress($id, $count, $pointsConsumed = 0) {
        self::checkInit();
        $db = Database::getInstance()->getConnection();
        // Update generated_count and reduce frozen_points
        $stmt = $db->prepare("UPDATE ai_schemes SET generated_count = ?, frozen_points = GREATEST(0, frozen_points - ?) WHERE id = ?");
        return $stmt->execute([$count, $pointsConsumed, $id]);
    }

    private static function checkInit() {
        $db = Database::getInstance()->getConnection();
        
        // Create ai_schemes table
        $sql = "CREATE TABLE IF NOT EXISTS ai_schemes (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            config JSON,
            target_count INT UNSIGNED NOT NULL DEFAULT 1,
            daily_limit INT UNSIGNED NOT NULL DEFAULT 0,
            generated_count INT UNSIGNED NOT NULL DEFAULT 0,
            cost_per_post INT UNSIGNED NOT NULL DEFAULT 1,
            frozen_points INT UNSIGNED NOT NULL DEFAULT 0,
            status ENUM('pending', 'approved', 'running', 'completed', 'rejected') DEFAULT 'pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->exec($sql);

        // Ensure ai_schemes table has frozen_points column (Fix for missing column error)
        try {
            $columns = $db->query("SHOW COLUMNS FROM ai_schemes")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('frozen_points', $columns)) {
                $db->exec("ALTER TABLE ai_schemes ADD COLUMN frozen_points INT UNSIGNED NOT NULL DEFAULT 0 AFTER cost_per_post");
            }
            if (!in_array('cost_per_post', $columns)) {
                $db->exec("ALTER TABLE ai_schemes ADD COLUMN cost_per_post INT UNSIGNED NOT NULL DEFAULT 1 AFTER generated_count");
            }
            if (!in_array('daily_limit', $columns)) {
                $db->exec("ALTER TABLE ai_schemes ADD COLUMN daily_limit INT UNSIGNED NOT NULL DEFAULT 0 AFTER target_count");
            }
            
            // Fix ENUM values for status if 'approved' is missing (Auto-migration)
            $stmt = $db->query("SHOW COLUMNS FROM ai_schemes LIKE 'status'");
            $statusCol = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($statusCol && strpos($statusCol['Type'], "'approved'") === false) {
                 $db->exec("ALTER TABLE ai_schemes MODIFY COLUMN status ENUM('pending', 'approved', 'running', 'completed', 'rejected') DEFAULT 'pending'");
            }
            
        } catch (Exception $e) {
            // Ignore
        }
        
        // Ensure users table has frozen_points
        try {
            $columns = $db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('frozen_points', $columns)) {
                $db->exec("ALTER TABLE users ADD COLUMN frozen_points INT UNSIGNED NOT NULL DEFAULT 0 AFTER points");
            }
        } catch (Exception $e) {
            // Ignore
        }
    }

    public static function find($id) {
        self::checkInit();
        return self::findById($id);
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM ai_schemes WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $scheme = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($scheme) {
            $scheme['config'] = json_decode($scheme['config'], true);
        }
        return $scheme;
    }

    public static function getByUser($userId, $limit = 20, $offset = 0) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM ai_schemes WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $schemes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($schemes as &$scheme) {
            $scheme['config'] = json_decode($scheme['config'], true);
        }
        return $schemes;
    }

    public static function countByUser($userId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM ai_schemes WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public static function getAll($limit = 20, $offset = 0, $status = null) {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT s.*, u.name as user_name, u.points as user_points, u.frozen_points as user_frozen_points, ac.api_key 
                FROM ai_schemes s 
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN api_configs ac ON s.id = ac.scheme_id";
        
        $params = [];
        if ($status) {
            $sql .= " WHERE s.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $db->prepare($sql);
        $i = 1;
        if ($status) {
            $stmt->bindValue($i++, $status);
        }
        $stmt->bindValue($i++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $schemes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($schemes as &$scheme) {
            $scheme['config'] = json_decode($scheme['config'], true);
        }
        return $schemes;
    }

    public static function countAll($status = null) {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM ai_schemes";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
}
