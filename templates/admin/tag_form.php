<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-body-emphasis fw-bold"><?php echo $isEdit ? '编辑标签' : '新建标签'; ?></h1>
        <p class="text-muted mb-0">
            <i class="bi bi-tags me-1"></i> 管理文章标签
        </p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/tags" class="btn btn-light shadow-sm border">
            <i class="bi bi-arrow-left me-1"></i> 返回列表
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-danger-subtle" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">标签详情</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="">
                    <?php echo Csrf::input(); ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">标签名称</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($tag['name'] ?? ''); ?>" required placeholder="例如：人工智能">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Slug (URL别名)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">/tag/</span>
                            <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($tag['slug'] ?? ''); ?>" placeholder="ai">
                        </div>
                        <div class="form-text">留空将根据名称自动生成。</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="/admin/tags" class="btn btn-light border">取消</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> 保存标签
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
