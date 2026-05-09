<?php
// src/Models/VerificationCode.php

require_once __DIR__ . '/../Config/Database.php';

class VerificationCode {
    public static function create($phone, $code, $type) {
        $db = Database::getInstance()->getConnection();
        
        // Invalidate previous codes
        $stmt = $db->prepare("UPDATE verification_codes SET is_used = 1 WHERE phone = ? AND type = ? AND is_used = 0");
        $stmt->execute([$phone, $type]);
        
        // Create new code (valid for 5 minutes)
        $expiresAt = date('Y-m-d H:i:s', time() + 300);
        $stmt = $db->prepare("INSERT INTO verification_codes (phone, code, type, expires_at) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$phone, $code, $type, $expiresAt]);
    }
    
    public static function verify($phone, $code, $type) {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM verification_codes WHERE phone = ? AND code = ? AND type = ? AND is_used = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$phone, $code, $type]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($record) {
            // Mark as used
            $update = $db->prepare("UPDATE verification_codes SET is_used = 1 WHERE id = ?");
            $update->execute([$record['id']]);
            return true;
        }
        
        return false;
    }
    
    public static function checkRateLimit($phone) {
        $db = Database::getInstance()->getConnection();
        
        // Check if sent in last 60 seconds
        $stmt = $db->prepare("SELECT COUNT(*) FROM verification_codes WHERE phone = ? AND created_at > DATE_SUB(NOW(), INTERVAL 60 SECOND)");
        $stmt->execute([$phone]);
        return $stmt->fetchColumn() > 0;
    }
}
