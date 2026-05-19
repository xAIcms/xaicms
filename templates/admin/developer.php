<?php
// templates/admin/developer.php — Developer center
require_once __DIR__ . '/../../src/Models/Developer.php';

$userId = $_SESSION['user_id'] ?? 0;
$dev = Developer::findByUserId($userId);

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_dev'])) {
    Developer::register($userId, $_POST);
    $dev = Developer::findByUserId($userId);
    $msg = '开发者注册成功！';
}

// Handle withdraw
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw']) && $dev) {
    $amount = (float)($_POST['withdraw_amount'] ?? 0);
    if (Developer::requestWithdraw($dev['id'], $amount)) {
        $msg = "提现申请已提交：¥{$amount}";
        $dev = Developer::findByUserId($userId);
    } else {
        $msg = "提现失败，余额不足";
    }
}

$transactions = $dev ? Developer::getTransactions($dev['id']) : [];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8"><title>开发者中心 - xAI CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
<div class="max-w-5xl mx-auto px-6 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">开发者中心</h1>
            <p class="text-gray-500 mt-2">管理你的插件、收益和提现</p>
        </div>
        <div class="flex gap-3">
            <a href="/admin/docs" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"><i class="bi bi-book mr-1"></i>开发文档</a>
            <a href="/admin/articles" class="text-gray-500 hover:text-gray-700"><i class="bi bi-arrow-left mr-1"></i>返回后台</a>
        </div>
    </div>

    <?php if (isset($msg)): ?>
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 text-green-700"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if (!$dev): ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-8 max-w-lg">
        <h2 class="text-xl font-bold mb-4">注册成为开发者</h2>
        <p class="text-sm text-gray-500 mb-6">注册后可以发布插件和模板，获得收益分成（<?php echo htmlspecialchars($settings['developer_revenue_share'] ?? '70'); ?>%）</p>
        <form method="POST" class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">开发者名称</label><input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">简介</label><textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">微信账号（用于提现）</label><input type="text" name="wechat_account" class="w-full border border-gray-300 rounded-lg px-3 py-2"></div>
            <button type="submit" name="register_dev" value="1" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-indigo-700">注册</button>
        </form>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">可提现余额</p>
            <p class="text-3xl font-bold text-gray-900">¥<?php echo number_format($dev['balance'], 2); ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">累计收益</p>
            <p class="text-3xl font-bold text-gray-900">¥<?php echo number_format($dev['total_earned'], 2); ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">分成比例</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($settings['developer_revenue_share'] ?? '70'); ?>%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="font-bold text-gray-900 mb-4">申请提现</h3>
            <form method="POST" class="flex gap-3">
                <input type="number" name="withdraw_amount" step="0.01" min="1" max="<?php echo $dev['balance']; ?>" placeholder="金额" class="flex-1 border border-gray-300 rounded-lg px-3 py-2" required>
                <button type="submit" name="withdraw" value="1" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-indigo-700">提现</button>
            </form>
            <p class="text-xs text-gray-400 mt-2">提现到微信：<?php echo htmlspecialchars($dev['wechat_account'] ?? '未设置'); ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="font-bold text-gray-900 mb-4">交易记录</h3>
            <?php if ($transactions): ?>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                <?php foreach ($transactions as $tx): ?>
                <div class="flex justify-between text-sm py-1.5 border-b border-gray-100">
                    <span class="<?php echo $tx['type'] === 'revenue' ? 'text-green-600' : 'text-orange-600'; ?>">
                        <?php echo $tx['type'] === 'revenue' ? '+' : '-'; ?>¥<?php echo $tx['amount']; ?>
                    </span>
                    <span class="text-gray-400 text-xs"><?php echo $tx['created_at']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-400">暂无交易</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
