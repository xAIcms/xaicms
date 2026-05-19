<?php
// templates/search.php — Search results page
$q = trim($_GET['q'] ?? '');
include __DIR__ . '/partials/header.php';
?>

<section class="py-16 bg-white min-h-screen">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Search Form -->
        <form action="/search" method="GET" class="mb-10">
            <div class="flex gap-3">
                <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" 
                    placeholder="搜索文章..." 
                    class="flex-1 border border-gray-300 rounded-xl px-5 py-3 text-lg focus:border-indigo-500 focus:outline-none">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 transition-colors">
                    <i class="bi bi-search mr-1"></i>搜索
                </button>
            </div>
        </form>

        <?php if ($q): ?>
            <p class="text-gray-500 mb-8">
                搜索 "<strong class="text-gray-900"><?php echo htmlspecialchars($q); ?></strong>" 
                <?php if ($total > 0): ?>
                    找到 <strong class="text-indigo-600"><?php echo $total; ?></strong> 篇相关文章
                <?php else: ?>
                    未找到相关文章
                <?php endif; ?>
            </p>

            <?php if (!empty($articles)): ?>
                <div class="space-y-6">
                    <?php foreach ($articles as $post): ?>
                    <div class="border-b border-gray-100 pb-6">
                        <h3 class="text-xl font-bold mb-2">
                            <a href="/<?php echo htmlspecialchars($post['slug']); ?>.html" class="text-gray-900 hover:text-indigo-600 transition-colors no-underline">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 text-sm mb-3">
                            <?php 
                            $excerpt = strip_tags($post['summary'] ?? $post['content'] ?? '');
                            // Highlight keyword
                            $excerpt = mb_substr($excerpt, 0, 200);
                            echo htmlspecialchars($excerpt);
                            ?>...
                        </p>
                        <div class="flex items-center gap-3 text-xs text-gray-400">
                            <span><i class="bi bi-calendar3 mr-1"></i><?php echo date('Y-m-d', strtotime($post['published_at'])); ?></span>
                            <?php if (!empty($post['category_name'])): ?>
                                <span><i class="bi bi-folder mr-1"></i><?php echo htmlspecialchars($post['category_name']); ?></span>
                            <?php endif; ?>
                            <span><i class="bi bi-eye mr-1"></i><?php echo $post['views'] ?? 0; ?> 阅读</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="mt-10">
                    <?php $baseUrl = '/search?q=' . urlencode($q); include __DIR__ . '/partials/pagination.php'; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-20 text-gray-400">
                <i class="bi bi-search text-5xl mb-4 block"></i>
                <p class="text-lg">输入关键词搜索文章</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
