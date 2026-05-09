<?php
$title = '文章管理';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">文章管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/articles/create" class="btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> 新建文章
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4" style="width: 5%;">ID</th>
                        <th style="width: 35%;">标题</th>
                        <th style="width: 15%;">分类</th>
                        <th style="width: 10%;">状态</th>
                        <th style="width: 10%;">浏览量</th>
                        <th style="width: 15%;">发布时间</th>
                        <th class="text-end pe-4" style="width: 10%;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                暂无文章
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?php echo $article['id']; ?></td>
                                <td>
                                    <div class="fw-bold text-dark mb-1">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                        <a href="/<?php echo $article['slug'] ?? $article['id']; ?>.html" target="_blank" class="ms-1 text-muted text-decoration-none" data-bs-toggle="tooltip" title="预览文章">
                                            <i class="bi bi-box-arrow-up-right small"></i>
                                        </a>
                                    </div>
                                    <?php if ($article['geo_region'] !== 'CN'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-globe2 me-1"></i><?php echo $article['geo_region']; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($article['category_name'])): ?>
                                        <span class="badge bg-light text-secondary border fw-normal">
                                            <?php echo htmlspecialchars($article['category_name']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">未分类</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($article['status'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-normal">
                                            <i class="bi bi-check-circle-fill me-1" style="font-size: 0.7em;"></i>已发布
                                        </span>
                                    <?php elseif ($article['status'] == 0): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 fw-normal">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 0.7em;"></i>草稿
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 fw-normal">其他</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted">
                                    <i class="bi bi-eye me-1"></i><?php echo $article['views']; ?>
                                </td>
                                <td class="text-muted small">
                                    <?php echo $article['published_at'] ? date('Y-m-d H:i', strtotime($article['published_at'])) : '-'; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="/admin/articles/edit?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-white text-primary border shadow-sm" data-bs-toggle="tooltip" title="编辑">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-white text-danger border shadow-sm ms-1" onclick="deleteArticle(<?php echo $article['id']; ?>)" data-bs-toggle="tooltip" title="删除">
                                            <i class="bi bi-trash"></i>
                                        </button>
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
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link border-0 text-secondary" href="?page=<?php echo $page - 1; ?>"><i class="bi bi-chevron-left"></i></a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link border-0 rounded-circle mx-1 <?php echo $i == $page ? 'bg-primary text-white shadow-sm' : 'text-secondary'; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link border-0 text-secondary" href="?page=<?php echo $page + 1; ?>"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })


<script>
function deleteArticle(id) {
    if (confirm('确定要删除这篇文章吗？此操作不可恢复。')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/articles/delete';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = 'csrf_token';
        csrf.value = '<?php echo Csrf::generate(); ?>';

        form.appendChild(input);
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>