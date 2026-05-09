<?php
$pageTitle = '全部分类 - ' . ($settings['siteName'] ?? 'xAI');
include __DIR__ . '/partials/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">文章分类</h1>
        <p class="mt-4 text-lg text-gray-500">浏览我们要闻的各个领域</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($categories as $category): ?>
        <a href="/<?php echo htmlspecialchars($category['slug']); ?>.html" class="group block bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 overflow-hidden">
            <div class="p-6 flex flex-col items-center text-center">
                <div class="h-16 w-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-2xl mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <i class="bi bi-grid"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                    <?php echo htmlspecialchars($category['name']); ?>
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    <?php echo isset($category['description']) && $category['description'] ? htmlspecialchars($category['description']) : '暂无描述'; ?>
                </p>
                <div class="mt-4 text-xs font-medium text-indigo-500 bg-indigo-50 px-3 py-1 rounded-full">
                    浏览文章 &rarr;
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
