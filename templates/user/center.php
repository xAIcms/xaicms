<?php ob_start(); ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stats Card 1 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-50 text-indigo-600">
                <i class="bi bi-clock-history text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">注册时间</p>
                <p class="text-lg font-bold text-gray-900">
                    <?php echo isset($user['created_at']) ? date('Y-m-d', strtotime($user['created_at'])) : 'N/A'; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Card 2 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-50 text-yellow-600">
                <i class="bi bi-coin text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">我的积分</p>
                <p class="text-lg font-bold text-gray-900">
                    <?php echo number_format($user['points'] ?? 0); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Card 3 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-50 text-blue-600">
                <i class="bi bi-geo-alt text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">上次登录 IP</p>
                <p class="text-lg font-bold text-gray-900 text-sm">
                    <?php echo htmlspecialchars($user['login_ip'] ?? '未知'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Announcements -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-xl">
             <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="bi bi-megaphone text-indigo-600 mr-2"></i> 平台公告
             </h3>
        </div>
        <div class="p-6 flex-grow">
            <?php if (empty($announcements)): ?>
                <div class="text-center text-gray-500 py-4">暂无公告</div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($announcements as $item): ?>
                    <div class="border-b border-gray-100 last:border-0 pb-4 last:pb-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mr-2
                                        <?php 
                                        switch($item['type']) {
                                            case 'important': echo 'bg-red-100 text-red-800'; break;
                                            case 'activity': echo 'bg-orange-100 text-orange-800'; break;
                                            case 'feature': echo 'bg-purple-100 text-purple-800'; break;
                                            default: echo 'bg-blue-100 text-blue-800';
                                        }
                                        ?>">
                                        <?php echo Announcement::getTypeLabel($item['type']); ?>
                                    </span>
                                    <h4 class="text-sm font-medium text-gray-900 line-clamp-1"><?php echo htmlspecialchars($item['title']); ?></h4>
                                </div>
                                <?php if (!empty($item['content'])): ?>
                                    <p class="text-xs text-gray-500 line-clamp-2 mt-1"><?php echo htmlspecialchars(strip_tags($item['content'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="text-xs text-gray-400 whitespace-nowrap ml-2">
                                <?php echo date('m-d', strtotime($item['published_at'])); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- System Updates -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-xl">
             <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="bi bi-activity text-green-600 mr-2"></i> 系统更新
             </h3>
        </div>
        <div class="p-6 flex-grow overflow-y-auto" style="max-height: 300px;">
            <?php if (empty($systemUpdates)): ?>
                <div class="text-center text-gray-500 py-4">暂无更新记录</div>
            <?php else: ?>
                <div class="relative border-l border-gray-200 ml-3 space-y-6">
                    <?php foreach ($systemUpdates as $item): ?>
                    <div class="relative pl-6">
                        <div class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full border border-white bg-green-500"></div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-baseline mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mb-1 sm:mb-0">
                                <?php echo htmlspecialchars($item['version']); ?>
                            </span>
                            <span class="text-xs text-gray-400">
                                <?php echo date('Y-m-d', strtotime($item['release_date'])); ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1 whitespace-pre-line"><?php echo htmlspecialchars($item['content']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <h3 class="text-lg font-bold text-gray-900">最近活动日志</h3>
        <span class="text-xs text-gray-500 bg-white border border-gray-200 px-2 py-1 rounded-md shadow-sm">最近 10 条</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作类型</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">详情</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP 地址</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">时间</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($activities)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">
                        暂无活动记录
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($activities as $log): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $badgeClass = 'bg-gray-100 text-gray-800';
                            $icon = 'bi-circle';
                            switch($log['action']) {
                                case 'login': 
                                    $badgeClass = 'bg-blue-100 text-blue-800'; 
                                    $icon = 'bi-box-arrow-in-right';
                                    break;
                                case 'register':
                                    $badgeClass = 'bg-green-100 text-green-800';
                                    $icon = 'bi-person-plus';
                                    break;
                                case 'change_password':
                                    $badgeClass = 'bg-yellow-100 text-yellow-800';
                                    $icon = 'bi-key';
                                    break;
                                case 'update_profile':
                                    $badgeClass = 'bg-indigo-100 text-indigo-800';
                                    $icon = 'bi-pencil-square';
                                    break;
                            }
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                <i class="bi <?php echo $icon; ?> mr-1.5"></i>
                                <?php echo htmlspecialchars($log['action']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo htmlspecialchars($log['details'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            <?php echo htmlspecialchars($log['ip_address']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
$pageTitle = '用户仪表盘';
require __DIR__ . '/layout.php';
?>
