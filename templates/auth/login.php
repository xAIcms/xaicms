<?php
// Ensure settings are available
if (!isset($settings) && class_exists('Settings')) {
    $settings = Settings::getAll();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户登录 - <?php echo htmlspecialchars($settings['siteName'] ?? 'GeoPulse'); ?></title>
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
<body class="bg-gray-50 h-screen flex items-center justify-center">

<div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
    <div class="px-8 py-10">
        <div class="text-center mb-8">
            <a href="/" class="inline-block mb-4 text-indigo-600">
                <i class="bi bi-globe2 text-4xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">欢迎回来</h2>
            <p class="text-gray-500 mt-2 text-sm">登录您的 <?php echo htmlspecialchars($settings['siteName'] ?? 'GeoPulse'); ?> 账户</p>
        </div>

        <?php if (isset($_GET['registered'])): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r">
                <div class="flex items-center">
                    <i class="bi bi-check-circle text-green-500 mr-2"></i>
                    <p class="text-sm text-green-700">注册成功，请登录</p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['reset'])): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r">
                <div class="flex items-center">
                    <i class="bi bi-check-circle text-green-500 mr-2"></i>
                    <p class="text-sm text-green-700">密码重置成功，请使用新密码登录</p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
                <div class="flex items-center">
                    <i class="bi bi-exclamation-circle text-red-500 mr-2"></i>
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="space-y-6" id="loginForm">
            <?php if (class_exists('Csrf')) echo Csrf::input(); ?>
            <input type="hidden" name="login_type" value="password">
            
            <!-- Password Login Fields -->
            <div id="password-fields">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">手机号</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-phone text-gray-400"></i>
                        </div>
                        <input type="text" id="email" name="email" required class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="请输入手机号">
                    </div>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">密码</label>
                        <a href="/forgot-password" class="text-xs text-indigo-600 hover:text-indigo-500">忘记密码?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="••••••••">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                登录
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                还没有账号？ 
                <a href="/register" class="font-medium text-indigo-600 hover:text-indigo-500">立即注册</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
