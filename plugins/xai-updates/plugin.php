<?php
/**
 * Plugin Name: System Updates
 * Description: Changelog and update history tracking
 * Version: 1.0.0
 * Author: xAIcms
 */

add_action('admin_menu', function () {
    global $uri; $uri = $uri ?? ($_SERVER['REQUEST_URI'] ?? '');
    ?><li class="nav-item"><a class="nav-link py-1 <?php echo (strpos($uri, '/admin/system-updates') !== false) ? 'active' : ''; ?>" href="/admin/system-updates">📋 Updates</a></li><?php
});

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if (strpos($uri, '/admin/system-updates') === 0) {
    require_once __DIR__ . '/../../src/Models/SystemUpdate.php';
    require_once __DIR__ . '/src/AdminSystemUpdateController.php';
    $parts = explode('/', trim($uri, '/'));
    $action = $parts[2] ?? 'index';
    $id = $parts[3] ?? null;
    if ($action === 'create') AdminSystemUpdateController::create();
    elseif ($action === 'edit' && $id) AdminSystemUpdateController::edit($id);
    elseif ($action === 'delete' && $id) AdminSystemUpdateController::delete($id);
    else AdminSystemUpdateController::index();
    exit;
}
