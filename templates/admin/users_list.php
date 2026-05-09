<?php
$title = '用户管理';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">用户管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <!-- Search -->
        <form class="d-flex" action="/admin/users" method="GET">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="搜索用户名或邮箱" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4" style="width: 5%;">ID</th>
                        <th style="width: 15%;">用户</th>
                        <th style="width: 20%;">手机号 / 邮箱</th>
                        <th style="width: 10%;">角色</th>
                        <th style="width: 10%;">积分</th>
                        <th style="width: 15%;">注册时间</th>
                        <th style="width: 15%;">最后登录</th>
                        <th class="text-end pe-4" style="width: 10%;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                暂无用户
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?php echo $user['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($user['avatar'])): ?>
                                            <img src="<?php echo htmlspecialchars($user['avatar']); ?>" class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                <i class="bi bi-person text-secondary"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($user['name']); ?></span>
                                    </div>
                                </td>
                                <td class="text-muted">
                                    <?php if (!empty($user['phone'])): ?>
                                        <div class="fw-bold text-dark"><i class="bi bi-phone me-1"></i><?php echo htmlspecialchars($user['phone']); ?></div>
                                    <?php endif; ?>
                                    <?php if (strpos($user['email'], '@mobile.user') === false): ?>
                                        <div class="<?php echo !empty($user['phone']) ? 'small text-muted' : ''; ?>">
                                            <?php if (empty($user['phone'])) echo '<i class="bi bi-envelope me-1"></i>'; ?>
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger text-white rounded-pill px-2 py-1 fw-normal">管理员</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary text-white rounded-pill px-2 py-1 fw-normal">用户</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-success"><?php echo number_format($user['points'] ?? 0); ?></td>
                                <td class="text-muted small">
                                    <?php echo date('Y-m-d', strtotime($user['created_at'])); ?>
                                </td>
                                <td class="text-muted small">
                                    <?php echo $user['last_login_at'] ? date('Y-m-d H:i', strtotime($user['last_login_at'])) : '-'; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="/admin/users/edit?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-white text-primary border shadow-sm" data-bs-toggle="tooltip" title="编辑">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" class="btn btn-sm btn-white text-danger border shadow-sm ms-1" onclick="deleteUser(<?php echo $user['id']; ?>)" data-bs-toggle="tooltip" title="删除">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white border-top-0 py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<script>
function deleteUser(id) {
    if (confirm('确定要删除此用户吗？此操作不可恢复！')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/users/delete?id=' + id;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?php echo Csrf::generate(); ?>';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
