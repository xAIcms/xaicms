<?php
/**
 * Plugin Name: Announcements
 * Description: Manage platform announcements and notices from the admin panel
 * Version: 1.0.0
 * Author: xAIcms
 *
 * This plugin adds announcement management UI to the admin panel.
 * The Announcement model stays in core so other features can read announcements.
 */

// Register admin sidebar menu
add_action('admin_menu', function () {
    global $uri;
    $uri = $uri ?? ($_SERVER['REQUEST_URI'] ?? '');
    ?>
    <li class="nav-item">
        <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/announcements') !== false) ? 'active' : ''; ?>" href="/admin/announcements">
            📢 Announcements
        </a>
    </li>
    <?php
});

// Intercept admin routes
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if (strpos($uri, '/admin/announcements') === 0) {
    require_once __DIR__ . '/../../src/Models/Announcement.php';
    require_once __DIR__ . '/src/AdminAnnouncementController.php';

    $parts = explode('/', trim($uri, '/'));
    $action = $parts[2] ?? 'index';
    $id = $parts[3] ?? null;

    if ($action === 'create') {
        AdminAnnouncementController::create();
    } elseif ($action === 'edit' && $id) {
        AdminAnnouncementController::edit($id);
    } elseif ($action === 'delete' && $id) {
        AdminAnnouncementController::delete($id);
    } else {
        AdminAnnouncementController::index();
    }
    exit;
}
