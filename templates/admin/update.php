<?php
// templates/admin/update.php — Version history & changelog
$title = '系统更新';
ob_start();

$currentVer = $settings['current_version'] ?? '1.0.0';
$pdo = Database::getInstance()->getConnection();
$updates = $pdo->query("SELECT * FROM system_updates ORDER BY release_date DESC")->fetchAll();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-gray-800">系统更新</h1>
        <p class="text-muted small mb-0">当前版本：<strong>v<?php echo $currentVer; ?></strong></p>
    </div>
    <a href="https://github.com/xAIcms/xaicms/releases" target="_blank" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-github me-1"></i> GitHub Releases
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-4">
                <h6 class="text-muted text-uppercase small">当前版本</h6>
                <h2 class="mb-0 fw-bold">v<?php echo $currentVer; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-4">
                <h6 class="text-muted text-uppercase small">最新版本</h6>
                <h2 class="mb-0 fw-bold text-indigo">v<?php echo $updates ? $updates[0]['version'] : $currentVer; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-4">
                <h6 class="text-muted text-uppercase small">状态</h6>
                <?php $hasNew = $updates && version_compare($updates[0]['version'], $currentVer, '>'); ?>
                <h2 class="mb-0 fw-bold <?php echo $hasNew ? 'text-warning' : 'text-success'; ?>">
                    <?php echo $hasNew ? '有更新可用' : '已是最新'; ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Changelog -->
<h4 class="fw-bold mb-3"><i class="bi bi-journal-text me-2"></i>更新日志</h4>
<div class="timeline">
    <?php foreach ($updates as $u): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-indigo fs-6 me-3">v<?php echo $u['version']; ?></span>
                <small class="text-muted"><?php echo $u['release_date']; ?></small>
            </div>
            <div class="ps-2">
                <?php echo $u['content']; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
