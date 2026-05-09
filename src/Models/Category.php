<?php
// php_backend/src/Models/Category.php

require_once __DIR__ . '/../Config/Database.php';

class Category {
    public static function getAll() {
        $pdo = Database::getInstance()->getConnection();
        // Order by sort_order and id
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getBySlug($slug) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $pdo = Database::getInstance()->getConnection();
        
        if (empty($data['uuid'])) {
            $data['uuid'] = self::generateUuid();
        }
        
        if (empty($data['slug'])) {
            $data['slug'] = self::generateSlug($data['name']);
        }
        
        $sql = "INSERT INTO categories (uuid, parent_id, name, slug, description, sort_order) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['uuid'],
            $data['parent_id'] ?? 0,
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['sort_order'] ?? 0
        ]);
        
        return $pdo->lastInsertId();
    }

    public static function update($id, $data) {
        $pdo = Database::getInstance()->getConnection();
        
        $fields = [];
        $values = [];
        $allowed = ['parent_id', 'name', 'slug', 'description', 'sort_order'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "`$key` = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE categories SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public static function delete($id) {
        $pdo = Database::getInstance()->getConnection();
        
        // Check if has children
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("无法删除：该分类下还有子分类");
        }
        
        // Check if has articles
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("无法删除：该分类下还有文章");
        }

        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Helpers
    private static function generateUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private static function generateSlug($string) {
        $slug = preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($string)));
        $slug = trim($slug, '-');
        if (empty($slug)) $slug = 'cat-' . time();
        return $slug;
    }
}
