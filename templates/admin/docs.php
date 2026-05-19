<?php
// templates/admin/docs.php — Developer documentation
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8"><title>开发者文档 - xAI CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
<div class="max-w-4xl mx-auto px-6 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">开发者文档</h1>
            <p class="text-gray-500 mt-2">构建 xAI CMS 插件和模板</p>
        </div>
        <a href="/admin/articles" class="text-gray-500 hover:text-gray-700"><i class="bi bi-arrow-left mr-1"></i>返回后台</a>
    </div>

    <div class="space-y-8">
        <!-- Quick Start -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="bi bi-rocket-takeoff text-indigo-600 mr-2"></i>快速开始</h2>
            <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm overflow-x-auto">
                <pre>mkdir plugins/my-plugin
cd plugins/my-plugin
touch plugin.php</pre>
            </div>
            <p class="text-sm text-gray-500 mt-3">每个插件就是一个文件夹 + 一个 plugin.php 文件。xAI CMS 会自动扫描 plugins/ 目录。</p>
        </div>

        <!-- Plugin Structure -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="bi bi-puzzle text-indigo-600 mr-2"></i>插件结构</h2>
            <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm overflow-x-auto">
<pre>plugins/
└── my-plugin/
    ├── plugin.php          # 必须：插件主文件
    ├── README.md           # 可选：说明文档
    └── assets/             # 可选：静态资源</pre>
            </div>
        </div>

        <!-- Plugin Header -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="bi bi-file-code text-indigo-600 mr-2"></i>插件元数据</h2>
            <p class="text-sm text-gray-500 mb-4">plugin.php 文件头部必须包含以下注释：</p>
            <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm overflow-x-auto">
<pre>&lt;?php
/**
 * Plugin Name: 我的插件
 * Description: 在页面底部加一句话
 * Version: 1.0.0
 * Author: 你的名字
 * Author URI: https://example.com
 * License: MIT
 */</pre>
            </div>
        </div>

        <!-- Hooks -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="bi bi-link-45deg text-indigo-600 mr-2"></i>Hook 系统</h2>
            <p class="text-sm text-gray-500 mb-4">xAI CMS 使用 WordPress 风格的 Hook 机制：</p>
            <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm overflow-x-auto mb-4">
<pre>// Action Hook
add_action('before_footer', function() {
    echo '&lt;p&gt;Powered by xAI CMS&lt;/p&gt;';
});

// Filter Hook
add_filter('article_content', function($content) {
    return $content . '&lt;p&gt;阅读更多...&lt;/p&gt;';
});</pre>
            </div>
            <h4 class="font-bold text-gray-900 mt-6 mb-3">可用 Hook 点</h4>
            <table class="w-full text-sm">
                <thead><tr class="text-left border-b border-gray-200"><th class="py-2 pr-4">Hook</th><th class="py-2 pr-4">类型</th><th class="py-2">说明</th></tr></thead>
                <tbody class="text-gray-600">
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">before_footer</td><td class="py-2 pr-4">Action</td><td class="py-2">页面底部输出前</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">before_output</td><td class="py-2 pr-4">Action</td><td class="py-2">页面完全输出前</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">article_saved</td><td class="py-2 pr-4">Action</td><td class="py-2">文章保存后 ($articleId, $data)</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">user_registered</td><td class="py-2 pr-4">Action</td><td class="py-2">用户注册后 ($userId, $data)</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">admin_dashboard_widgets</td><td class="py-2 pr-4">Action</td><td class="py-2">后台仪表盘小组件</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">article_content</td><td class="py-2 pr-4">Filter</td><td class="py-2">文章内容过滤 ($content)</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Template Dev -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="bi bi-palette2 text-indigo-600 mr-2"></i>模板开发</h2>
            <p class="text-sm text-gray-500 mb-4">模板放在 templates/ 目录下，每个模板一个文件夹：</p>
            <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm overflow-x-auto mb-4">
<pre>templates/
└── my-theme/
    ├── template.json     # 模板元数据
    ├── home.php          # 首页
    ├── article.php       # 文章页
    ├── about.php         # 关于页
    ├── partials/         # 公共组件
    │   ├── header.php
    │   └── footer.php
    └── assets/           # 静态资源
        └── style.css</pre>
            </div>
            <h4 class="font-bold text-gray-900 mt-6 mb-3">template.json 示例</h4>
            <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-sm overflow-x-auto">
<pre>{
  "name": "我的主题",
  "version": "1.0.0",
  "author": "开发者名称",
  "description": "一个简洁的主题",
  "screenshot": "screenshot.png"
}</pre>
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="bi bi-cash-coin text-indigo-600 mr-2"></i>收益分成</h2>
            <p class="text-sm text-gray-500 mb-4">插件和模板可以设置付费价格。用户购买后，开发者获得收益分成：</p>
            <ul class="list-disc pl-5 space-y-2 text-sm text-gray-600">
                <li>分成比例：<strong><?php echo htmlspecialchars($settings['developer_revenue_share'] ?? '70'); ?>%</strong> 归开发者，平台抽 <?php echo 100 - (int)($settings['developer_revenue_share'] ?? 70); ?>%</li>
                <li>收益实时到账，可在开发者中心查看</li>
                <li>满 100 元可提现到微信</li>
                <li>在 plugin.php 的注释中添加 <code>Price: 9.9</code> 即可设置价格</li>
            </ul>
        </div>

        <!-- API -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4"><i class="bi bi-code-slash text-indigo-600 mr-2"></i>可用 API</h2>
            <table class="w-full text-sm">
                <thead><tr class="text-left border-b border-gray-200"><th class="py-2 pr-4">函数</th><th class="py-2">说明</th></tr></thead>
                <tbody class="text-gray-600">
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">Settings::get('key')</td><td class="py-2">获取系统设置</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">Article::getLatest(10)</td><td class="py-2">获取最新文章</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">Category::getAll()</td><td class="py-2">获取所有分类</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">User::find($id)</td><td class="py-2">获取用户信息</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">Plan::has('plugins')</td><td class="py-2">检查当前计划功能权限</td></tr>
                    <tr><td class="py-2 pr-4 font-mono text-indigo-600">__('Hello', '你好')</td><td class="py-2">多语言翻译函数</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
