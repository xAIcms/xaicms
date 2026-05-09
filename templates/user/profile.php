<?php ob_start(); ?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900">编辑个人资料</h3>
            <p class="text-sm text-gray-500">更新您的基本账户信息</p>
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

            <form method="POST" action="/user/profile" class="space-y-6">
                <?php if (class_exists('Csrf')) echo Csrf::input(); ?>
                
                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">手机号码</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-phone text-gray-400"></i>
                        </div>
                        <input type="text" value="<?php echo htmlspecialchars($user['phone'] ?? '未绑定'); ?>" disabled class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button type="button" onclick="document.getElementById('changePhoneModal').classList.remove('hidden')" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                <?php echo !empty($user['phone']) ? '更换手机号' : '绑定手机号'; ?>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Email (Read-only) - Only show if not fake mobile email -->
                <?php if (!str_ends_with($user['email'], '@mobile.user')): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">邮箱地址</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-envelope text-gray-400"></i>
                        </div>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">邮箱地址暂不支持直接修改，如需修改请联系管理员。</p>
                </div>
                <?php endif; ?>

                <!-- Username -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">用户名</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-person text-gray-400"></i>
                        </div>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm transition-all">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end">
                    <button type="submit" class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        保存更改
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Phone Modal -->
<div id="changePhoneModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4">绑定/更换手机号</h3>
            <form id="bindPhoneForm" onsubmit="return handleBindPhone(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">新手机号</label>
                    <input type="tel" id="newPhone" name="phone" required pattern="^1[3-9]\d{9}$" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" placeholder="请输入11位手机号">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">验证码</label>
                    <div class="flex gap-2">
                        <input type="text" id="verifyCode" name="code" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" placeholder="6位验证码">
                        <button type="button" id="sendCodeBtn" onclick="sendBindCode()" class="whitespace-nowrap px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            发送验证码
                        </button>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('changePhoneModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors">取消</button>
                    <button type="submit" id="submitBindBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">确认绑定</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let countdown = 0;
let timer = null;

function sendBindCode() {
    const phone = document.getElementById('newPhone').value;
    if (!phone || !/^1[3-9]\d{9}$/.test(phone)) {
        alert('请输入正确的手机号');
        return;
    }

    if (countdown > 0) return;

    const btn = document.getElementById('sendCodeBtn');
    btn.disabled = true;
    btn.textContent = '发送中...';

    fetch('/api/send-sms', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            phone: phone,
            type: 'bind'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            startCountdown(60);
            alert('验证码已发送');
        } else {
            alert(data.message || '发送失败');
            btn.disabled = false;
            btn.textContent = '发送验证码';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('系统错误，请重试');
        btn.disabled = false;
        btn.textContent = '发送验证码';
    });
}

function startCountdown(seconds) {
    countdown = seconds;
    const btn = document.getElementById('sendCodeBtn');
    btn.disabled = true;
    
    timer = setInterval(() => {
        countdown--;
        btn.textContent = `${countdown}s 后重试`;
        
        if (countdown <= 0) {
            clearInterval(timer);
            btn.disabled = false;
            btn.textContent = '发送验证码';
        }
    }, 1000);
}

function handleBindPhone(event) {
    event.preventDefault();
    
    const phone = document.getElementById('newPhone').value;
    const code = document.getElementById('verifyCode').value;
    const btn = document.getElementById('submitBindBtn');
    
    if (!phone || !code) {
        alert('请填写完整信息');
        return false;
    }
    
    btn.disabled = true;
    btn.textContent = '提交中...';
    
    fetch('/user/bind-phone', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            phone: phone,
            code: code,
            csrf_token: '<?php echo class_exists("Csrf") ? Csrf::generate() : ""; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('绑定成功');
            window.location.reload();
        } else {
            alert(data.message || '绑定失败');
            btn.disabled = false;
            btn.textContent = '确认绑定';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('系统错误，请重试');
        btn.disabled = false;
        btn.textContent = '确认绑定';
    });
    
    return false;
}
</script>

<?php 
$content = ob_get_clean();
$pageTitle = '个人资料';
require __DIR__ . '/layout.php';
?>
