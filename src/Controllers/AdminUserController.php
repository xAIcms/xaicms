<?php
// src/Controllers/AdminUserController.php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Utils/Csrf.php';

class AdminUserController {
    
    // Check if current user is admin
    private static function checkAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            echo "Access Denied: You must be an administrator.";
            exit;
        }
    }

    // 用户列表
    public static function index() {
        self::checkAdmin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $users = User::getAll($limit, $offset, $search);
        $total = User::countAll($search);
        $totalPages = ceil($total / $limit);
        
        require __DIR__ . '/../../templates/admin/users_list.php';
    }
    
    // 编辑用户
    public static function edit($id) {
        self::checkAdmin();
        
        $user = User::findById($id);
        if (!$user) {
            header('Location: /admin/users');
            exit;
        }
        
        require __DIR__ . '/../../templates/admin/user_form.php';
    }
    
    // 更新用户
    public static function update($id) {
        self::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'phone' => !empty($_POST['phone']) ? $_POST['phone'] : null,
                'email' => $_POST['email'] ?? '',
                'role' => $_POST['role'] ?? 'user',
            ];
            
            // Password update (optional)
            if (!empty($_POST['password'])) {
                User::updatePassword($id, $_POST['password']);
            }
            
            // Points update
            if (isset($_POST['points'])) {
                User::updatePoints($id, (int)$_POST['points']);
            }
            
            try {
                User::update($id, $data);
                User::updateRole($id, $data['role']); // Explicitly update role
                header('Location: /admin/users');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                $user = User::findById($id); // Reload user for form
                require __DIR__ . '/../../templates/admin/user_form.php';
                exit;
            }
        }
    }
    
    // 删除用户
    public static function delete($id) {
        self::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            // Prevent deleting self
            if ($id == $_SESSION['user_id']) {
                die("Cannot delete yourself.");
            }
            
            User::delete($id);
            header('Location: /admin/users');
            exit;
        }
    }
}
