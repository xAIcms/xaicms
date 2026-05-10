<?php
/**
 * Plugin Name: Points & Recharge
 * Description: User credit system with recharge packages and order management
 * Version: 1.0.0
 * Author: xAIcms
 *
 * Tables: point_packages, recharge_orders (created by core SQL)
 * Models: PointPackage, RechargeOrder (in core for cross-feature access)
 */

// ── Admin sidebar menu ──
add_action('admin_menu', function () {
    global $uri;
    $uri = $uri ?? ($_SERVER['REQUEST_URI'] ?? '');
    $active = strpos($uri, '/admin/point-packages') !== false || strpos($uri, '/admin/recharge-orders') !== false;
    ?>
    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-muted text-uppercase small fw-bold">
        <span>Finance</span>
    </h6>
    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/point-packages') !== false) ? 'active' : ''; ?>" href="/admin/point-packages">
                💰 Point Packages
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/recharge-orders') !== false) ? 'active' : ''; ?>" href="/admin/recharge-orders">
                📋 Recharge Orders
            </a>
        </li>
    </ul>
    <?php
});

// ── Route interception ──
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

// Admin: point packages
if (strpos($uri, '/admin/point-packages') === 0) {
    require_once __DIR__ . '/../../src/Models/PointPackage.php';
    require_once __DIR__ . '/src/AdminPointPackageController.php';
    $action = $_GET['id'] ?? '';
    if ($uri === '/admin/point-packages/create') {
        AdminPointPackageController::create();
    } elseif ($uri === '/admin/point-packages/edit' && $action) {
        AdminPointPackageController::edit($action);
    } elseif ($uri === '/admin/point-packages/delete' && $action) {
        AdminPointPackageController::delete($action);
    } else {
        AdminPointPackageController::index();
    }
    exit;
}

// Admin: recharge orders
if (strpos($uri, '/admin/recharge-orders') === 0) {
    require_once __DIR__ . '/../../src/Models/RechargeOrder.php';
    require_once __DIR__ . '/src/AdminRechargeController.php';
    if ($uri === '/admin/recharge-orders/approve') {
        AdminRechargeController::approve();
    } elseif ($uri === '/admin/recharge-orders/reject') {
        AdminRechargeController::reject();
    } elseif ($uri === '/admin/recharge-orders/update-remark') {
        AdminRechargeController::updateRemark();
    } else {
        AdminRechargeController::index();
    }
    exit;
}

// User: recharge
if (strpos($uri, '/user/recharge') === 0) {
    require_once __DIR__ . '/../../src/Models/PointPackage.php';
    require_once __DIR__ . '/../../src/Models/RechargeOrder.php';
    require_once __DIR__ . '/src/UserRechargeController.php';
    if ($uri === '/user/recharge/create') {
        UserRechargeController::create();
    } else {
        UserRechargeController::index();
    }
    exit;
}
