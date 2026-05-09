<?php
// src/Controllers/AdminAiSchemeController.php

require_once __DIR__ . '/../Models/AiScheme.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/ApiConfig.php';

class AdminAiSchemeController {
    
    public static function index() {
        self::checkAdmin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // We need a method to get all schemes with user info
        // AiScheme model needs an update or we use a custom query here
        // For now let's assume AiScheme::getAll exists or add it
        $schemes = AiScheme::getAll($limit, $offset); 
        $total = AiScheme::countAll();
        $totalPages = ceil($total / $limit);
        
        require __DIR__ . '/../../templates/admin/ai_schemes_list.php';
    }
    
    public static function approve($id) {
        self::checkAdmin();
        
        $scheme = AiScheme::find($id);
        if (!$scheme) {
            die("Scheme not found");
        }
        
        if ($scheme['status'] !== 'pending') {
            // Already handled
            header('Location: /admin/api');
            exit;
        }
        
        // Update Scheme Status
        AiScheme::updateStatus($id, 'approved');
        
        // Enable linked ApiConfig
        // We need to find the ApiConfig by scheme_id
        // Since we didn't add findBySchemeId yet, let's add it or use raw query
        // Actually we can do a direct update
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE api_configs SET status = 1 WHERE scheme_id = ?");
        $stmt->execute([$id]);
        
        header('Location: /admin/api');
        exit;
    }
    
    public static function reject($id) {
        self::checkAdmin();
        
        $scheme = AiScheme::find($id);
        if (!$scheme) {
            die("Scheme not found");
        }
        
        if ($scheme['status'] !== 'pending') {
             // Already handled or running
            header('Location: /admin/api');
            exit;
        }
        
        // Refund Points
        if ($scheme['frozen_points'] > 0) {
            User::unfreezePoints($scheme['user_id'], $scheme['frozen_points'], "方案被拒绝退还: {$scheme['name']}");
        }
        
        // Update Scheme Status
        AiScheme::updateStatus($id, 'rejected');
        
        // Delete or Disable ApiConfig
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM api_configs WHERE scheme_id = ?"); // Or just delete it so it doesn't clutter
        $stmt->execute([$id]);
        
        header('Location: /admin/api');
        exit;
    }
    
    private static function checkAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /admin/login');
            exit;
        }
    }
}
