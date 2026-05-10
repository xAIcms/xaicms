<?php

class Announcement {
    public static function getAll($limit = 10, $offset = 0) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM announcements ORDER BY published_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPublished($limit = 5) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM announcements WHERE status = 1 ORDER BY published_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll() {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM announcements WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO announcements (title, type, content, status, published_at) VALUES (:title, :type, :content, :status, :published_at)");
        return $stmt->execute([
            ':title' => $data['title'],
            ':type' => $data['type'],
            ':content' => $data['content'],
            ':status' => $data['status'],
            ':published_at' => $data['published_at'] ?: date('Y-m-d H:i:s')
        ]);
    }

    public static function update($id, $data) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE announcements SET title = :title, type = :type, content = :content, status = :status, published_at = :published_at WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':type' => $data['type'],
            ':content' => $data['content'],
            ':status' => $data['status'],
            ':published_at' => $data['published_at']
        ]);
    }

    public static function delete($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM announcements WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function getTypeLabel($type) {
        $labels = [
            'activity' => '活动',
            'announcement' => '公告',
            'feature' => '新功能',
            'important' => '重要'
        ];
        return $labels[$type] ?? '通知';
    }

    public static function getTypeColor($type) {
        $colors = [
            'activity' => 'bg-red-100 text-red-600',
            'announcement' => 'bg-blue-100 text-blue-600',
            'feature' => 'bg-green-100 text-green-600',
            'important' => 'bg-purple-100 text-purple-600'
        ];
        return $colors[$type] ?? 'bg-gray-100 text-gray-600';
    }
}
