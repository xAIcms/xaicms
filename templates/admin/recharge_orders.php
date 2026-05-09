<?php
$title = '充值订单审核';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">充值订单审核</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2 shadow-sm">
            <a href="?status=all" class="btn btn-sm btn-outline-secondary <?php echo (!isset($_GET['status']) || $_GET['status'] === 'all') ? 'active' : ''; ?>">全部</a>
            <a href="?status=pending" class="btn btn-sm btn-outline-warning <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'active' : ''; ?>">待审核</a>
            <a href="?status=approved" class="btn btn-sm btn-outline-success <?php echo (isset($_GET['status']) && $_GET['status'] === 'approved') ? 'active' : ''; ?>">已完成</a>
            <a href="?status=rejected" class="btn btn-sm btn-outline-danger <?php echo (isset($_GET['status']) && $_GET['status'] === 'rejected') ? 'active' : ''; ?>">已拒绝</a>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>用户</th>
                        <th>套餐 / 金额</th>
                        <th>积分变动</th>
                        <th>用户备注</th>
                        <th>状态</th>
                        <th>提交时间</th>
                        <th>审核备注</th>
                        <th class="text-end pe-4">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">暂无相关订单</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#<?php echo $order['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <span class="fw-bold"><?php echo strtoupper(substr($order['user_name'], 0, 1)); ?></span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($order['user_name']); ?></div>
                                            <div class="small text-muted">
                                                <?php 
                                                $contact = $order['user_email'];
                                                if (strpos($contact, '@mobile.user') !== false) {
                                                    $contact = !empty($order['user_phone']) ? $order['user_phone'] : str_replace('@mobile.user', '', $contact);
                                                }
                                                echo htmlspecialchars($contact); 
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?php echo htmlspecialchars($order['package_name']); ?></div>
                                    <div class="small text-danger fw-bold">¥ <?php echo number_format($order['amount'], 2); ?></div>
                                </td>
                                <td class="fw-bold text-primary">+<?php echo number_format($order['points']); ?></td>
                                <td class="small text-muted text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($order['user_remark'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($order['user_remark'] ?? '-'); ?>
                                </td>
                                <td>
                                    <?php if ($order['status'] === 'approved'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">已完成</span>
                                    <?php elseif ($order['status'] === 'rejected'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">已拒绝</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">待审核</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?>
                                </td>
                                <td class="text-muted small text-truncate" style="max-width: 150px;">
                                    <?php echo htmlspecialchars($order['admin_remark'] ?? '-'); ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <button type="button" class="btn btn-sm btn-success text-white shadow-sm" onclick='openAuditModal(<?php echo $order['id']; ?>, "approve", <?php echo json_encode($order['user_remark'] ?? ""); ?>)'>
                                                <i class="bi bi-check-lg"></i> 通过
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger text-white shadow-sm" onclick='openAuditModal(<?php echo $order['id']; ?>, "reject", <?php echo json_encode($order['user_remark'] ?? ""); ?>)'>
                                                <i class="bi bi-x-lg"></i> 拒绝
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-white border text-primary shadow-sm" onclick='openEditRemarkModal(<?php echo $order['id']; ?>, <?php echo json_encode($order['admin_remark'] ?? ""); ?>)' title="编辑备注">
                                            <i class="bi bi-pencil"></i>
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
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white border-top-0 py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Audit Modal -->
<div class="modal fade" id="auditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="auditForm">
                <?php if (class_exists('Csrf')) echo Csrf::input(); ?>
                <input type="hidden" name="id" id="modal-order-id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">审核订单</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modal-message"></p>
                    <div id="modal-user-remark-container" class="mb-3 p-3 bg-light rounded d-none">
                        <label class="small text-muted fw-bold">用户备注:</label>
                        <p id="modal-user-remark" class="mb-0 text-dark small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="remark" class="form-label">备注信息 (可选)</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary" id="modal-submit-btn">确认</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let auditModal;

document.addEventListener('DOMContentLoaded', function() {
    auditModal = new bootstrap.Modal(document.getElementById('auditModal'));
});

function openAuditModal(id, action, userRemark = '') {
    document.getElementById('modal-order-id').value = id;
    const form = document.getElementById('auditForm');
    const title = document.getElementById('modal-title');
    const message = document.getElementById('modal-message');
    const btn = document.getElementById('modal-submit-btn');
    const userRemarkContainer = document.getElementById('modal-user-remark-container');
    const userRemarkEl = document.getElementById('modal-user-remark');
    
    // Reset inputs
    document.getElementById('remark').value = '';

    if (userRemark) {
        userRemarkContainer.classList.remove('d-none');
        userRemarkEl.textContent = userRemark;
    } else {
        userRemarkContainer.classList.add('d-none');
    }
    
    if (action === 'approve') {
        form.action = '/admin/recharge-orders/approve';
        title.textContent = '通过充值审核';
        title.className = 'modal-title text-success fw-bold';
        message.textContent = '确认收到款项并为用户发放积分吗？';
        btn.className = 'btn btn-success';
        btn.textContent = '确认通过';
    } else {
        form.action = '/admin/recharge-orders/reject';
        title.textContent = '拒绝充值申请';
        title.className = 'modal-title text-danger fw-bold';
        message.textContent = '确认要拒绝该充值申请吗？';
        btn.className = 'btn btn-danger';
        btn.textContent = '确认拒绝';
    }
    
    auditModal.show();
}

function openEditRemarkModal(id, currentRemark) {
    document.getElementById('modal-order-id').value = id;
    const form = document.getElementById('auditForm');
    const title = document.getElementById('modal-title');
    const message = document.getElementById('modal-message');
    const btn = document.getElementById('modal-submit-btn');
    const userRemarkContainer = document.getElementById('modal-user-remark-container');
    
    // Hide user remark for this simple edit modal or pass it if needed
    userRemarkContainer.classList.add('d-none');
    
    form.action = '/admin/recharge-orders/update-remark';
    title.textContent = '修改订单备注';
    title.className = 'modal-title text-primary fw-bold';
    message.textContent = ''; // No confirmation message needed
    btn.className = 'btn btn-primary';
    btn.textContent = '保存修改';
    
    document.getElementById('remark').value = currentRemark;
    
    auditModal.show();
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
