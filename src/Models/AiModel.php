<?php
// php_backend/src/Models/AiModel.php

require_once __DIR__ . '/../Config/Database.php';

class AiModel {
    public static function ensureTable() {
        $pdo = Database::getInstance()->getConnection();
        
        // Create table if not exists
        $sql = "CREATE TABLE IF NOT EXISTS ai_models (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            provider VARCHAR(50) DEFAULT 'openai',
            api_key VARCHAR(255) NOT NULL,
            base_url VARCHAR(255) NOT NULL,
            model_name VARCHAR(100) NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            is_ly_api TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        
        // Check if is_ly_api column exists, if not add it
        $columns = $pdo->query("SHOW COLUMNS FROM ai_models")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('is_ly_api', $columns)) {
            $pdo->exec("ALTER TABLE ai_models ADD COLUMN is_ly_api TINYINT(1) DEFAULT 0");
        }
    }

    public static function getAll() {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM ai_models ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function getActive() {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM ai_models WHERE is_active = 1 ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function find($id) {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM ai_models WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();
        
        $sql = "INSERT INTO ai_models (name, provider, api_key, base_url, model_name, is_active, is_ly_api) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['provider'] ?? 'openai',
            $data['api_key'],
            $data['base_url'],
            $data['model_name'],
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            isset($data['is_ly_api']) ? (int)$data['is_ly_api'] : 0
        ]);
        
        return $pdo->lastInsertId();
    }

    public static function update($id, $data) {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();
        
        $sql = "UPDATE ai_models SET name = ?, provider = ?, api_key = ?, base_url = ?, model_name = ?, is_active = ?, is_ly_api = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['provider'] ?? 'openai',
            $data['api_key'],
            $data['base_url'],
            $data['model_name'],
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            isset($data['is_ly_api']) ? (int)$data['is_ly_api'] : 0,
            $id
        ]);
    }

    public static function delete($id) {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("DELETE FROM ai_models WHERE id = ?");
        $stmt->execute([$id]);
    }
}
