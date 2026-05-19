<?php
// templates/admin/upgrade.php — Plan upgrade page with payment
require_once __DIR__ . '/../../src/Services/WechatPayService.php';
require_once __DIR__ . '/../../src/Models/Order.php';

$currentPlan = Plan::current();
$planName = Plan::planName();
$features = Plan::features();
$wechat = new WechatPayService();
$payConfigured = $wechat->isConfigured();

// Handle free upgrade (when payment not configured)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan']) && !$payConfigured) {
    $newPlan = $_POST['plan'];
    if (Plan::upgrade($newPlan)) {
        $currentPlan = $newPlan;
        $planName = Plan::planName();
        $upgradeMsg = "已免费升级到 $planName！";
    }
}

// Handle payment order creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $targetPlan = $_POST['plan'] ?? 'pro';
    $amountCents = (int)($_POST['amount_cents'] ?? 2900);
    $orderNo = Order::generateOrderNo();
    $description = "xAI CMS - 升级到 " . ucfirst($targetPlan);
    
    $result = $wechat->createNativeOrder($orderNo, $description, $amountCents);
    if ($result && !empty($result['code_url'])) {
        $orderId = Order::create([
            'order_no' => $orderNo,
            'user_id' => $_SESSION['user_id'] ?? 0,
            'plan' => $targetPlan,
            'amount' => $amountCents / 100,
            'amount_cents' => $amountCents,
            'description' => $description,
        ]);
        $qrUrl = $result['code_url'];
        $showQR = true;
    } else {
        $payError = '创建支付订单失败，请检查微信支付配置';
    }
}

$pricingPro = $settings['pricing_pro_monthly'] ?? '29';
$pricingProYear = $settings['pricing_pro_yearly'] ?? '199';
$pricingEnt = $settings['pricing_enterprise_monthly'] ?? '99';

$plans = [
    'free' => ['name' => 'Free', 'price' => '免费', 'amount_cents' => 0, 'color' => 'gray', 'desc' => '基础内容管理'],
    'pro' => ['name' => 'Pro', 'price' => "¥{$pricingPro}/月", 'amount_cents' => (int)$pricingPro * 100, 'color' => 'indigo', 'desc' => '专业AI内容工厂'],
    'enterprise' => ['name' => 'Enterprise', 'price' => "¥{$pricingEnt}/月", 'amount_cents' => (int)$pricingEnt * 100, 'color' => 'purple', 'desc' => '团队协作 + 无限扩展'],
];

