<?php
// Version update notice for admin dashboard
$pdo = \Database::getInstance()->getConnection();
$stmt = $pdo->query("SELECT version, content FROM system_updates ORDER BY release_date DESC LIMIT 1");
$latestUpdate = $stmt->fetch();
$currentVer = $settings['current_version'] ?? '1.0.0';

if ($latestUpdate && version_compare($latestUpdate['version'], $currentVer, '>')): ?>
<div class="alert alert-warning bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 shadow-sm mb-4">
    <div class="d-flex align-items-center">
        <div class="display-6 me-3"><i class="bi bi-arrow-up-circle-fill text-warning"></i></div>
        <div class="flex-grow-1">
            <h4 class="alert-heading fw-bold mb-1">新版本可用：v<?php echo $latestUpdate['version']; ?></h4>
            <p class="mb-0 text-secondary small"><?php echo strip_tags(mb_substr($latestUpdate['content'], 0, 150)); ?>...</p>
        </div>
        <a href="/admin/update" class="btn btn-warning btn-sm fw-bold px-3">查看更新</a>
    </div>
</div>
<?php else: ?>
<div class="alert alert-success bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 shadow-sm mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
        <div>
            <span class="fw-bold">系统已是最新版本 v<?php echo $currentVer; ?></span>
            <a href="/admin/update" class="ms-2 text-decoration-none small fw-bold">查看更新日志 →</a>
        </div>
    </div>
</div>
<?php endif; ?>
