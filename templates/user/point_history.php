<?php ob_start(); ?>
<?php require_once __DIR__ . '/../../src/Models/Article.php'; ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900">积分变动历史</h3>
        <div class="text-sm text-gray-500">
            当前积分: <span class="font-bold text-indigo-600"><?php echo number_format($user['points'] ?? 0); ?></span>
            <span class="mx-2">|</span>
            冻结积分: <span class="font-bold text-orange-600"><?php echo number_format($user['frozen_points'] ?? 0); ?></span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">时间</th>
                    <th class="px-6 py-3">类型</th>
                    <th class="px-6 py-3">变动</th>
                    <th class="px-6 py-3">详情</th>
                    <th class="px-6 py-3">IP地址</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            暂无积分变动记录
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                <?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                $badgeClass = 'bg-gray-100 text-gray-800';
                                $label = $log['action'];
                                $amountClass = 'text-gray-900';
                                
                                switch($log['action']) {
                                    case 'freeze_points':
                                        $badgeClass = 'bg-orange-100 text-orange-800';
                                        $label = '冻结积分';
                                        $amountClass = 'text-orange-600 font-bold';
                                        break;
                                    case 'unfreeze_points':
                                        $badgeClass = 'bg-green-100 text-green-800';
                                        $label = '解冻积分';
                                        $amountClass = 'text-green-600 font-bold';
                                        break;
                                    case 'consume_points':
                                        $badgeClass = 'bg-red-100 text-red-800';
                                        $label = '消费积分';
                                        $amountClass = 'text-red-600 font-bold';
                                        break;
                                    case 'add_points':
                                        $badgeClass = 'bg-blue-100 text-blue-800';
                                        $label = '增加积分';
                                        $amountClass = 'text-blue-600 font-bold';
                                        break;
                                    case 'admin_update_points':
                                        $badgeClass = 'bg-purple-100 text-purple-800';
                                        $label = '系统变更';
                                        $amountClass = 'text-purple-600 font-bold';
                                        break;
                                }
                                
                                // Parse Amount and Reason from Details
                                $details = $log['details'];
                                $amount = '-';
                                $reason = $details;
                                
                                // Matches "Reason: Amount" or "Reason: -Amount"
                                if (preg_match('/^(.*):\s*([-+]?\d+)$/', $details, $matches)) {
                                    $reason = trim($matches[1]);
                                    $amount = $matches[2];
                                }

                                $articleHref = null;
                                if (preg_match('#(/(?:news/)?[^\s)]+\.html)#u', $reason, $m)) {
                                    $articleHref = $m[1];
                                }
                                if (empty($articleHref) && preg_match('/生成文章[:：]\s*(.+)$/u', $reason, $m)) {
                                    $title = trim($m[1]);
                                    if (!empty($title)) {
                                        $candidates = Article::search($title, 1, 0);
                                        if (!empty($candidates) && !empty($candidates[0]['slug'])) {
                                            $articleHref = '/' . $candidates[0]['slug'] . '.html';
                                        }
                                    }
                                }
                                
                                // Add +/- sign if missing based on action type for clarity
                                if (is_numeric($amount)) {
                                    if ($log['action'] == 'consume_points' && $amount > 0) {
                                        $amount = '-' . $amount;
                                    } elseif ($log['action'] == 'add_points' && $amount > 0 && strpos($amount, '+') === false) {
                                        $amount = '+' . $amount;
                                    } elseif ($log['action'] == 'freeze_points' && $amount > 0) {
                                         $amount = '-' . $amount; // Freeze is a deduction from available
                                    } elseif ($log['action'] == 'unfreeze_points' && $amount > 0) {
                                         $amount = '+' . $amount; // Unfreeze is adding back to available
                                    }
                                }
                                ?>
                                <span class="px-2 py-1 rounded text-xs font-medium <?php echo $badgeClass; ?>">
                                    <?php echo $label; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 <?php echo $amountClass; ?>">
                                <?php echo $amount; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-900">
                                <?php echo htmlspecialchars($reason); ?>
                                <?php if (!empty($articleHref)): ?>
                                    <a href="<?php echo htmlspecialchars($articleHref); ?>" target="_blank" rel="noopener noreferrer" class="ml-2 inline-flex items-center text-gray-400 hover:text-indigo-600 transition-colors" title="打开文章">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?php echo htmlspecialchars($log['ip_address']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between sm:px-6">
        <!-- Mobile Pagination -->
        <div class="flex-1 flex justify-between sm:hidden">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?php echo $currentPage - 1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">上一页</a>
            <?php else: ?>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">上一页</span>
            <?php endif; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?php echo $currentPage + 1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">下一页</a>
            <?php else: ?>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">下一页</span>
            <?php endif; ?>
        </div>

        <!-- Desktop Pagination -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div class="text-sm text-gray-500">
                显示 <?php echo ($currentPage - 1) * $perPage + 1; ?> 到 <?php echo min($currentPage * $perPage, $totalLogs); ?> 条，共 <?php echo $totalLogs; ?> 条
            </div>
            <div class="flex space-x-2">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?php echo $currentPage - 1; ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">上一页</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="px-3 py-1 text-sm border rounded bg-indigo-50 text-indigo-600 border-indigo-200"><?php echo $i; ?></span>
                    <?php elseif ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
                        <a href="?page=<?php echo $i; ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50"><?php echo $i; ?></a>
                    <?php elseif (abs($i - $currentPage) == 3): ?>
                        <span class="px-2 py-1 text-sm text-gray-400">...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?php echo $currentPage + 1; ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">下一页</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$pageTitle = '积分变动历史';
require __DIR__ . '/layout.php';
?>
