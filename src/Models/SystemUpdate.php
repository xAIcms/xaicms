<?php

class SystemUpdate {
    public static function getAll($limit = 10, $offset = 0) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM system_updates ORDER BY release_date DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLatest($limit = 5) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM system_updates ORDER BY release_date DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll() {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT COUNT(*) FROM system_updates")->fetchColumn();
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM system_updates WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO system_updates (version, content, release_date) VALUES (:version, :content, :release_date)");
        return $stmt->execute([
            ':version' => $data['version'],
            ':content' => $data['content'],
            ':release_date' => $data['release_date'] ?: date('Y-m-d')
        ]);
    }

    public static function update($id, $data) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE system_updates SET version = :version, content = :content, release_date = :release_date WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':version' => $data['version'],
            ':content' => $data['content'],
            ':release_date' => $data['release_date']
        ]);
    }

    public static function delete($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM system_updates WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
