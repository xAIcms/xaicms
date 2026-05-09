<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">标签管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/tags/create" class="btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> 新建标签
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-10 text-danger" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4" style="width: 35%;">标签名称</th>
                        <th style="width: 30%;">Slug</th>
                        <th style="width: 15%;">文章数</th>
                        <th class="text-end pe-4" style="width: 20%;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tags)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-tags fs-1 d-block mb-2"></i>
                            暂无标签
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($tags as $tag): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border fw-normal">
                                    <i class="bi bi-tag me-1 text-secondary"></i><?php echo htmlspecialchars($tag['name']); ?>
                                </span>
                            </td>
                            <td class="text-secondary"><?php echo htmlspecialchars($tag['slug']); ?></td>
                            <td><span class="badge bg-secondary rounded-pill px-2"><?php echo $tag['article_count'] ?? 0; ?></span></td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="/admin/tags/edit/<?php echo $tag['id']; ?>" class="btn btn-sm btn-white text-primary border shadow-sm" data-bs-toggle="tooltip" title="编辑"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="btn btn-sm btn-white text-danger border shadow-sm ms-1" onclick="deleteTag(<?php echo $tag['id']; ?>)" data-bs-toggle="tooltip" title="删除"><i class="bi bi-trash"></i></button>
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
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link border-0 text-secondary" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                if ($start > 1): ?>
                    <li class="page-item"><a class="page-link border-0 text-secondary" href="?page=1">1</a></li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled"><span class="page-link border-0 text-secondary">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link border-0 rounded-circle mx-1 <?php echo $i == $page ? 'bg-primary text-white shadow-sm' : 'text-secondary'; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link border-0 text-secondary">...</span></li>
                    <?php endif; ?>
                    <li class="page-item"><a class="page-link border-0 text-secondary" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a></li>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link border-0 text-secondary" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
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


<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