$featureLabels = [
    'ai_per_day' => 'AI每日调用', 'ai_models' => 'AI模型数', 'languages' => '生成语言',
    'plugins' => '插件系统', 'templates' => '模板市场', 'seo_tools' => 'SEO工具包',
    'api_access' => 'API访问', 'multi_user' => '多用户协作',
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8"><title>升级计划 - xAI CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
<div class="max-w-6xl mx-auto px-6 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">升级计划</h1>
            <p class="text-gray-500 mt-2">当前：<span class="font-bold text-indigo-600"><?php echo $planName; ?></span></p>
        </div>
        <a href="/admin/articles" class="text-gray-500 hover:text-gray-700"><i class="bi bi-arrow-left mr-1"></i>返回后台</a>
    </div>

    <?php if (isset($upgradeMsg)): ?>
    <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6"><?php echo $upgradeMsg; ?></div>
    <?php endif; ?>

    <?php if (isset($showQR)): ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-8 mb-6 text-center max-w-md mx-auto">
        <h3 class="font-bold text-lg mb-4">微信扫码支付</h3>
        <div class="bg-white p-4 inline-block rounded-xl border mb-4">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($qrUrl); ?>" alt="支付二维码" width="200" height="200">
        </div>
        <p class="text-2xl font-bold text-gray-900 mb-2">¥<?php echo ($_POST['amount_cents'] ?? 2900) / 100; ?></p>
        <p class="text-sm text-gray-500">扫码支付后自动升级</p>
        <p class="text-xs text-gray-400 mt-4">订单号：<?php echo $orderNo ?? ''; ?></p>
    </div>
    <?php endif; ?>

    <?php if (isset($payError)): ?>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-red-700"><?php echo $payError; ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($plans as $key => $plan): 
            $isCurrent = $currentPlan === $key;
        ?>
        <div class="bg-white rounded-2xl border <?php echo $isCurrent ? 'border-indigo-500 ring-2 ring-indigo-200 shadow-lg' : 'border-gray-200 shadow-sm'; ?> p-8 relative">
            <?php if ($isCurrent): ?>
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-600 text-white px-4 py-1 rounded-full text-xs font-bold">当前计划</div>
            <?php endif; ?>
            <h3 class="text-xl font-bold text-gray-900 mb-1"><?php echo $plan['name']; ?></h3>
            <p class="text-gray-500 text-sm mb-4"><?php echo $plan['desc']; ?></p>
            <div class="text-3xl font-extrabold text-gray-900 mb-6"><?php echo $plan['price']; ?></div>
            <ul class="space-y-3 mb-8">
                <?php foreach ($featureLabels as $fkey => $flabel): 
                    $val = $features[$key][$fkey] ?? false;
                    $isBool = in_array($fkey, ['plugins', 'templates', 'seo_tools', 'api_access', 'multi_user']);
                ?>
                <li class="flex items-center gap-2 text-sm">
                    <?php if ($val): ?>
                        <i class="bi bi-check-circle-fill text-green-500"></i>
                        <span class="text-gray-700"><?php echo $flabel; ?><?php if (!$isBool): ?> <span class="text-gray-400">(<?php echo $val >= 999 ? '无限' : $val; ?>)</span><?php endif; ?></span>
                    <?php else: ?>
                        <i class="bi bi-x-circle text-gray-300"></i><span class="text-gray-400"><?php echo $flabel; ?></span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php if (!$isCurrent): ?>
                <?php if ($payConfigured && $plan['amount_cents'] > 0): ?>
                <form method="POST">
                    <input type="hidden" name="create_order" value="1">
                    <input type="hidden" name="plan" value="<?php echo $key; ?>">
                    <input type="hidden" name="amount_cents" value="<?php echo $plan['amount_cents']; ?>">
                    <button type="submit" class="w-full py-3 bg-<?php echo $plan['color']; ?>-600 text-white rounded-xl font-bold hover:bg-<?php echo $plan['color']; ?>-700 transition-colors">
                        <i class="bi bi-wechat mr-1"></i>微信支付 <?php echo $plan['price']; ?>
                    </button>
                </form>
                <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="plan" value="<?php echo $key; ?>">
                    <button type="submit" class="w-full py-3 bg-<?php echo $plan['color']; ?>-600 text-white rounded-xl font-bold hover:bg-<?php echo $plan['color']; ?>-700 transition-colors">
                        免费升级
                    </button>
                </form>
                <?php endif; ?>
            <?php else: ?>
            <div class="w-full py-3 bg-gray-100 text-gray-500 rounded-xl font-bold text-center"><i class="bi bi-check-lg mr-1"></i>当前计划</div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Payment Config -->
    <div class="mt-10 bg-white rounded-2xl border border-gray-200 p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="bi bi-gear text-gray-500"></i> 微信支付配置
            <?php if ($payConfigured): ?>
            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-normal">已配置</span>
            <?php else: ?>
            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-normal">未配置</span>
            <?php endif; ?>
        </h2>
        <p class="text-sm text-gray-500 mb-4">配置微信支付后可在线收款。获取参数：<a href="https://pay.weixin.qq.com/" target="_blank" class="text-indigo-600">微信支付商户平台</a></p>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_payment'])) {
            $payKeys = ['wx_appid', 'wx_mchid', 'wx_apiv3_key', 'wx_serial_no', 'wx_key_path', 'pricing_pro_monthly', 'pricing_pro_yearly', 'pricing_enterprise_monthly', 'developer_revenue_share'];
            foreach ($payKeys as $k) {
                Settings::update($k, $_POST[$k] ?? '');
            }
            echo '<div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4 text-green-700 text-sm">配置已保存</div>';
        }
        ?>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-xs font-bold text-gray-600 mb-1">AppID</label><input type="text" name="wx_appid" value="<?php echo htmlspecialchars($settings['wx_appid'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1">商户号 MchID</label><input type="text" name="wx_mchid" value="<?php echo htmlspecialchars($settings['wx_mchid'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1">API V3 Key</label><input type="text" name="wx_apiv3_key" value="<?php echo htmlspecialchars($settings['wx_apiv3_key'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1">证书序列号</label><input type="text" name="wx_serial_no" value="<?php echo htmlspecialchars($settings['wx_serial_no'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1">私钥路径</label><input type="text" name="wx_key_path" value="<?php echo htmlspecialchars($settings['wx_key_path'] ?? ''); ?>" placeholder="/path/to/apiclient_key.pem" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1">开发者分成比例 (%)</label><input type="number" name="developer_revenue_share" value="<?php echo htmlspecialchars($settings['developer_revenue_share'] ?? '70'); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1">Pro月价 (元)</label><input type="number" name="pricing_pro_monthly" value="<?php echo htmlspecialchars($settings['pricing_pro_monthly'] ?? '29'); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1">Pro年价 (元)</label><input type="number" name="pricing_pro_yearly" value="<?php echo htmlspecialchars($settings['pricing_pro_yearly'] ?? '199'); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1">Enterprise月价 (元)</label><input type="number" name="pricing_enterprise_monthly" value="<?php echo htmlspecialchars($settings['pricing_enterprise_monthly'] ?? '99'); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
            <div class="md:col-span-2"><button type="submit" name="save_payment" value="1" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">保存支付配置</button></div>
        </form>
    </div>
</div>
</body>
</html>
