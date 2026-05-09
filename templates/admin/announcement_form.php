<?php
$title = isset($announcement) ? '编辑公告' : '新增公告';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <h1 class="h2"><?php echo $title; ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/announcements" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">标题</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">类型</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="activity" <?php echo (isset($announcement['type']) && $announcement['type'] === 'activity') ? 'selected' : ''; ?>>活动</option>
                            <option value="announcement" <?php echo (isset($announcement['type']) && $announcement['type'] === 'announcement') ? 'selected' : ''; ?>>公告</option>
                            <option value="feature" <?php echo (isset($announcement['type']) && $announcement['type'] === 'feature') ? 'selected' : ''; ?>>新功能</option>
                            <option value="important" <?php echo (isset($announcement['type']) && $announcement['type'] === 'important') ? 'selected' : ''; ?>>重要</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">内容详情 (可选)</label>
                        <textarea class="form-control" id="content" name="content" rows="5"><?php echo htmlspecialchars($announcement['content'] ?? ''); ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="published_at" class="form-label">发布时间</label>
                            <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="<?php echo isset($announcement['published_at']) ? date('Y-m-d\TH:i', strtotime($announcement['published_at'])) : date('Y-m-d\TH:i'); ?>">
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" <?php echo (!isset($announcement) || $announcement['status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">立即发布</label>
                    </div>

                    <button type="submit" class="btn btn-primary">保存</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
