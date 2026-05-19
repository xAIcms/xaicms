<?php
require_once __DIR__ . '/../Models/SystemUpdate.php';

class AdminSystemUpdateController {
    private static function checkAdmin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION["user_id"]) || ($_SESSION["user_role"] ?? "") !== "admin") {
            http_response_code(403);
            die("Access denied");
        }
    }

    public static function index() {
        self::checkAdmin();
        $page = $_GET['page'] ?? 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $updates = SystemUpdate::getAll($perPage, $offset);
        $total = SystemUpdate::countAll();
        $totalPages = ceil($total / $perPage);
        require __DIR__ . '/../../templates/admin/system_updates_list.php';
    }

    public static function create() {
        self::checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = ['version' => $_POST['version'], 'content' => $_POST['content'], 'release_date' => $_POST['release_date']];
            if (SystemUpdate::create($data)) { header('Location: /admin/system-updates'); exit; }
            else { $error = "创建失败"; }
        }
        require __DIR__ . '/../../templates/admin/system_update_form.php';
    }

    public static function edit($id = null) {
        self::checkAdmin();
        $id = $id ?? ($_GET["id"] ?? null);
        $update = SystemUpdate::findById($id);
        if (!$update) { header('Location: /admin/system-updates'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = ['version' => $_POST['version'], 'content' => $_POST['content'], 'release_date' => $_POST['release_date']];
            if (SystemUpdate::update($id, $data)) { header('Location: /admin/system-updates'); exit; }
            else { $error = "更新失败"; }
        }
        require __DIR__ . '/../../templates/admin/system_update_form.php';
    }

    public static function delete($id = null) {
        self::checkAdmin();
        $id = $id ?? ($_GET["id"] ?? $_POST["id"] ?? null);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { SystemUpdate::delete($id); }
        header('Location: /admin/system-updates');
        exit;
    }
}
