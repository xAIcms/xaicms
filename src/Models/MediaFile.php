<?php
// php_backend/src/Models/MediaFile.php

require_once __DIR__ . '/../Config/Database.php';

class MediaFile {
    public static function getAll($limit = 50, $offset = 0, $categoryId = null) {
        $pdo = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM media_files";
        $params = [];
        
        if ($categoryId) {
            $sql .= " WHERE category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $pdo->prepare($sql);
        // Bind parameters carefully
        if ($categoryId) {
            $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countAll($categoryId = null) {
        $pdo = Database::getInstance()->getConnection();
        if ($categoryId) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM media_files WHERE category_id = ?");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $pdo->query("SELECT COUNT(*) FROM media_files");
        }
        return $stmt->fetchColumn();
    }

    public static function create($data) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO media_files (category_id, filename, original_name, path, mime_type, size) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['category_id'] ?? null,
            $data['filename'],
            $data['original_name'],
            $data['path'],
            $data['mime_type'],
            $data['size']
        ]);
        return $pdo->lastInsertId();
    }
    
    public static function delete($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT path FROM media_files WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetch();
        
        if ($file) {
            // Delete physical file
            $filepath = __DIR__ . '/../../public' . $file['path'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // Delete DB record
            $delStmt = $pdo->prepare("DELETE FROM media_files WHERE id = ?");
            return $delStmt->execute([$id]);
        }
        return false;
    }
    
    public static function handleUpload($file, $categoryId = null) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowedExtensions)) {
            return false;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/media/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $relativePath = '/uploads/media/' . $filename;
            
            return self::create([
                'category_id' => $categoryId ?: null,
                'filename' => $filename,
                'original_name' => $file['name'],
                'path' => $relativePath,
                'mime_type' => $file['type'],
                'size' => $file['size']
            ]);
        }
        return false;
    }
}
