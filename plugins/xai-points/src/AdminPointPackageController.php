<?php
// src/Controllers/AdminPointPackageController.php

require_once __DIR__ . '/../Models/PointPackage.php';
require_once __DIR__ . '/../Utils/Csrf.php';

class AdminPointPackageController {
    
    private static function checkAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            exit("Access Denied");
        }
    }

    public static function index() {
        self::checkAdmin();
        
        $packages = PointPackage::getAll(false); // Show all, including inactive
        
        require __DIR__ . '/../../templates/admin/point_packages_list.php';
    }

    public static function create() {
        self::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'points' => $_POST['points'] ?? 0,
                'bonus_percent' => $_POST['bonus_percent'] ?? 0,
                'is_active' => isset($_POST['is_active'])
            ];
            
            if (PointPackage::create($data)) {
                header('Location: /admin/point-packages');
                exit;
            } else {
                $error = "创建失败";
            }
        }
        
        require __DIR__ . '/../../templates/admin/point_package_form.php';
    }

    public static function edit($id) {
        self::checkAdmin();
        
        $package = PointPackage::findById($id);
        if (!$package) {
            header('Location: /admin/point-packages');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'points' => $_POST['points'] ?? 0,
                'bonus_percent' => $_POST['bonus_percent'] ?? 0,
                'is_active' => isset($_POST['is_active'])
            ];
            
            if (PointPackage::update($id, $data)) {
                header('Location: /admin/point-packages');
                exit;
            } else {
                $error = "更新失败";
            }
        }
        
        require __DIR__ . '/../../templates/admin/point_package_form.php';
    }

    public static function delete($id) {
        self::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            PointPackage::delete($id);
        }
        
        header('Location: /admin/point-packages');
        exit;
    }
}
