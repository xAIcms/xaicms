<?php
/**
 * Plugin Name: Spider Logs
 * Description: Track search engine bot and AI crawler visits (Googlebot, GPTBot, etc.)
 * Version: 1.0.0
 * Author: xAIcms
 */

add_action('admin_menu', function () {
    global $uri; $uri = $uri ?? ($_SERVER['REQUEST_URI'] ?? '');
    ?><li class="nav-item"><a class="nav-link py-1 <?php echo (strpos($uri, '/admin/spider-logs') !== false) ? 'active' : ''; ?>" href="/admin/spider-logs">🕷 Spider Logs</a></li><?php
});

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if (strpos($uri, '/admin/spider-logs') === 0) {
    require_once __DIR__ . '/../../src/Models/SpiderLog.php';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 50;
    $logs = SpiderLog::getAll($limit, ($page - 1) * $limit);
    $total = SpiderLog::countAll();
    require __DIR__ . '/src/spider_logs.php';
    exit;
}
