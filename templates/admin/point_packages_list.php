<?php
$title = '积分套餐管理';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">积分套餐管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/point-packages/create" class="btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> 新建套餐
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>套餐名称</th>
                        <th>价格 (CNY)</th>
                        <th>基础积分</th>
                        <th>赠送比例</th>
                        <th>实际到账</th>
                        <th>状态</th>
                        <th class="text-end pe-4">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($packages)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">暂无积分套餐</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($packages as $pkg): ?>
                            <tr>
                                <td class="ps-4"><?php echo $pkg['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($pkg['name']); ?></td>
                                <td class="text-danger fw-bold">¥<?php echo number_format($pkg['price'], 2); ?></td>
                                <td><?php echo number_format($pkg['points']); ?></td>
                                <td>
                                    <?php if ($pkg['bonus_percent'] > 0): ?>
                                        <span class="badge bg-success">+<?php echo $pkg['bonus_percent']; ?>%</span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-primary">
                                    <?php 
                                    $total = $pkg['points'] + floor($pkg['points'] * ($pkg['bonus_percent'] / 100));
                                    echo number_format($total); 
                                    ?>
                                </td>
                                <td>
                                    <?php if ($pkg['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">上架中</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">已下架</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="/admin/point-packages/edit?id=<?php echo $pkg['id']; ?>" class="btn btn-sm btn-white text-primary border shadow-sm" title="编辑">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-white text-danger border shadow-sm ms-1" onclick="deletePackage(<?php echo $pkg['id']; ?>)" title="删除">
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

<form id="deleteForm" method="POST" action="" style="display: none;">
    <?php if (class_exists('Csrf')) echo Csrf::input(); ?>
</form>

<script>
function deletePackage(id) {
    if (confirm('确定要删除这个套餐吗？')) {
        var form = document.getElementById('deleteForm');
        form.action = '/admin/point-packages/delete?id=' + id;
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
