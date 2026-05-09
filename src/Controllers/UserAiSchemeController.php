<?php
// src/Controllers/UserAiSchemeController.php

require_once __DIR__ . '/../Models/AiScheme.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/ApiConfig.php';
require_once __DIR__ . '/../Config/AppConfig.php';

class UserAiSchemeController {
    
    public static function index() {
        self::checkAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $userId = $_SESSION['user_id'];
        $schemes = AiScheme::getByUser($userId, $limit, $offset);
        $total = AiScheme::countByUser($userId);
        $totalPages = ceil($total / $limit);
        
        $userPoints = User::getPoints($userId);
        
        $pageTitle = 'AI 写作方案';
        ob_start();
        require __DIR__ . '/../../templates/user/ai_schemes_list.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/../../templates/user/layout.php';
    }
    
    public static function create() {
        self::checkAuth();
        
        $userId = $_SESSION['user_id'];
        $userPoints = User::getPoints($userId);
        
        $isEdit = false;
        $scheme = null;
        
        $pageTitle = '新建 AI 方案';
        ob_start();
        require __DIR__ . '/../../templates/user/ai_scheme_form.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/../../templates/user/layout.php';
    }

    public static function edit($id) {
        self::checkAuth();
        $userId = $_SESSION['user_id'];
        
        $scheme = AiScheme::find($id);
        
        if (!$scheme || $scheme['user_id'] != $userId) {
            header('Location: /user/ai-schemes');
            exit;
        }
        
        // Allow editing if pending, paused, or completed/rejected (which implies restart)
        $isRestart = in_array($scheme['status'], ['completed', 'rejected', 'paused']);
        
        if ($scheme['status'] !== 'pending' && !$isRestart) {
            $_SESSION['error'] = '当前状态无法编辑，只能编辑待审核或已完成/拒绝的方案。';
            header('Location: /user/ai-schemes');
            exit;
        }
        
        $userPoints = User::getPoints($userId);
        $isEdit = true;
        
        $pageTitle = '编辑 AI 方案';
        ob_start();
        require __DIR__ . '/../../templates/user/ai_scheme_form.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/../../templates/user/layout.php';
    }
    
    public static function store() {
        self::checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user/ai-schemes');
            exit;
        }
        
        $name = trim($_POST['name'] ?? '');
        $region = $_POST['region'] ?? 'CN';
        $language = $_POST['language'] ?? 'zh-CN';
        $keywords = trim($_POST['keywords'] ?? '');
        $prompt = $_POST['prompt'] ?? '';
        $targetCount = (int)($_POST['target_count'] ?? 0);
        $dailyLimit = (int)($_POST['daily_limit'] ?? 0);
        
        // Validation
        if (empty($name) || empty($keywords) || $targetCount <= 0) {
            $_SESSION['error'] = '请填写完整信息，生成数量必须大于0';
            header('Location: /user/ai-schemes/create');
            exit;
        }
        
        // Cost calculation (Assume 1 point per post for now, configurable later)
        $costPerPost = 1; 
        $totalCost = $targetCount * $costPerPost;
        $userId = $_SESSION['user_id'];
        
        // Freeze points
        if (!User::freezePoints($userId, $totalCost, "提交方案冻结: $name")) {
            $_SESSION['error'] = '积分不足，无法创建方案。需要 ' . $totalCost . ' 积分。';
            header('Location: /user/ai-schemes/create');
            exit;
        }
        
        // Create Scheme
        $config = [
            'region' => $region,
            'language' => $language,
            'keywords' => $keywords,
            'prompt' => $prompt
        ];
        
        $schemeId = AiScheme::create([
            'user_id' => $userId,
            'name' => $name,
            'config' => $config,
            'target_count' => $targetCount,
            'daily_limit' => $dailyLimit,
            'cost_per_post' => $costPerPost,
            'frozen_points' => $totalCost
        ]);
        
        // Create corresponding ApiConfig (Pending state)
        // This makes it visible in Admin API List
        ApiConfig::create([
            'name' => $name . ' (User Scheme)',
            'geo_region' => $region,
            'language' => $language,
            'keywords' => $keywords,
            'promotion_info' => $prompt, // Using prompt as promotion info or custom instruction
            'status' => 0, // Disabled/Pending
            'user_id' => $userId,
            'scheme_id' => $schemeId,
            'daily_limit' => $dailyLimit,
            'keyword_count' => 1 // Default
        ]);
        
