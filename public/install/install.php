<?php
/**
 * xAI CMS Installer Backend
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable direct output of errors to keep JSON clean

function json_response($data) {
    echo json_encode($data);
    exit;
}

function json_error($msg) {
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'check_env':
            $data = [
                'php_version' => [
                    'current' => PHP_VERSION,
                    'ok' => version_compare(PHP_VERSION, '8.0.0', '>=')
                ],
                'extensions' => [
                    'pdo_mysql' => extension_loaded('pdo_mysql'),
                    'mbstring' => extension_loaded('mbstring'),
                    'json' => extension_loaded('json'),
                    'curl' => extension_loaded('curl')
                ],
                'write_permission' => is_writable(__DIR__ . '/../../')
            ];
            json_response($data);
            break;

        case 'test_db':
            $host = $_POST['db_host'] ?? '127.0.0.1';
            $port = $_POST['db_port'] ?? '3306';
            $name = $_POST['db_name'] ?? '';
            $user = $_POST['db_user'] ?? '';
            $pass = $_POST['db_pass'] ?? '';

            try {
                $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                
                // Check if database exists, if not try to create
                // Note: In shared hosting (like BT Panel), user might not have CREATE DATABASE permission.
                // So we try to connect to the specific database directly first.
                try {
                    $pdo->exec("USE `$name`");
                } catch (PDOException $e) {
                    // If USE failed, maybe it doesn't exist. Try to create it.
                    try {
                        $pdo->exec("CREATE DATABASE `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    } catch (PDOException $createErr) {
                         // If create failed, it might be permission issue.
                         // But if the error is Access Denied, it will be caught by outer catch block initially.
                         // Let's just throw the original error if we can't USE it.
                         throw new Exception("无法选择或创建数据库 '$name'。请确保数据库已存在，或用户有创建权限。");
                    }
                }
                
                json_response(['ok' => true]);
            } catch (PDOException $e) {
                json_error($e->getMessage());
            } catch (Exception $e) {
                json_error($e->getMessage());
            }
            break;

        case 'install':
            $host = $_POST['db_host'] ?? '';
            $port = $_POST['db_port'] ?? '3306';
            $name = $_POST['db_name'] ?? '';
            $user = $_POST['db_user'] ?? '';
            $pass = $_POST['db_pass'] ?? '';
            
            $admin_email = $_POST['admin_email'] ?? '';
            $admin_pass = $_POST['admin_pass'] ?? '';
            $site_language = $_POST['site_language'] ?? 'en-US';

            $logs = [];

            // 1. Connect
            $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
            try {
                $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $logs[] = "数据库连接成功";
            } catch (PDOException $e) {
                json_error("数据库连接失败: " . $e->getMessage());
            }

            // 2. Read SQL
            $sqlFile = __DIR__ . '/../../sql/database.sql';
            if (!file_exists($sqlFile)) {
                json_error("找不到 SQL 文件: $sqlFile");
            }
            $sqlContent = file_get_contents($sqlFile);
            $logs[] = "读取 SQL 文件成功";

            // 3. Execute SQL (Simple split by ;)
            // Note: This is a simple parser. For complex SQL with triggers/procedures, a better parser is needed.
            // Our SQL is simple enough.
            $statements = explode(';', $sqlContent);
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt)) continue;
                try {
                    $pdo->exec($stmt);
                } catch (PDOException $e) {
                    // Fail hard on SQL errors to ensure database integrity
                    json_error("SQL 执行失败: " . $e->getMessage() . " \nSQL: " . substr($stmt, 0, 100) . "...");
                }
            }
            $logs[] = "数据库表结构导入完成";

            // 4. Create Admin
            if ($admin_email && $admin_pass) {
                $hash = password_hash($admin_pass, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, name, role) VALUES (?, ?, 'Administrator', 'admin') ON DUPLICATE KEY UPDATE password_hash = ?");
                $stmt->execute([$admin_email, $hash, $hash]);
                $logs[] = "管理员账号创建成功";
            }

            // Save site language to settings
            $pdo->exec("UPDATE settings SET `value` = " . $pdo->quote($site_language) . " WHERE `key` = 'language'");
            $logs[] = "站点语言设置: $site_language";

            // 5. Write Config
            $configContent = "<?php\n\nreturn [\n    'db' => [\n        'host' => '$host',\n        'port' => '$port',\n        'database' => '$name',\n        'username' => '$user',\n        'password' => '$pass',\n        'charset' => 'utf8mb4',\n    ],\n    'app_url' => 'http://' . \$_SERVER['HTTP_HOST'],
    'language' => '$site_language',\n];\n";
            
            $configFile = __DIR__ . '/../../config.php';
            if (file_put_contents($configFile, $configContent)) {
                $logs[] = "配置文件写入成功: config.php";
            } else {
                json_error("配置文件写入失败，请检查目录权限");
            }

            // 6. Create lock file
            file_put_contents(__DIR__ . '/install.lock', date('Y-m-d H:i:s'));
            
            echo json_encode(['ok' => true, 'logs' => $logs]);
            break;

        default:
            json_error('Invalid action');
    }

} catch (Exception $e) {
    json_error($e->getMessage());
}
