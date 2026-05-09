<?php
// php_backend/src/Models/MediaCategory.php

require_once __DIR__ . '/../Config/Database.php';

class MediaCategory {
    public static function getAll() {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM media_categories ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public static function create($data) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO media_categories (name, slug) VALUES (?, ?)");
        $stmt->execute([
            $data['name'],
            $data['slug'] ?? self::slugify($data['name'])
        ]);
        return $pdo->lastInsertId();
    }
    
    public static function delete($id) {
        $pdo = Database::getInstance()->getConnection();
        // Reset files in this category to default (or NULL)
        $pdo->prepare("UPDATE media_files SET category_id = NULL WHERE category_id = ?")->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM media_categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private static function slugify($text) {
        $text = trim($text);
        if (empty($text)) {
            return 'cat-' . uniqid();
        }
        
        // Replace spaces with dashes
        $text = preg_replace('/\s+/', '-', $text);
        
        // Remove unsafe URL characters but keep Unicode (for Chinese support)
        $text = str_replace(['?', '#', '&', '/', '\\', '%', "'", '"'], '', $text);
        
        return $text;
    }
}
