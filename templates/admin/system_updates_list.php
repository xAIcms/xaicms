<?php
$title = '系统更新管理';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <h1 class="h2">系统更新日志</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/system-updates/create" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> 新增更新记录
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>版本号</th>
                        <th>更新内容</th>
                        <th>发布日期</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($updates)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">暂无更新记录</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($updates as $item): ?>
                            <tr>
                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($item['version']); ?></span></td>
                                <td><?php echo htmlspecialchars(mb_strimwidth($item['content'], 0, 100, '...')); ?></td>
                                <td><?php echo $item['release_date']; ?></td>
                                <td>
                                    <a href="/admin/system-updates/edit/<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-2">编辑</a>
                                    <form action="/admin/system-updates/delete/<?php echo $item['id']; ?>" method="POST" class="d-inline" onsubmit="return confirm('确定要删除吗？');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
