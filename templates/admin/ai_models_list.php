<?php
$title = 'AI 模型管理';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 fw-bold text-gray-800">AI 模型配置</h1>
        <p class="text-muted small mb-0">管理用于内容生成的 AI 模型参数</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/ai-models/create" class="btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> 添加 AI 模型
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3 border-bottom-0">名称</th>
                        <th class="py-3 border-bottom-0">提供商</th>
                        <th class="py-3 border-bottom-0">模型名 (API)</th>
                        <th class="py-3 border-bottom-0">Base URL</th>
                        <th class="py-3 border-bottom-0">状态</th>
                        <th class="pe-4 py-3 border-bottom-0 text-end">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($models)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="bi bi-cpu fs-1 d-block mb-3 opacity-50"></i>
                                <p class="mb-0">暂无 AI 模型配置</p>
                                <a href="/admin/ai-models/create" class="btn btn-sm btn-primary mt-3">立即添加</a>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($models as $model): ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($model['name']); ?></div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2">
                                    <i class="bi bi-cloud me-1"></i><?php echo htmlspecialchars($model['provider']); ?>
                                </span>
                            </td>
                            <td class="py-3">
                                <code class="bg-light px-2 py-1 rounded text-dark border small"><?php echo htmlspecialchars($model['model_name']); ?></code>
                            </td>
                            <td class="py-3">
                                <small class="text-muted font-monospace"><?php echo htmlspecialchars($model['base_url']); ?></small>
                            </td>
                            <td class="py-3">
                                <?php if ($model['is_active'] == 1): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">
                                        <span class="d-inline-block rounded-circle bg-success me-1" style="width: 6px; height: 6px;"></span>
                                        启用
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">
                                        <span class="d-inline-block rounded-circle bg-secondary me-1" style="width: 6px; height: 6px;"></span>
                                        禁用
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <div class="btn-group">
                                    <a href="/admin/ai-models/edit/<?php echo $model['id']; ?>" class="btn btn-sm btn-white border text-primary shadow-sm" title="编辑">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-white border text-danger shadow-sm" onclick="deleteModel(<?php echo $model['id']; ?>)" title="删除">
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
</div>

<script>
function deleteModel(id) {
    if (confirm('确定要删除这个模型配置吗？关联的生成方案可能会失效。')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/ai-models/delete';
        
        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id';
        inputId.value = id;
        
        const inputCsrf = document.createElement('input');
        inputCsrf.type = 'hidden';
        inputCsrf.name = 'csrf_token';
        inputCsrf.value = '<?php echo Csrf::generate(); ?>';
        
        form.appendChild(inputId);
        form.appendChild(inputCsrf);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
