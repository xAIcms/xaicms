<?php
// templates/admin/marketplace.php — Plugin & Template Marketplace

// Built-in free plugins
$builtinPlugins = [
    ['id' => 'xai-announcements', 'name' => '平台公告', 'icon' => 'bi-megaphone', 'desc' => '管理平台公告和系统通知', 'author' => 'xAI', 'version' => '1.0', 'active' => true],
    ['id' => 'xai-points', 'name' => '积分系统', 'icon' => 'bi-coin', 'desc' => '积分充值、消费记录、积分排行', 'author' => 'xAI', 'version' => '1.0', 'active' => true],
    ['id' => 'xai-sms', 'name' => '短信验证', 'icon' => 'bi-chat-dots', 'desc' => '腾讯云短信验证，登录/注册安全加固', 'author' => 'xAI', 'version' => '1.0', 'active' => true],
    ['id' => 'xai-spider-log', 'name' => '爬虫分析', 'icon' => 'bi-graph-up', 'desc' => 'Googlebot/GPTBot等爬虫访问统计', 'author' => 'xAI', 'version' => '1.0', 'active' => true],
    ['id' => 'xai-updates', 'name' => '更新日志', 'icon' => 'bi-journal-text', 'desc' => '系统版本更新记录展示', 'author' => 'xAI', 'version' => '1.0', 'active' => true],
];

// Available free plugins (not yet installed)
$availablePlugins = [
    ['id' => 'xai-seo-analyzer', 'name' => 'SEO分析器', 'icon' => 'bi-search', 'desc' => '文章SEO评分、关键词密度、可读性分析', 'author' => 'xAI', 'version' => '1.0', 'installs' => 0],
    ['id' => 'xai-auto-translate', 'name' => '自动翻译', 'icon' => 'bi-translate', 'desc' => '文章自动翻译为14种语言并发布', 'author' => 'xAI', 'version' => '1.0', 'installs' => 0],
    ['id' => 'xai-social-share', 'name' => '社交分享', 'icon' => 'bi-share', 'desc' => '文章一键分享到Twitter/Facebook/LinkedIn', 'author' => 'xAI', 'version' => '1.0', 'installs' => 0],
    ['id' => 'xai-newsletter', 'name' => '邮件订阅', 'icon' => 'bi-envelope', 'desc' => '访客邮件订阅，新文章自动推送', 'author' => 'xAI', 'version' => '1.0', 'installs' => 0],
];

// Built-in free templates
$builtinTemplates = [
    ['id' => 'default', 'name' => '默认主题', 'icon' => 'bi-palette', 'desc' => '简洁大气的默认主题，适合企业官网', 'author' => 'xAI', 'active' => true],
    ['id' => 'blog', 'name' => '博客主题', 'icon' => 'bi-journal', 'desc' => '专注阅读体验的博客风格主题', 'author' => 'xAI', 'active' => false],
];

$availableTemplates = [
    ['id' => 'saas', 'name' => 'SaaS主题', 'icon' => 'bi-cloud', 'desc' => '适合SaaS产品官网的现代主题', 'author' => 'xAI', 'installs' => 0],
    ['id' => 'shop', 'name' => '电商主题', 'icon' => 'bi-cart', 'desc' => '适合跨境电商独立站的主题', 'author' => 'xAI', 'installs' => 0],
    ['id' => 'docs', 'name' => '文档主题', 'icon' => 'bi-book', 'desc' => '适合产品文档/知识库的主题', 'author' => 'xAI', 'installs' => 0],
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>插件市场 - xAI CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">插件 & 模板市场</h1>
                <p class="text-gray-500 mt-2">扩展你的 xAI CMS 功能</p>
            </div>
            <a href="/admin/articles" class="text-gray-500 hover:text-gray-700">
                <i class="bi bi-arrow-left mr-1"></i>返回后台
            </a>
        </div>

        <!-- Plugins Section -->
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="bi bi-puzzle text-indigo-600"></i> 已安装插件
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <?php foreach ($builtinPlugins as $p): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="bi <?php echo $p['icon']; ?> text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm"><?php echo $p['name']; ?></h3>
                        <span class="text-xs text-gray-400">v<?php echo $p['version']; ?> · <?php echo $p['author']; ?></span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mb-3"><?php echo $p['desc']; ?></p>
                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">已激活</span>
            </div>
            <?php endforeach; ?>
        </div>

        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="bi bi-download text-indigo-600"></i> 可安装插件
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10">
            <?php foreach ($availablePlugins as $p): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-5 border-dashed">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="bi <?php echo $p['icon']; ?> text-gray-500"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm"><?php echo $p['name']; ?></h3>
                        <span class="text-xs text-gray-400"><?php echo $p['author']; ?></span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mb-3"><?php echo $p['desc']; ?></p>
                <button disabled class="w-full py-2 bg-gray-100 text-gray-400 rounded-lg text-xs font-medium cursor-not-allowed">
                    即将开放
                </button>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Templates Section -->
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="bi bi-palette2 text-purple-600"></i> 已安装模板
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <?php foreach ($builtinTemplates as $t): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="bi <?php echo $t['icon']; ?> text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm"><?php echo $t['name']; ?></h3>
                        <span class="text-xs text-gray-400"><?php echo $t['author']; ?></span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mb-3"><?php echo $t['desc']; ?></p>
                <span class="inline-block px-2 py-0.5 <?php echo $t['active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; ?> rounded text-xs font-medium">
                    <?php echo $t['active'] ? '使用中' : '未激活'; ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>

        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="bi bi-download text-purple-600"></i> 可安装模板
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($availableTemplates as $t): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-5 border-dashed">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="bi <?php echo $t['icon']; ?> text-gray-500"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm"><?php echo $t['name']; ?></h3>
                        <span class="text-xs text-gray-400"><?php echo $t['author']; ?></span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mb-3"><?php echo $t['desc']; ?></p>
                <button disabled class="w-full py-2 bg-gray-100 text-gray-400 rounded-lg text-xs font-medium cursor-not-allowed">
                    即将开放
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
