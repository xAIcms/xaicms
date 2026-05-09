<?php
require_once __DIR__ . '/../Models/Announcement.php';

class AdminAnnouncementController {
    public static function index() {
        $page = $_GET['page'] ?? 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        $announcements = Announcement::getAll($perPage, $offset);
        $total = Announcement::countAll();
        $totalPages = ceil($total / $perPage);
        
        require __DIR__ . '/../../templates/admin/announcements_list.php';
    }

    public static function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'type' => $_POST['type'],
                'content' => $_POST['content'],
                'status' => isset($_POST['status']) ? 1 : 0,
                'published_at' => $_POST['published_at']
            ];
            
            if (Announcement::create($data)) {
                header('Location: /admin/announcements');
                exit;
            } else {
                $error = "创建失败";
            }
        }
        
        require __DIR__ . '/../../templates/admin/announcement_form.php';
    }

    public static function edit($id) {
        $announcement = Announcement::findById($id);
        if (!$announcement) {
            header('Location: /admin/announcements');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'type' => $_POST['type'],
                'content' => $_POST['content'],
                'status' => isset($_POST['status']) ? 1 : 0,
                'published_at' => $_POST['published_at']
            ];
            
            if (Announcement::update($id, $data)) {
                header('Location: /admin/announcements');
                exit;
            } else {
                $error = "更新失败";
            }
        }
        
        require __DIR__ . '/../../templates/admin/announcement_form.php';
    }

    public static function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Announcement::delete($id);
        }
        header('Location: /admin/announcements');
        exit;
    }
}
