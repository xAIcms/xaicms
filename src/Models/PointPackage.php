<?php
// src/Models/PointPackage.php

require_once __DIR__ . '/../Config/Database.php';

class PointPackage {
    public static function ensureTable() {
        $db = Database::getInstance()->getConnection();
        $sql = "CREATE TABLE IF NOT EXISTS `point_packages` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `price` DECIMAL(10, 2) NOT NULL,
            `points` INT UNSIGNED NOT NULL,
            `bonus_percent` DECIMAL(5, 2) NOT NULL DEFAULT 0,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->exec($sql);
    }

    public static function getAll($activeOnly = true) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM point_packages";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY price ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function findById($id) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM point_packages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO point_packages (name, price, points, bonus_percent, is_active) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['price'],
            $data['points'],
            $data['bonus_percent'] ?? 0,
            isset($data['is_active']) ? 1 : 0
        ]);
    }

    public static function update($id, $data) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE point_packages SET name = ?, price = ?, points = ?, bonus_percent = ?, is_active = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['price'],
            $data['points'],
            $data['bonus_percent'] ?? 0,
            isset($data['is_active']) ? 1 : 0,
            $id
        ]);
    }

    public static function delete($id) {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM point_packages WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
