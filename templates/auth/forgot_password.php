<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>找回密码 - xAI CMS</title>
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
            <h2 class="text-2xl font-bold text-gray-900">重置密码</h2>
            <p class="text-gray-500 mt-2 text-sm">通过手机验证码重置您的密码</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
                <div class="flex items-center">
                    <i class="bi bi-exclamation-circle text-red-500 mr-2"></i>
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="/forgot-password" class="space-y-6">
            <?php if (class_exists('Csrf')) echo Csrf::input(); ?>
            
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">手机号</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-phone text-gray-400"></i>
                    </div>
                    <input type="tel" id="phone" name="phone" required class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="请输入注册手机号" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>
            </div>

            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">验证码</label>
                <div class="flex gap-2">
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-shield-lock text-gray-400"></i>
                        </div>
                        <input type="text" id="code" name="code" required class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="6位验证码">
                    </div>
                    <button type="button" id="sendCodeBtn" onclick="sendSmsCode()" class="whitespace-nowrap px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        获取验证码
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">新密码</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-lock text-gray-400"></i>
                    </div>
                    <input type="password" id="password" name="password" required class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="请输入新密码（至少6位）">
                </div>
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">确认新密码</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-lock-fill text-gray-400"></i>
                    </div>
                    <input type="password" id="confirm_password" name="confirm_password" required class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="请再次输入新密码">
                </div>
            </div>

            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                重置密码
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
                    <i class="bi bi-arrow-left me-1"></i> 返回登录
                </a>
            </p>
        </div>
    </div>
</div>

<script>
    let countdown = 0;
    function sendSmsCode() {
        if (countdown > 0) return;
        
        const phone = document.getElementById('phone').value;
        if (!phone) {
            alert('请输入手机号');
            return;
        }
        
        const btn = document.getElementById('sendCodeBtn');
        btn.disabled = true;
        btn.innerText = '发送中...';
        
        // Use 'forgot_password' type
        fetch('/api/send-sms', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ phone: phone, type: 'forgot_password' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                startCountdown();
            } else {
                alert(data.message || '发送失败');
                btn.disabled = false;
                btn.innerText = '获取验证码';
            }
        })
        .catch(err => {
            alert('网络错误');
            btn.disabled = false;
            btn.innerText = '获取验证码';
        });
    }

    function startCountdown() {
        countdown = 60;
        const btn = document.getElementById('sendCodeBtn');
        btn.disabled = true;
        btn.innerText = countdown + '秒后重发';
        
        const timer = setInterval(() => {
            countdown--;
            if (countdown <= 0) {
                clearInterval(timer);
                btn.disabled = false;
                btn.innerText = '获取验证码';
            } else {
                btn.innerText = countdown + '秒后重发';
            }
        }, 1000);
    }
</script>

</body>
</html>
