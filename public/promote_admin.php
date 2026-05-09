<?php
// public/promote_admin.php
// 临时脚本：将指定邮箱的用户提升为管理员

header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/../src/Config/Database.php';

// 安全检查：仅允许本地或带 Key 访问
$isLocal = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1');
if (!$isLocal && (!isset($_GET['key']) || $_GET['key'] !== 'admin2025')) {
    die("Access Denied. Use ?key=admin2025");
}

$email = $_GET['email'] ?? '';

if (empty($email)) {
    die("Please provide an email address via ?email=user@example.com");
}

try {
    $db = Database::getInstance()->getConnection();
    
    // 检查用户是否存在
    $stmt = $db->prepare("SELECT id, name, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die("User not found: " . htmlspecialchars($email));
    }
    
    if ($user['role'] === 'admin') {
        die("User '{$user['name']}' is already an admin.");
    }
    
    // 更新为管理员
    $update = $db->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
    $update->execute([$user['id']]);
    
    echo "Success! User '{$user['name']}' ({$email}) has been promoted to Administrator.\n";
    echo "Please logout and login again to apply changes.";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
