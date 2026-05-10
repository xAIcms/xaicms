<?php
// src/Controllers/UserController.php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/VerificationCode.php';
require_once __DIR__ . '/../Utils/Csrf.php';

class UserController {
    
    // 注册页面
    public static function register() {
        if (isset($_SESSION['user_id'])) {
            header('Location: /user/center');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            $registerType = $_POST['register_type'] ?? 'email';
            $error = null;
            $data = [];
            
            if ($registerType === 'phone') {
                $phone = trim($_POST['phone'] ?? '');
                $code = trim($_POST['code'] ?? '');
                $password = $_POST['phone_password'] ?? ''; // Optional
                
                if (empty($phone) || empty($code)) {
                    $error = "请填写手机号和验证码";
                } elseif (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
                    $error = "手机号格式不正确";
                } elseif (!VerificationCode::verify($phone, $code, 'register')) {
                    $error = "验证码错误或已过期";
                } elseif (User::findByPhone($phone)) {
                    $error = "该手机号已被注册";
                } else {
                    $data = [
                        'phone' => $phone,
                        'name' => 'User_' . substr($phone, -4),
                        'role' => 'user'
                    ];
                    if (!empty($password)) {
                        if (strlen($password) < 6) {
                            $error = "密码长度至少为 6 位";
                        } else {
                            $data['password'] = $password;
                        }
                    }
                }
            } else {
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if (empty($name) || empty($email) || empty($password)) {
                    $error = "请填写所有必填字段";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "邮箱格式不正确";
                } elseif (strlen($password) < 6) {
                    $error = "密码长度至少为 6 位";
                } elseif ($password !== $confirmPassword) {
                    $error = "两次输入的密码不一致";
                } else {
                    $data = [
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'role' => 'user'
                    ];
                }
            }
            
            if (!$error) {
                try {
                    $userId = User::create($data);
                    
                    if ($userId) {
                        do_action('user_registered', $userId, $data);
                        header('Location: /login?registered=1');
                        exit;
                    } else {
                        $error = "注册失败，请稍后重试";
                    }
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
            
            // Pass error and input back to view
            require __DIR__ . '/../../templates/auth/register.php';
            exit;
        }
        
        require __DIR__ . '/../../templates/auth/register.php';
    }
    
    // 登录页面
    public static function login() {
        if (isset($_SESSION['user_id'])) {
            header('Location: /user/center');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = null;
            $account = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $user = null;
            if (filter_var($account, FILTER_VALIDATE_EMAIL)) {
                $user = User::findByEmail($account);
            } else {
                $user = User::findByPhone($account);
            }
            
            if ($user && User::verifyPassword($user, $password)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_avatar'] = $user['avatar'] ?? null;
                User::updateLoginInfo($user['id'], $_SERVER['REMOTE_ADDR']);
                
                header('Location: /user/center');
                exit;
            } else {
                $error = "账号或密码错误";
            }
            
            if ($error) {
                require __DIR__ . '/../../templates/auth/login.php';
                exit;
            }
        }
        
        require __DIR__ . '/../../templates/auth/login.php';
    }

    // 忘记密码
    public static function forgotPassword() {
        if (isset($_SESSION['user_id'])) {
            header('Location: /user/center');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phone = trim($_POST['phone'] ?? '');
            $code = trim($_POST['code'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $error = null;
            
            if (empty($phone) || empty($code) || empty($password)) {
                $error = "请填写所有必填字段";
            } elseif (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
                $error = "手机号格式不正确";
            } elseif (strlen($password) < 6) {
                $error = "密码长度至少为 6 位";
            } elseif ($password !== $confirmPassword) {
                $error = "两次输入的密码不一致";
            } elseif (!VerificationCode::verify($phone, $code, 'forgot_password')) {
                $error = "验证码错误或已过期";
            } else {
                $user = User::findByPhone($phone);
                if (!$user) {
                    $error = "该手机号未注册";
                } else {
                    if (User::updatePassword($user['id'], $password)) {
                        header('Location: /login?reset=1');
                        exit;
                    } else {
                        $error = "重置密码失败，请重试";
                    }
                }
            }
            
            if ($error) {
                require __DIR__ . '/../../templates/auth/forgot_password.php';
                exit;
            }
        }
        
        require __DIR__ . '/../../templates/auth/forgot_password.php';
    }
    
    // 注销
    public static function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
    
    // 用户中心首页
    public static function center() {
        self::requireLogin();
        
        $user = User::findById($_SESSION['user_id']);
        $activities = User::getRecentActivity($_SESSION['user_id']);
        
        // Fetch Announcements and System Updates
        require_once __DIR__ . '/../Models/Announcement.php';
        require_once __DIR__ . '/../Models/SystemUpdate.php';
        
        $announcements = Announcement::getPublished(5);
        $systemUpdates = SystemUpdate::getLatest(5);
        
        require __DIR__ . '/../../templates/user/center.php';
    }
    
    // 个人资料设置
    public static function profile() {
        self::requireLogin();
        
        $user = User::findById($_SESSION['user_id']);
        $success = null;
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            $name = trim($_POST['name'] ?? '');
            // Email updates might require verification, skipping for now or allow direct update
            
            try {
                User::update($user['id'], ['name' => $name]);
                $user = User::findById($user['id']); // Refresh
                $_SESSION['user_name'] = $user['name']; // Update session
                $success = "个人资料已更新";
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        require __DIR__ . '/../../templates/user/profile.php';
    }

    // 绑定/更换手机号 API
    public static function bindPhone() {
        self::requireLogin();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $phone = $input['phone'] ?? '';
        $code = $input['code'] ?? '';
        $csrfToken = $input['csrf_token'] ?? '';
        
        // Verify CSRF
        if (!Csrf::check($csrfToken)) {
            echo json_encode(['success' => false, 'message' => '无效的 CSRF 令牌']);
            exit;
        }
        
        if (empty($phone) || empty($code)) {
            echo json_encode(['success' => false, 'message' => '请填写手机号和验证码']);
            exit;
        }
        
        // Check phone format
        if (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
            echo json_encode(['success' => false, 'message' => '手机号格式不正确']);
            exit;
        }
        
        // Verify SMS Code
        require_once __DIR__ . '/../Models/VerificationCode.php';
        if (!VerificationCode::verify($phone, $code, 'bind')) {
            echo json_encode(['success' => false, 'message' => '验证码错误或已过期']);
            exit;
        }
        
        // Update User
        try {
            User::update($_SESSION['user_id'], ['phone' => $phone]);
            echo json_encode(['success' => true, 'message' => '绑定成功']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    // 安全设置（修改密码）
    public static function security() {
        self::requireLogin();
        
        $user = User::findById($_SESSION['user_id']);
        $success = null;
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (!User::verifyPassword($user, $currentPassword)) {
                $error = "当前密码错误";
            } elseif (strlen($newPassword) < 6) {
                $error = "新密码长度至少为 6 位";
            } elseif ($newPassword !== $confirmPassword) {
                $error = "两次输入的新密码不一致";
            } else {
                if (User::updatePassword($user['id'], $newPassword)) {
                    $success = "密码已修改";
                } else {
                    $error = "修改失败，请稍后重试";
                }
            }
        }
        
        require __DIR__ . '/../../templates/user/security.php';
    }
    
    // 积分历史
    public static function pointHistory() {
        self::requireLogin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $user = User::findById($_SESSION['user_id']);
        $logs = User::getPointHistory($_SESSION['user_id'], $perPage, $offset);
        $totalLogs = User::countPointHistory($_SESSION['user_id']);
        $totalPages = ceil($totalLogs / $perPage);
        $currentPage = $page;
        
        require __DIR__ . '/../../templates/user/point_history.php';
    }

    // 路由守卫
    private static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}
