<?php
$title = isset($package) ? '编辑套餐' : '新建套餐';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800"><?php echo $title; ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/point-packages" class="btn btn-sm btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> 返回列表
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="POST">
                    <?php if (class_exists('Csrf')) echo Csrf::input(); ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">套餐名称</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($package['name'] ?? ''); ?>" required placeholder="例如：超值入门包">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">价格 (CNY)</label>
                            <div class="input-group">
                                <span class="input-group-text">¥</span>
                                <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($package['price'] ?? ''); ?>" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="points" class="form-label">基础积分</label>
                            <input type="number" class="form-control" id="points" name="points" value="<?php echo htmlspecialchars($package['points'] ?? ''); ?>" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bonus_percent" class="form-label">赠送比例 (%)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="bonus_percent" name="bonus_percent" value="<?php echo htmlspecialchars($package['bonus_percent'] ?? '0'); ?>" min="0" step="1">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">例如输入 25，表示赠送 25% 的积分。总积分 = 基础积分 * 1.25。</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo (!isset($package) || $package['is_active']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">立即上架</label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> 保存套餐
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">预览效果</h5>
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0 text-center" id="preview-name">套餐名称</h6>
                    </div>
                    <div class="card-body text-center py-4">
                        <h3 class="fw-bold text-primary mb-0" id="preview-points">0 积分</h3>
                        <div class="badge bg-warning text-dark mb-3" id="preview-bonus" style="display:none;">+送 0%</div>
                        <h4 class="text-danger fw-bold mb-0">¥ <span id="preview-price">0.00</span></h4>
                    </div>
                </div>
                <p class="small text-muted mb-0">
                    <i class="bi bi-info-circle me-1"></i> 
                    输入数值时，左侧预览卡片会自动更新，展示用户在前端看到的大致效果。
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['name', 'price', 'points', 'bonus_percent'];
    inputs.forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });
    
    function updatePreview() {
        const name = document.getElementById('name').value || '套餐名称';
        const price = parseFloat(document.getElementById('price').value) || 0;
        const points = parseInt(document.getElementById('points').value) || 0;
        const bonus = parseFloat(document.getElementById('bonus_percent').value) || 0;
        
        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-price').textContent = price.toFixed(2);
        
        const totalPoints = Math.floor(points * (1 + bonus / 100));
        document.getElementById('preview-points').textContent = totalPoints.toLocaleString() + ' 积分';
        
        const bonusEl = document.getElementById('preview-bonus');
        if (bonus > 0) {
            bonusEl.textContent = '+送 ' + bonus + '%';
            bonusEl.style.display = 'inline-block';
        } else {
            bonusEl.style.display = 'none';
        }
    }
    
    updatePreview();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
