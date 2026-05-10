<?php
$title = '爬虫日志';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-body-emphasis fw-bold">爬虫日志</h1>
        <p class="text-muted mb-0">
            <i class="bi bi-robot me-1"></i> 监控搜索引擎爬虫的访问记录
        </p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-light shadow-sm border">
            <i class="bi bi-download me-1"></i> 导出记录
        </button>
    </div>
</div>

<!-- 统计卡片 -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-primary bg-gradient text-white overflow-hidden position-relative">
            <div class="card-body position-relative z-1">
                <h6 class="card-title text-white-50 mb-2">近7天总访问</h6>
                <div class="d-flex align-items-baseline">
                    <h2 class="display-6 fw-bold mb-0"><?php echo number_format($stats['total']); ?></h2>
                    <span class="ms-2 small text-white-50">次</span>
                </div>
            </div>
            <i class="bi bi-graph-up position-absolute bottom-0 end-0 display-1 text-white opacity-10 me-n3 mb-n2"></i>
        </div>
    </div>
    
    <?php 
    $colors = ['success', 'info', 'warning', 'danger'];
    $i = 0;
    foreach (array_slice($stats['by_bot'], 0, 3) as $index => $bot): 
        $color = $colors[$i % 4];
        $percent = $stats['total'] > 0 ? round(($bot['count'] / $stats['total']) * 100, 1) : 0;
    ?>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="card-subtitle text-muted mb-1"><?php echo htmlspecialchars($bot['bot_name']); ?></h6>
                        <h3 class="card-title fw-bold mb-0"><?php echo number_format($bot['count']); ?></h3>
                    </div>
                    <div class="icon-shape bg-<?php echo $color; ?> bg-opacity-10 text-<?php echo $color; ?> rounded-3 p-2">
                        <i class="bi bi-robot"></i>
                    </div>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" style="width: <?php echo $percent; ?>%"></div>
                </div>
                <div class="mt-2 small text-muted"><?php echo $percent; ?>% 的访问占比</div>
            </div>
        </div>
    </div>
    <?php 
        $i++;
    endforeach; 
    ?>
</div>

<!-- 日志列表 -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">访问明细</h5>
            <span class="badge bg-light text-dark border">共 <?php echo number_format($total ?? 0); ?> 条</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 ps-4">时间</th>
                    <th class="border-0">爬虫名称</th>
                    <th class="border-0">类型</th>
                    <th class="border-0">访问路径</th>
                    <th class="border-0">IP地址</th>
                    <th class="border-0 text-end pe-4">状态码</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <div class="py-4">
                            <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                            <p class="mb-0">暂无爬虫记录</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="ps-4 text-nowrap text-muted small">
                            <?php echo date('Y-m-d H:i:s', strtotime($log['visited_at'])); ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge rounded-pill" style="background-color: #e0e7ff; color: #4f46e5;">
                                    <?php echo htmlspecialchars($log['bot_name']); ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="small text-muted"><?php echo htmlspecialchars($log['bot_type']); ?></span>
                        </td>
                        <td class="text-break" style="min-width: 200px; max-width: 400px;">
                            <a href="<?php echo htmlspecialchars($log['path']); ?>" target="_blank" class="text-decoration-none text-dark d-block text-truncate font-monospace small" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($log['path']); ?>">
                                <?php echo htmlspecialchars($log['path']); ?>
                            </a>
                        </td>
                        <td>
                            <span class="font-monospace small text-muted"><?php echo htmlspecialchars($log['ip_address']); ?></span>
                        </td>
                        <td class="text-end pe-4">
                            <?php 
                            $code = $log['status_code'];
                            $badgeClass = match (true) {
                                $code >= 200 && $code < 300 => 'success',
                                $code >= 300 && $code < 400 => 'info',
                                $code >= 400 && $code < 500 => 'warning',
                                default => 'danger'
                            };
                            ?>
                            <span class="badge bg-<?php echo $badgeClass; ?>-subtle text-<?php echo $badgeClass; ?> border border-<?php echo $badgeClass; ?>-subtle rounded-pill">
                                <?php echo $code; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- 分页 -->
    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white border-0 py-3">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link rounded-circle mx-1 border-0 bg-light text-dark" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                
                if ($start > 1): ?>
                    <li class="page-item">
                        <a class="page-link rounded-circle mx-1 border-0 bg-light text-dark" href="?page=1">1</a>
                    </li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled"><span class="page-link border-0 text-muted">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item">
                    <a class="page-link rounded-circle mx-1 border-0 <?php echo $i === $page ? 'active bg-primary text-white shadow-sm' : 'bg-light text-dark'; ?>" href="?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link border-0 text-muted">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link rounded-circle mx-1 border-0 bg-light text-dark" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                    </li>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link rounded-circle mx-1 border-0 bg-light text-dark" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>