        $_SESSION['success'] = '方案已提交，积分已预冻结，请等待管理员审核。';
        header('Location: /user/ai-schemes');
        exit;
    }

    public static function update($id) {
        self::checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user/ai-schemes');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $scheme = AiScheme::find($id);
        
        if (!$scheme || $scheme['user_id'] != $userId) {
            header('Location: /user/ai-schemes');
            exit;
        }
        
        // Allow editing if pending, paused, or completed/rejected (which implies restart)
        $isRestart = in_array($scheme['status'], ['completed', 'rejected']);
        
        if ($scheme['status'] !== 'pending' && !$isRestart) {
            $_SESSION['error'] = '当前状态无法编辑，只能编辑待审核或已完成/拒绝的方案。';
            header('Location: /user/ai-schemes');
            exit;
        }
        
        $name = trim($_POST['name'] ?? '');
        $region = $_POST['region'] ?? 'CN';
        $language = $_POST['language'] ?? 'zh-CN';
        $keywords = trim($_POST['keywords'] ?? '');
        $prompt = trim($_POST['prompt'] ?? '');
        $targetCount = (int)($_POST['target_count'] ?? 1);
        $dailyLimit = (int)($_POST['daily_limit'] ?? 1);
        
        // Validation
        if (empty($name) || empty($keywords) || $targetCount <= 0) {
            $_SESSION['error'] = '请填写完整信息，生成数量必须大于0';
            header('Location: /user/ai-schemes/edit/' . $id);
            exit;
        }
        
        $costPerPost = $scheme['cost_per_post'] ?: 1;
        $newTotalCost = $targetCount * $costPerPost;
        
        // Points Logic
        $oldFrozen = $scheme['frozen_points'];
        
        if ($isRestart) {
            // Check balance
            if (!User::freezePoints($userId, $newTotalCost, "重启方案冻结: $name")) {
                $_SESSION['error'] = '积分不足，无法重启方案。需要 ' . $newTotalCost . ' 积分。';
                header('Location: /user/ai-schemes/edit/' . $id);
                exit;
            }
        } else {
            // Pending status: unfreeze old, freeze new
            User::unfreezePoints($userId, $oldFrozen, "修改方案退还: {$scheme['name']}");
            if (!User::freezePoints($userId, $newTotalCost, "修改方案冻结: $name")) {
                // Revert
                User::freezePoints($userId, $oldFrozen, "修改失败回退: {$scheme['name']}");
                $_SESSION['error'] = '修改失败：积分不足。需要 ' . $newTotalCost . ' 积分。';
                header('Location: /user/ai-schemes/edit/' . $id);
                exit;
            }
        }
        
        // Update Scheme
        $config = [
            'region' => $region,
            'language' => $language,
            'keywords' => $keywords,
            'prompt' => $prompt
        ];
        
        $updateData = [
            'name' => $name,
            'config' => $config,
            'target_count' => $targetCount,
            'daily_limit' => $dailyLimit,
            'frozen_points' => $newTotalCost
        ];
        
        if ($isRestart) {
            $updateData['status'] = 'pending';
            $updateData['generated_count'] = 0;
            $updateData['admin_notes'] = null;
        }
        
        AiScheme::update($id, $updateData);
        
        // Update ApiConfig
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE api_configs SET name = ?, geo_region = ?, language = ?, keywords = ?, promotion_info = ?, daily_limit = ?, status = 0 WHERE scheme_id = ?");
        $stmt->execute([
            $name . ' (User Scheme)',
            $region,
            $language,
            $keywords,
            $prompt,
            $dailyLimit,
            $id
        ]);
        
        $_SESSION['success'] = '方案已更新' . ($isRestart ? '并重新提交审核' : '') . '。';
        header('Location: /user/ai-schemes');
        exit;
    }

    public static function resubmit($id) {
        self::checkAuth();
        
        $userId = $_SESSION['user_id'];
        $scheme = AiScheme::find($id);
        
        if (!$scheme || $scheme['user_id'] != $userId) {
            header('Location: /user/ai-schemes');
            exit;
        }
        
        if (!in_array($scheme['status'], ['completed', 'rejected', 'paused'])) {
            $_SESSION['error'] = '当前状态无法重新提交。';
            header('Location: /user/ai-schemes');
            exit;
        }
        
        // Calculate cost
        $targetCount = $scheme['target_count'];
        $costPerPost = $scheme['cost_per_post'] ?: 1;
        $totalCost = $targetCount * $costPerPost;
        
        // Freeze points
        if (!User::freezePoints($userId, $totalCost, "重新提交方案: {$scheme['name']}")) {
             $_SESSION['error'] = '积分不足，无法重新提交。';
             header('Location: /user/ai-schemes');
             exit;
        }
        
        // Reset Scheme
        AiScheme::update($id, [
            'status' => 'pending',
            'generated_count' => 0,
            'frozen_points' => $totalCost,
            'admin_notes' => null
        ]);
        
        // Update ApiConfig Status to 0 (Pending)
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE api_configs SET status = 0 WHERE scheme_id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = '方案已重新提交，等待审核。';
        header('Location: /user/ai-schemes');
        exit;
    }

    public static function destroy($id) {
        self::checkAuth();
        $userId = $_SESSION['user_id'];
        $scheme = AiScheme::find($id);
        
        if (!$scheme || $scheme['user_id'] != $userId) {
            header('Location: /user/ai-schemes');
            exit;
        }
        
        if ($scheme['status'] !== 'pending') {
            $_SESSION['error'] = '只能删除待审核状态的方案。';
            header('Location: /user/ai-schemes');
            exit;
        }
        
        // Refund frozen points
        User::unfreezePoints($userId, $scheme['frozen_points'], "删除方案退还: {$scheme['name']}");
        
        // Delete Scheme
        AiScheme::delete($id);
        
        // Delete ApiConfig
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM api_configs WHERE scheme_id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = '方案已删除，冻结积分已退还。';
        header('Location: /user/ai-schemes');
        exit;
    }
    
    private static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}
