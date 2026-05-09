<?php
// templates/user/ai_scheme_form.php
require_once __DIR__ . '/../../src/Config/AppConfig.php';
?>
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4"><?php echo $isEdit ? '编辑 AI 写作方案' : '新建 AI 写作方案'; ?></h3>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?php echo $isEdit ? '/user/ai-schemes/edit/' . $scheme['id'] : '/user/ai-schemes/create'; ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <!-- Scheme Name -->
                <div class="sm:col-span-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">方案名称</label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" required
                               value="<?php echo $isEdit ? htmlspecialchars($scheme['name']) : ''; ?>"
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                               placeholder="例如：科技新闻自动更新">
                    </div>
                </div>

                <!-- Region -->
                <div class="sm:col-span-3">
                    <label for="region" class="block text-sm font-medium text-gray-700">目标地区</label>
                    <div class="mt-1">
                        <select id="region" name="region" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                            <?php foreach (AppConfig::REGIONS as $code => $name): ?>
                                <option value="<?php echo $code; ?>" <?php echo ($isEdit && isset($scheme['config']['region']) && $scheme['config']['region'] === $code) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Language -->
                <div class="sm:col-span-3">
                    <label for="language" class="block text-sm font-medium text-gray-700">语言</label>
                    <div class="mt-1">
                        <select id="language" name="language" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                            <?php foreach (AppConfig::LANGUAGES as $code => $name): ?>
                                <option value="<?php echo $code; ?>" <?php echo ($isEdit && isset($scheme['config']['language']) && $scheme['config']['language'] === $code) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Keywords -->
                <div class="sm:col-span-6">
                    <label for="keywords" class="block text-sm font-medium text-gray-700">关键词 (一行一个)</label>
                    <div class="mt-1">
                        <textarea name="keywords" id="keywords" rows="5" required
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                               placeholder="科技&#10;人工智能&#10;区块链"><?php echo $isEdit ? htmlspecialchars($scheme['config']['keywords'] ?? '') : ''; ?></textarea>
                    </div>
                </div>

                <!-- Prompt / Requirements -->
                <div class="sm:col-span-6">
                    <label for="prompt" class="block text-sm font-medium text-gray-700">具体要求 / Prompt (可选，对应推广植入信息)</label>
                    <div class="mt-1">
                        <textarea id="prompt" name="prompt" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md p-2" placeholder="例如：文章风格幽默风趣，每篇文章必须包含至少一张图片..."><?php echo $isEdit ? htmlspecialchars($scheme['config']['prompt'] ?? '') : ''; ?></textarea>
                    </div>
                </div>

                <!-- Target Count -->
                <div class="sm:col-span-3">
                    <label for="target_count" class="block text-sm font-medium text-gray-700">生成文章总数量</label>
                    <div class="mt-1">
                        <input type="number" name="target_count" id="target_count" min="1" 
                               value="<?php echo $isEdit ? $scheme['target_count'] : '10'; ?>" required
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                               oninput="updateCost()">
                    </div>
                </div>
                
                <!-- Daily Limit -->
                <div class="sm:col-span-3">
                    <label for="daily_limit" class="block text-sm font-medium text-gray-700">每天发布数量 (0为不限制)</label>
                    <div class="mt-1">
                        <input type="number" name="daily_limit" id="daily_limit" min="0" 
                               value="<?php echo $isEdit ? ($scheme['daily_limit'] ?? 0) : '0'; ?>"
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">控制每天生成的文章数量，避免短时间大量发布。</p>
                </div>
                
                <!-- Cost Estimation -->
                <div class="sm:col-span-6 bg-gray-50 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">当前可用积分</p>
                            <p class="text-lg font-bold text-gray-900"><?php echo $userPoints['points']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-500">预计冻结积分</p>
                            <p class="text-lg font-bold text-indigo-600" id="estimated_cost"><?php echo $isEdit ? ($scheme['target_count'] * $scheme['cost_per_post']) : '10'; ?></p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">* 费用标准：1 积分 / 篇文章。提交后积分将被冻结，直至任务完成或取消。</p>
                </div>
            </div>

            <div class="pt-5">
                <div class="flex justify-end">
                    <a href="/user/ai-schemes" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        取消
                    </a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <?php echo $isEdit ? '保存修改' : '提交方案'; ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function updateCost() {
    const count = document.getElementById('target_count').value;
    const costPerPost = 1; // Sync with backend logic
    const total = count * costPerPost;
    document.getElementById('estimated_cost').innerText = total;
}
</script>
