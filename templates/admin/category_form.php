<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-body-emphasis fw-bold"><?php echo $isEdit ? '编辑分类' : '新建分类'; ?></h1>
        <p class="text-muted mb-0">
            <i class="bi bi-folder me-1"></i> 管理文章分类结构
        </p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/categories" class="btn btn-light shadow-sm border">
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
                <h5 class="card-title mb-0 fw-bold">分类详情</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="">
                    <?php echo Csrf::input(); ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">分类名称</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" required placeholder="例如：科技新闻">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Slug (URL别名)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">/</span>
                            <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($category['slug'] ?? ''); ?>" placeholder="tech-news">
                        </div>
                        <div class="form-text">留空将根据名称自动生成，仅支持小写字母、数字和连字符。</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">父级分类</label>
                        <select name="parent_id" class="form-select">
                            <option value="0">无 (顶级分类)</option>
                            <?php 
                            // Helper to render options
                            function renderOptions($items, $parentId = 0, $level = 0, $selectedId = 0, $excludeId = null) {
                                foreach ($items as $item) {
                                    if ($item['parent_id'] == $parentId) {
                                        // Skip self and children if editing
                                        if ($excludeId && $item['id'] == $excludeId) continue;
                                        
                                        $selected = ($item['id'] == $selectedId) ? 'selected' : '';
                                        $prefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                        if ($level > 0) $prefix .= '└─ ';
                                        
                                        echo '<option value="' . $item['id'] . '" ' . $selected . '>' . $prefix . htmlspecialchars($item['name']) . '</option>';
                                        
                                        renderOptions($items, $item['id'], $level + 1, $selectedId, $excludeId);
                                    }
                                }
                            }
                            renderOptions($allCategories ?? [], 0, 0, $category['parent_id'] ?? 0, $category['id'] ?? null);
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">描述</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="简要描述该分类的内容..."><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">排序</label>
                        <input type="number" name="sort_order" class="form-control" value="<?php echo htmlspecialchars($category['sort_order'] ?? '0'); ?>">
                        <div class="form-text">数值越小排序越靠前。</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="/admin/categories" class="btn btn-light border">取消</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> 保存分类
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
