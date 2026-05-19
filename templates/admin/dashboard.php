<?php
$title = __('Dashboard');
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 fw-bold text-gray-800"><?php echo __('Dashboard'); ?></h1>
        <p class="text-muted small mb-0"><?php echo __('System overview and statistics', '系统运行概览与统计'); ?></p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2 shadow-sm">
            <button type="button" class="btn btn-sm btn-light border">
                <i class="bi bi-share me-1"></i> <?php echo __("Share", "分享"); ?>
            </button>
            <button type="button" class="btn btn-sm btn-light border">
                <i class="bi bi-download me-1"></i> <?php echo __("Export", "导出"); ?>
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-white border shadow-sm dropdown-toggle">
            <i class="bi bi-calendar-event me-1"></i>
            <?php echo __("This Week", "本周"); ?>
        </button>
    </div>
</div>

<div class="alert alert-primary bg-indigo-50 border-0 text-indigo-900 rounded-3 shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-center">
        <div class="display-6 me-3"><i class="bi bi-emoji-smile"></i></div>
        <div>
            <h4 class="alert-heading fw-bold mb-1">欢迎回来，<?php echo htmlspecialchars($_SESSION['user_name'] ?? '管理员'); ?>！</h4>
            <p class="mb-0 text-indigo-800">今天是 <?php echo date('Y年m月d日'); ?>。当前计划：<strong><?php echo Plan::planName(); ?></strong><?php if(Plan::current()==="free"): ?> · <a href="/admin/upgrade" class="text-warning fw-bold">升级解锁更多功能 →</a><?php endif; ?></p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/version_notice.php'; ?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-3 card-hover">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1 tracking-wide">总文章数</h6>
                        <h2 class="mb-0 fw-bold text-dark display-6"><?php echo $stats['total_articles'] ?? 0; ?></h2>
                    </div>
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-file-text fs-3"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-3 px-4 rounded-bottom-3">
                <a href="/admin/articles" class="text-primary text-decoration-none small fw-bold d-flex align-items-center">
                    管理文章 <i class="bi bi-arrow-right ms-auto"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-3 card-hover">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1 tracking-wide">总阅读量</h6>
                        <h2 class="mb-0 fw-bold text-dark display-6"><?php echo $stats['total_views'] ?? 0; ?></h2>
                    </div>
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-eye fs-3"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-3 px-4 rounded-bottom-3">
                <span class="text-muted small fw-bold d-flex align-items-center">
                    <i class="bi bi-graph-up me-2"></i> 实时统计
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-3 card-hover">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1 tracking-wide">API 调用</h6>
                        <h2 class="mb-0 fw-bold text-dark display-6"><?php echo $stats['api_calls'] ?? 0; ?></h2>
                    </div>
                    <div class="icon-shape bg-info bg-opacity-10 text-info rounded-3 p-3">
                        <i class="bi bi-code-slash fs-3"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-3 px-4 rounded-bottom-3">
                <a href="/admin/api" class="text-info text-decoration-none small fw-bold d-flex align-items-center">
                    API 管理 <i class="bi bi-arrow-right ms-auto"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100 rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-clock-history me-2 text-primary"></i>最近文章
                    </h5>
                    <a href="/admin/articles" class="btn btn-sm btn-light text-primary fw-medium rounded-pill px-3">查看全部</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php 
                    // Fetch a few recent articles for dashboard (using getAll to include drafts)
                    $recentArticles = Article::getAll(5);
                    if (empty($recentArticles)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                            <p>暂无文章</p>
                            <a href="/admin/articles/create" class="btn btn-sm btn-primary mt-2">写一篇</a>
                        </div>
                    <?php else: 
                        foreach ($recentArticles as $article): ?>
                        <a href="/admin/articles/edit/<?php echo $article['id']; ?>" class="list-group-item list-group-item-action border-0 px-4 py-3 d-flex justify-content-between align-items-center transition-hover">
                            <div class="d-flex align-items-center overflow-hidden">
                                <div class="avatar bg-light rounded-3 p-2 me-3 text-secondary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="bi bi-file-text fs-5"></i>
                                </div>
                                <div class="text-truncate" style="max-width: 400px;">
                                    <h6 class="mb-1 text-dark fw-bold text-truncate"><?php echo htmlspecialchars($article['title']); ?></h6>
                                    <small class="text-muted d-flex align-items-center">
                                        <?php if ($article['status'] == 0): ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary me-2 rounded-pill px-2">草稿</span>
                                        <?php else: ?>
                                            <span class="badge bg-success bg-opacity-10 text-success me-2 rounded-pill px-2">已发布</span>
                                        <?php endif; ?>
                                        <i class="bi bi-clock me-1"></i> <?php echo date('Y-m-d H:i', strtotime($article['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                            <div class="text-end ps-3">
                                <i class="bi bi-chevron-right text-muted small"></i>
                            </div>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-lightning-charge me-2 text-warning"></i>快速操作
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-3">
                    <a href="/admin/articles/create" class="btn btn-primary btn-lg text-start shadow-sm p-3 rounded-3 d-flex align-items-center justify-content-between group-hover">
                        <span><i class="bi bi-pencil-square me-2"></i> 写文章</span>
                        <i class="bi bi-arrow-right opacity-0 group-hover-opacity transition-all"></i>
                    </a>
                    <a href="/admin/settings" class="btn btn-light btn-lg text-start text-dark border-0 shadow-sm bg-gray-100 p-3 rounded-3 d-flex align-items-center justify-content-between group-hover">
                        <span><i class="bi bi-gear me-2"></i> 系统设置</span>
                        <i class="bi bi-arrow-right opacity-0 group-hover-opacity transition-all"></i>
                    </a>
                    <a href="/" target="_blank" class="btn btn-light btn-lg text-start text-dark border-0 shadow-sm bg-gray-100 p-3 rounded-3 d-flex align-items-center justify-content-between group-hover">
                    <a href="/" target="_blank" class="btn btn-light btn-lg text-start text-dark border-0 shadow-sm bg-gray-100 p-3 rounded-3 d-flex align-items-center justify-content-between group-hover">
                        <span><i class="bi bi-box-arrow-up-right me-2"></i> 查看站点</span>
                        <i class="bi bi-arrow-right opacity-0 group-hover-opacity transition-all"></i>
                    </a>
                    <a href="/admin/upgrade" class="btn btn-warning btn-lg text-start shadow-sm p-3 rounded-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-arrow-up-circle me-2"></i> 升级计划 <span class="badge bg-dark ms-2">Free</span></span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="/admin/marketplace" class="btn btn-light btn-lg text-start text-dark border-0 shadow-sm bg-gray-100 p-3 rounded-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-shop me-2"></i> 插件 & 模板市场</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="/admin/developer" class="btn btn-light btn-lg text-start text-dark border-0 shadow-sm bg-gray-100 p-3 rounded-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-code-slash me-2"></i> 开发者中心</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="/admin/docs" class="btn btn-light btn-lg text-start text-dark border-0 shadow-sm bg-gray-100 p-3 rounded-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-book me-2"></i> 开发文档</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    </a>
                </div>
                
                <hr class="my-4 opacity-10">
                
                <div class="bg-light p-4 rounded-3">
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted tracking-wide">系统状态</h6>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">服务器时间</span>
                        <span class="fw-bold text-dark font-monospace"><?php echo date('H:i'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">PHP 版本</span>
                        <span class="fw-bold text-dark font-monospace"><?php echo PHP_VERSION; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php do_action('admin_dashboard_widgets'); ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
