<?php
$title = '编辑用户';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">编辑用户</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/users" class="btn btn-sm btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> 返回列表
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill me-2 text-primary"></i>基本信息</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/users/update?id=<?php echo $user['id']; ?>">
                    <?php if (class_exists('Csrf')) echo Csrf::input(); ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">用户名</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">手机号码</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="输入手机号">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">邮箱地址</label>
                        <input type="email" class="form-control <?php echo strpos($user['email'], '@mobile.user') !== false ? 'text-muted' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        <?php if (strpos($user['email'], '@mobile.user') !== false): ?>
                            <div class="form-text text-warning"><i class="bi bi-info-circle"></i> 这是系统自动生成的伪邮箱（用户未绑定邮箱）</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">修改密码 <small class="text-muted">（留空则不修改）</small></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="输入新密码">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">角色</label>
                            <select class="form-select" id="role" name="role">
                                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>普通用户</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>管理员</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="points" class="form-label">积分</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-coin"></i></span>
                                <input type="number" class="form-control" id="points" name="points" value="<?php echo (int)($user['points'] ?? 0); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> 保存更改
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-info"></i>账号信息</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">注册时间</span>
                        <span class="fw-bold"><?php echo $user['created_at']; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">最后登录</span>
                        <span><?php echo $user['last_login_at'] ?? '从未登录'; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">登录 IP</span>
                        <span><?php echo $user['login_ip'] ?? '-'; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
