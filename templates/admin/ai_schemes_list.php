<?php
// templates/admin/ai_schemes_list.php
$pageTitle = 'AI 方案管理';
ob_start();
?>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">AI 方案列表</h3>
    </div>
    <div class="border-t border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">用户</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">方案名称</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">目标数量</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">已生成</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">冻结积分</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">创建时间</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">操作</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($schemes as $scheme): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $scheme['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($scheme['user_name']); ?>
                            <span class="text-gray-500 text-xs block">余额: <?php echo $scheme['user_points']; ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            <?php echo htmlspecialchars($scheme['name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            <?php if ($scheme['api_key']): ?>
                                <?php echo htmlspecialchars(substr($scheme['api_key'], 0, 8) . '...'); ?>
                                <button onclick="navigator.clipboard.writeText('<?php echo $scheme['api_key']; ?>')" class="ml-1 text-indigo-600 hover:text-indigo-900" title="复制完整 Key"><i class="bi bi-clipboard"></i></button>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $scheme['target_count']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $scheme['generated_count']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $scheme['frozen_points']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($scheme['status'] === 'pending'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">待审核</span>
                            <?php elseif ($scheme['status'] === 'approved'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">已批准</span>
                            <?php elseif ($scheme['status'] === 'running'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">进行中</span>
                            <?php elseif ($scheme['status'] === 'rejected'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">已拒绝</span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800"><?php echo ucfirst($scheme['status']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $scheme['created_at']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <?php if ($scheme['status'] === 'pending'): ?>
                                <a href="/admin/ai-schemes/approve/<?php echo $scheme['id']; ?>" class="text-green-600 hover:text-green-900 mr-4" onclick="return confirm('确定要批准此方案吗？这将激活相关的 API 配置。')">批准</a>
                                <a href="/admin/ai-schemes/reject/<?php echo $scheme['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('确定要拒绝此方案吗？积分将返还给用户。')">拒绝</a>
                            <?php endif; ?>
                            
                            <!-- Link to View/Edit associated API Config -->
                            <!-- We don't have direct link here easily without api_config_id, but we know user_id and scheme_id -->
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    显示第 <span class="font-medium"><?php echo $offset + 1; ?></span> 到 <span class="font-medium"><?php echo min($offset + $limit, $total); ?></span> 条，共 <span class="font-medium"><?php echo $total; ?></span> 条
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo $page === $i ? 'bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
