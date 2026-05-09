<?php
require_once __DIR__ . '/../Models/SystemUpdate.php';

class AdminSystemUpdateController {
    public static function index() {
        $page = $_GET['page'] ?? 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        $updates = SystemUpdate::getAll($perPage, $offset);
        $total = SystemUpdate::countAll();
        $totalPages = ceil($total / $perPage);
        
        require __DIR__ . '/../../templates/admin/system_updates_list.php';
    }

    public static function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'version' => $_POST['version'],
                'content' => $_POST['content'],
                'release_date' => $_POST['release_date']
            ];
            
            if (SystemUpdate::create($data)) {
                header('Location: /admin/system-updates');
                exit;
            } else {
                $error = "创建失败";
            }
        }
        
        require __DIR__ . '/../../templates/admin/system_update_form.php';
    }

    public static function edit($id) {
        $update = SystemUpdate::findById($id);
        if (!$update) {
            header('Location: /admin/system-updates');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'version' => $_POST['version'],
                'content' => $_POST['content'],
                'release_date' => $_POST['release_date']
            ];
            
            if (SystemUpdate::update($id, $data)) {
                header('Location: /admin/system-updates');
                exit;
            } else {
                $error = "更新失败";
            }
        }
        
        require __DIR__ . '/../../templates/admin/system_update_form.php';
    }

    public static function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            SystemUpdate::delete($id);
        }
        header('Location: /admin/system-updates');
        exit;
    }
}
