<?php
$title = isset($update) ? '编辑更新记录' : '新增更新记录';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <h1 class="h2"><?php echo $title; ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/system-updates" class="btn btn-sm btn-outline-secondary">
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
                        <label for="version" class="form-label">版本号</label>
                        <input type="text" class="form-control" id="version" name="version" value="<?php echo htmlspecialchars($update['version'] ?? ''); ?>" placeholder="例如 v1.0.0" required>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">更新内容</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($update['content'] ?? ''); ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="release_date" class="form-label">发布日期</label>
                            <input type="date" class="form-control" id="release_date" name="release_date" value="<?php echo isset($update['release_date']) ? $update['release_date'] : date('Y-m-d'); ?>" required>
                        </div>
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
