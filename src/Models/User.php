<?php
// src/Models/User.php

require_once __DIR__ . '/../Config/Database.php';

class User {
    // 查找用户
    public static function findByEmail($email) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByPhone($phone) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE phone = ? LIMIT 1");
        $stmt->execute([$phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 获取所有用户（分页+搜索）
    public static function getAll($limit = 20, $offset = 0, $search = '') {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM users";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " WHERE name LIKE ? OR email LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        // PDO limit/offset needs integer
        // But bindValue is safer for int
        $stmt = $db->prepare($sql);
        
        $i = 1;
        if (!empty($search)) {
            $stmt->bindValue($i++, "%$search%");
            $stmt->bindValue($i++, "%$search%");
        }
        $stmt->bindValue($i++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 统计所有用户
    public static function countAll($search = '') {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM users";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " WHERE name LIKE ? OR email LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    // 更新积分 (管理员直接修改)
    public static function updatePoints($id, $points, $reason = '管理员修改') {
        $db = Database::getInstance()->getConnection();
        
        // Get current points to log difference
        $stmt = $db->prepare("SELECT points FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        
        $diff = $points - $current;
        
        $stmt = $db->prepare("UPDATE users SET points = ?, updated_at = NOW() WHERE id = ?");
        $result = $stmt->execute([$points, $id]);
        
        if ($result && $diff != 0) {
            $action = 'admin_update_points';
            self::logActivity($id, $action, "$reason: " . ($diff > 0 ? '+' : '') . $diff, $_SERVER['REMOTE_ADDR'] ?? 'Unknown');
        }
        
        return $result;
    }

    // 更新角色
    public static function updateRole($id, $role) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    // 删除用户
    public static function delete($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 验证密码
    public static function verifyPassword($user, $password) {
        if (!$user) return false;
        return password_verify($password, $user['password_hash']);
    }

    // 更新登录信息
    public static function updateLoginInfo($id, $ip) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET last_login_at = NOW(), login_ip = ? WHERE id = ?");
        $stmt->execute([$ip, $id]);
        
        // 记录登录日志
        self::logActivity($id, 'login', '用户登录', $ip);
    }

    // 获取用户积分信息
    public static function getPoints($id) {
        $user = self::findById($id);
        return [
            'points' => $user['points'] ?? 0,
            'frozen_points' => $user['frozen_points'] ?? 0
        ];
    }

    // 冻结积分 (提交方案时)
    public static function freezePoints($id, $amount, $reason = '冻结积分') {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        try {
            // 检查余额
            $stmt = $db->prepare("SELECT points FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$id]);
            $current = $stmt->fetchColumn();
            
            if ($current < $amount) {
                $db->rollBack();
                return false;
            }
            
            $stmt = $db->prepare("UPDATE users SET points = points - ?, frozen_points = frozen_points + ? WHERE id = ?");
            $result = $stmt->execute([$amount, $amount, $id]);
            
            if ($result) {
                self::logActivity($id, 'freeze_points', "$reason: $amount", $_SERVER['REMOTE_ADDR'] ?? 'Unknown');
            }
            
            $db->commit();
            return $result;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    // 解冻积分 (方案拒绝或取消时返还)
    public static function unfreezePoints($id, $amount, $reason = '解冻积分') {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET points = points + ?, frozen_points = frozen_points - ? WHERE id = ?");
        $result = $stmt->execute([$amount, $amount, $id]);
        
        if ($result) {
            self::logActivity($id, 'unfreeze_points', "$reason: $amount", $_SERVER['REMOTE_ADDR'] ?? 'Unknown');
        }
        
        return $result;
    }

    // 消耗冻结积分 (生成文章后)
    public static function consumeFrozenPoints($id, $amount, $reason = '消费冻结积分') {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET frozen_points = frozen_points - ? WHERE id = ?");
        $result = $stmt->execute([$amount, $id]);
        
        if ($result) {
            self::logActivity($id, 'consume_points', "$reason: $amount", $_SERVER['REMOTE_ADDR'] ?? 'Unknown');
        }
        
        return $result;
    }

    // 增加积分 (充值)
    public static function addPoints($id, $amount, $reason = '增加积分') {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET points = points + ? WHERE id = ?");
        $result = $stmt->execute([$amount, $id]);
        
        if ($result) {
            self::logActivity($id, 'add_points', "$reason: $amount", $_SERVER['REMOTE_ADDR'] ?? 'Unknown');
        }
        
        return $result;
    }

    // 创建用户（注册）
    public static function create($data) {
        $db = Database::getInstance()->getConnection();
        
        // Adjust for phone registration
        $phone = $data['phone'] ?? null;
        $email = $data['email'] ?? '';
        
        // If email is empty but phone exists, generate a placeholder
        if (empty($email) && !empty($phone)) {
            $email = $phone . '@mobile.user';
        }

        // 检查邮箱是否存在
        if (self::findByEmail($email)) {
            throw new Exception("该邮箱已被注册");
        }

        $sql = "INSERT INTO users (name, email, password_hash, role, phone, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $db->prepare($sql);
        
        $role = $data['role'] ?? 'user';
        $passwordHash = !empty($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : '';
        
        if ($stmt->execute([$data['name'], $email, $passwordHash, $role, $phone])) {
            $userId = $db->lastInsertId();
            self::logActivity($userId, 'register', '用户注册', $_SERVER['REMOTE_ADDR'] ?? 'Unknown');
            return $userId;
        }
        
        return false;
    }

    // 更新用户信息
    public static function update($id, $data) {
        $db = Database::getInstance()->getConnection();
        
        $fields = [];
        $values = [];
        
        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $values[] = $data['name'];
        }
        
        if (isset($data['email'])) {
            // Check if email is taken by another user
            $existing = self::findByEmail($data['email']);
            if ($existing && $existing['id'] != $id) {
                throw new Exception("该邮箱已被其他用户使用");
            }
            $fields[] = "email = ?";
            $values[] = $data['email'];
        }
        
        if (isset($data['phone'])) {
            // Check if phone is taken by another user
            $existing = self::findByPhone($data['phone']);
            if ($existing && $existing['id'] != $id) {
                throw new Exception("该手机号已被其他用户使用");
            }
            $fields[] = "phone = ?";
            $values[] = $data['phone'];
        }
        
        if (isset($data['avatar'])) {
            $fields[] = "avatar = ?";
            $values[] = $data['avatar'];
        }

        if (empty($fields)) {
            return true; // Nothing to update
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        $values[] = $id;
        
        $stmt = $db->prepare($sql);
        if ($stmt->execute($values)) {
            self::logActivity($id, 'update_profile', '更新个人资料', $_SERVER['REMOTE_ADDR'] ?? 'Unknown');
            return true;
        }
        return false;
    }

    // 修改密码
    public static function updatePassword($id, $newPassword) {
        $db = Database::getInstance()->getConnection();
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $stmt = $db->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
        if ($stmt->execute([$passwordHash, $id])) {
            self::logActivity($id, 'change_password', '修改密码', $_SERVER['REMOTE_ADDR'] ?? 'Unknown');
            return true;
        }
        return false;
    }

    // 记录活动日志
    public static function logActivity($userId, $action, $details = '', $ip = '') {
        try {
            $db = Database::getInstance()->getConnection();
            // Check if table exists first to avoid errors during migration
            // For performance, we might skip this check in prod, but here it's safer
            
            $stmt = $db->prepare("INSERT INTO user_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $action, $details, $ip]);
        } catch (Exception $e) {
            // Ignore logging errors to prevent blocking main flow
            // Or log to file
            error_log("Failed to log user activity: " . $e->getMessage());
        }
    }

    // 获取积分历史
    public static function getPointHistory($userId, $limit = 20, $offset = 0) {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM user_logs WHERE user_id = ? AND action IN ('freeze_points', 'unfreeze_points', 'consume_points', 'add_points', 'admin_update_points') ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 统计积分历史数量
    public static function countPointHistory($userId) {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM user_logs WHERE user_id = ? AND action IN ('freeze_points', 'unfreeze_points', 'consume_points', 'add_points', 'admin_update_points')";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    // 获取最近活动
    public static function getRecentActivity($userId, $limit = 10) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM user_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
            // Bind limit as integer
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
