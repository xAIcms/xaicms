<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-medium text-gray-900">AI 写作方案</h2>
        <p class="mt-1 text-sm text-gray-500">管理您的自动写作任务，查看进度和状态。</p>
    </div>
    <a href="/user/ai-schemes/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <i class="bi bi-plus-lg mr-2"></i> 新建方案
    </a>
</div>

<!-- Points Summary -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 mb-8">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="bi bi-coin text-yellow-400 text-3xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">可用积分</dt>
                        <dd>
                            <div class="text-lg font-medium text-gray-900"><?php echo $userPoints['points']; ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="bi bi-lock-fill text-gray-400 text-3xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">冻结积分 (进行中任务)</dt>
                        <dd>
                            <div class="text-lg font-medium text-gray-900"><?php echo $userPoints['frozen_points']; ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div class="rounded-md bg-green-50 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="bi bi-check-circle-fill text-green-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-green-800"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <?php if (empty($schemes)): ?>
        <div class="text-center py-12">
            <i class="bi bi-robot text-gray-300 text-4xl mb-4 block"></i>
            <h3 class="mt-2 text-sm font-medium text-gray-900">暂无方案</h3>
            <p class="mt-1 text-sm text-gray-500">您还没有创建任何 AI 写作方案。</p>
            <div class="mt-6">
                <a href="/user/ai-schemes/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="bi bi-plus-lg mr-2"></i> 创建第一个方案
                </a>
            </div>
        </div>
    <?php else: ?>
        <ul class="divide-y divide-gray-200">
            <?php foreach ($schemes as $scheme): ?>
            <li>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-indigo-600 truncate">
                            <?php echo htmlspecialchars($scheme['name']); ?>
                        </p>
                        <div class="ml-2 flex-shrink-0 flex">
                            <?php
                            $statusClass = '';
                            $statusText = '';
                            switch($scheme['status']) {
                                case 'pending': $statusClass = 'bg-yellow-100 text-yellow-800'; $statusText = '待审核'; break;
                                case 'approved': $statusClass = 'bg-blue-100 text-blue-800'; $statusText = '已审核'; break;
                                case 'running': $statusClass = 'bg-green-100 text-green-800'; $statusText = '运行中'; break;
                                case 'paused': $statusClass = 'bg-gray-100 text-gray-800'; $statusText = '已暂停'; break;
                                case 'completed': $statusClass = 'bg-purple-100 text-purple-800'; $statusText = '已完成'; break;
                                case 'rejected': $statusClass = 'bg-red-100 text-red-800'; $statusText = '已拒绝'; break;
                            }
                            ?>
                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                <?php echo $statusText; ?>
                            </p>
                        </div>
                        <?php if ($scheme['status'] === 'pending'): ?>
                        <div class="ml-4 flex-shrink-0 flex space-x-2">
                            <a href="/user/ai-schemes/edit/<?php echo $scheme['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-sm">编辑</a>
                            <a href="/user/ai-schemes/delete/<?php echo $scheme['id']; ?>" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('确定要删除这个方案吗？冻结的积分将退还。');">删除</a>
                        </div>
                        <?php elseif (in_array($scheme['status'], ['completed', 'rejected', 'paused'])): ?>
                        <div class="ml-4 flex-shrink-0 flex space-x-2">
                            <a href="/user/ai-schemes/edit/<?php echo $scheme['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-sm">编辑</a>
                            <a href="/user/ai-schemes/resubmit/<?php echo $scheme['id']; ?>" class="text-green-600 hover:text-green-900 text-sm" onclick="return confirm('确定要重新提交这个方案吗？将再次扣除积分并重置进度。');">重新提交</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-2 sm:flex sm:justify-between">
                        <div class="sm:flex">
                            <p class="flex items-center text-sm text-gray-500">
                                <i class="bi bi-translate mr-1.5 text-gray-400"></i>
                                <?php echo htmlspecialchars($scheme['config']['language'] ?? '-'); ?>
                            </p>
                            <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                <i class="bi bi-geo-alt mr-1.5 text-gray-400"></i>
                                <?php echo htmlspecialchars($scheme['config']['region'] ?? '-'); ?>
                            </p>
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                            <i class="bi bi-calendar-event mr-1.5 text-gray-400"></i>
                            <p>提交于 <?php echo date('Y-m-d', strtotime($scheme['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                            <span>进度: <?php echo $scheme['generated_count']; ?> / <?php echo $scheme['target_count']; ?></span>
                            <span><?php echo $scheme['target_count'] > 0 ? round(($scheme['generated_count'] / $scheme['target_count']) * 100) : 0; ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?php echo $scheme['target_count'] > 0 ? min(100, round(($scheme['generated_count'] / $scheme['target_count']) * 100)) : 0; ?>%"></div>
                        </div>
                    </div>
                    <?php if (!empty($scheme['admin_notes'])): ?>
                    <div class="mt-2 text-sm text-red-500 bg-red-50 p-2 rounded">
                        <strong>管理员备注:</strong> <?php echo htmlspecialchars($scheme['admin_notes']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        
        <?php if ($totalPages > 1): ?>
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    上一页
                </a>
                <?php else: ?>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">
                    上一页
                </span>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    下一页
                </a>
                <?php else: ?>
                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">
                    下一页
                </span>
                <?php endif; ?>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        显示第 <span class="font-medium"><?php echo $offset + 1; ?></span> 到 <span class="font-medium"><?php echo min($offset + $limit, $total); ?></span> 条，共 <span class="font-medium"><?php echo $total; ?></span> 条
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                    </nav>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
