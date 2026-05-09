<?php
// templates/user/layout.php
// Expected variables: $content, $pageTitle
if (!isset($settings) && class_exists('Settings')) {
    $settings = Settings::getAll();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? '用户中心'); ?> - <?php echo htmlspecialchars($settings['siteName'] ?? 'GeoPulse'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">

<div class="min-h-screen flex flex-col md:flex-row">
    <!-- Sidebar -->
    <aside class="bg-white w-full md:w-64 border-r border-gray-200 md:h-screen md:sticky md:top-0 flex-shrink-0 z-30">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between md:justify-start">
            <a href="/" class="flex items-center space-x-2 text-indigo-600 font-bold text-xl">
                <i class="bi bi-globe2"></i>
                <span>xAI CMS</span>
            </a>
            <button class="md:hidden text-gray-500 focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <i class="bi bi-list text-2xl"></i>
            </button>
        </div>
        
        <nav id="mobile-menu" class="hidden md:block p-4 space-y-1 overflow-y-auto h-[calc(100vh-80px)]">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                账户管理
            </div>
            
            <a href="/user/center" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg <?php echo strpos($_SERVER['REQUEST_URI'], '/user/center') !== false ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?> group transition-colors">
                <i class="bi bi-speedometer2 w-5 h-5 mr-3 <?php echo strpos($_SERVER['REQUEST_URI'], '/user/center') !== false ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500'; ?>"></i>
                仪表盘
            </a>
            
            <a href="/user/ai-schemes" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg <?php echo strpos($_SERVER['REQUEST_URI'], '/user/ai-schemes') !== false ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?> group transition-colors">
                <i class="bi bi-robot w-5 h-5 mr-3 <?php echo strpos($_SERVER['REQUEST_URI'], '/user/ai-schemes') !== false ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500'; ?>"></i>
                AI 写作方案
            </a>

            <a href="/user/recharge" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg <?php echo strpos($_SERVER['REQUEST_URI'], '/user/recharge') !== false ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?> group transition-colors">
                <i class="bi bi-wallet2 w-5 h-5 mr-3 <?php echo strpos($_SERVER['REQUEST_URI'], '/user/recharge') !== false ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500'; ?>"></i>
                积分充值
            </a>

            <a href="/user/point-history" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg <?php echo strpos($_SERVER['REQUEST_URI'], '/user/point-history') !== false ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?> group transition-colors">
                <i class="bi bi-clock-history w-5 h-5 mr-3 <?php echo strpos($_SERVER['REQUEST_URI'], '/user/point-history') !== false ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500'; ?>"></i>
                积分记录
            </a>

            <a href="/user/profile" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg <?php echo strpos($_SERVER['REQUEST_URI'], '/user/profile') !== false ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?> group transition-colors">
                <i class="bi bi-person w-5 h-5 mr-3 <?php echo strpos($_SERVER['REQUEST_URI'], '/user/profile') !== false ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500'; ?>"></i>
                个人资料
            </a>
            
            <a href="/user/security" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg <?php echo strpos($_SERVER['REQUEST_URI'], '/user/security') !== false ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?> group transition-colors">
                <i class="bi bi-shield-lock w-5 h-5 mr-3 <?php echo strpos($_SERVER['REQUEST_URI'], '/user/security') !== false ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500'; ?>"></i>
                安全设置
            </a>

            <div class="border-t border-gray-100 my-4 pt-4">
                <a href="/logout" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 group transition-colors">
                    <i class="bi bi-box-arrow-right w-5 h-5 mr-3 text-red-400 group-hover:text-red-500"></i>
                    退出登录
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top header -->
        <header class="bg-white shadow-sm border-b border-gray-200 py-4 px-6 md:px-8 flex items-center justify-between z-20">
            <h1 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($pageTitle ?? '用户中心'); ?></h1>
            <div class="flex items-center space-x-4">
                <div class="flex items-center text-sm text-gray-600">
                    <span class="mr-2 hidden sm:inline">你好,</span>
                    <span class="font-bold text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name'] ?? '用户'); ?></span>
                </div>
                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
                    <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
            <div class="w-full">
                <?php echo $content; ?>
            </div>
        </div>
    </main>
</div>

</body>
</html>
