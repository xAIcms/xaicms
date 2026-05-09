<?php
// php_backend/src/Models/Tag.php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Utils/SlugGenerator.php';

class Tag {
    public static function getAll($limit = null, $offset = 0) {
        $pdo = Database::getInstance()->getConnection();
        if ($limit) {
            $stmt = $pdo->prepare("SELECT * FROM tags ORDER BY id DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = $pdo->query("SELECT * FROM tags ORDER BY id DESC");
            return $stmt->fetchAll();
        }
    }

    public static function countAll() {
        $pdo = Database::getInstance()->getConnection();
        return $pdo->query("SELECT COUNT(*) FROM tags")->fetchColumn();
    }

    public static function getPopular($limit = 15) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM tags ORDER BY article_count DESC, id DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM tags WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getBySlug($slug) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM tags WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $pdo = Database::getInstance()->getConnection();
        
        if (empty($data['uuid'])) {
            $data['uuid'] = self::generateUuid();
        }
        
        // Ensure slug is valid and unique
        $baseSlug = !empty($data['slug']) ? $data['slug'] : $data['name'];
        $data['slug'] = self::generateSlug($baseSlug);
        
        $sql = "INSERT INTO tags (uuid, name, slug) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['uuid'],
            $data['name'],
            $data['slug']
        ]);
        
        return $pdo->lastInsertId();
    }

    public static function update($id, $data) {
        $pdo = Database::getInstance()->getConnection();
        
        $fields = [];
        $values = [];
        $allowed = ['name', 'slug'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "`$key` = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE tags SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public static function delete($id) {
        $pdo = Database::getInstance()->getConnection();
        
        // Remove relations first
        $stmt = $pdo->prepare("DELETE FROM article_tags WHERE tag_id = ?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM tags WHERE id = ?");
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
        $slug = SlugGenerator::generate($string);
        
        if (empty($slug)) {
            $slug = 'tag-' . time() . '-' . mt_rand(1000, 9999);
        }
        
        // Ensure slug is unique
        $pdo = Database::getInstance()->getConnection();
        $originalSlug = $slug;
        $count = 1;
        
        while (true) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM tags WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetchColumn() == 0) {
                break;
            }
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        return $slug;
    }
}
