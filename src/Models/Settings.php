<?php
// php_backend/src/Models/Settings.php

require_once __DIR__ . '/../Config/Database.php';

class Settings {
    public static function getAll() {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = $row['value'];
        }
        
        // Default settings to prevent undefined array key warnings
        $defaults = [
            'siteName' => 'GeoPower AI',
            'siteDescription' => 'Enterprise Global Content Management System',
            'siteKeywords' => 'CMS, AI, SEO',
            'siteUrl' => 'http://localhost',
            'language' => 'zh-CN',
            'geoRegion' => 'CN',
            'geoPlacename' => '',
            'geoPosition' => '',
            'footerText' => '&copy; ' . date('Y') . ' GeoPower AI. All rights reserved.',
            'homeTitle' => '',
            'homeDescription' => '',
        ];
        
        $settings = array_merge($defaults, $settings);
        
        // Convert boolean-like strings to booleans
        foreach ($settings as $k => $v) {
            if ($v === '1' || $v === 'true') $settings[$k] = true;
            if ($v === '0' || $v === 'false') $settings[$k] = false;

            // Dynamic text replacement for rebranding (Blog -> News)
            // Apply to specific display fields to avoid breaking configs/keys
            if (is_string($v) && in_array($k, ['siteName', 'siteDescription', 'siteKeywords', 'footerText', 'homeTitle', 'homeDescription', 'pageTitle'])) {
                $settings[$k] = str_replace(['博客', 'Blog'], ['新闻', 'News'], $v);
            }
        }
        
        return $settings;
    }

    public static function get($key, $default = null) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        
        if ($value !== false) {
             // Apply same replacement logic
             if (in_array($key, ['siteName', 'siteDescription', 'siteKeywords', 'footerText', 'homeTitle', 'homeDescription', 'pageTitle'])) {
                 $value = str_replace(['博客', 'Blog'], ['新闻', 'News'], $value);
             }
             return $value;
        }
        return $default;
    }

    public static function update($key, $value) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");
        return $stmt->execute([$key, $value, $value]);
    }

    public static function updateMany($data) {
        $pdo = Database::getInstance()->getConnection();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");
            foreach ($data as $key => $value) {
                // Handle boolean conversion for storage
                if (is_bool($value)) $value = $value ? '1' : '0';
                $stmt->execute([$key, $value, $value]);
            }
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
