<?php
$title = '平台公告管理';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <h1 class="h2">平台公告</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/announcements/create" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> 新增公告
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>标题</th>
                        <th>类型</th>
                        <th>状态</th>
                        <th>发布时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($announcements)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">暂无公告</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($announcements as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td>
                                    <span class="badge <?php echo Announcement::getTypeColor($item['type']); ?>">
                                        <?php echo Announcement::getTypeLabel($item['type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['status'] == 1): ?>
                                        <span class="badge bg-success">已发布</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">草稿</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $item['published_at']; ?></td>
                                <td>
                                    <a href="/admin/announcements/edit/<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-2">编辑</a>
                                    <form action="/admin/announcements/delete/<?php echo $item['id']; ?>" method="POST" class="d-inline" onsubmit="return confirm('确定要删除吗？');">
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
