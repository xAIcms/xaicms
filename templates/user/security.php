<?php ob_start(); ?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900">账户安全</h3>
            <p class="text-sm text-gray-500">修改您的登录密码</p>
        </div>
        
        <div class="p-6 md:p-8">
            <?php if (!empty($success)): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r">
                    <div class="flex items-center">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        <p class="text-sm text-green-700"><?php echo htmlspecialchars($success); ?></p>
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

            <form method="POST" action="/user/security" class="space-y-6">
                <?php if (class_exists('Csrf')) echo Csrf::input(); ?>
                
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">当前密码</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-key text-gray-400"></i>
                        </div>
                        <input type="password" id="current_password" name="current_password" required class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm">
                    </div>
                </div>

                <div class="border-t border-gray-100 my-2"></div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">新密码</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="new_password" name="new_password" required minlength="6" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm" placeholder="至少 6 位字符">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">确认新密码</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock-fill text-gray-400"></i>
                        </div>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm" placeholder="再次输入新密码">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end">
                    <button type="submit" class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        更新密码
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$pageTitle = '安全设置';
require __DIR__ . '/layout.php';
?>
