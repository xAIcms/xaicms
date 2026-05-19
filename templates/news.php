<?php
// templates/news.php — Pure article listing page
$pageTitle = __('News', '新闻动态') . ' - ' . ($settings['siteName'] ?? 'xAI CMS');
$categories = $categories ?? Category::getAll();

include __DIR__ . '/partials/header.php';
?>

<!-- Page Header -->
<section class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-purple-900 py-16">
    <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
            <?php echo __('News', '新闻动态'); ?>
        </h1>
        <p class="text-indigo-200">
            <?php echo __('Latest updates and articles', '最新文章与产品动态'); ?>
        </p>
    </div>
</section>

<!-- Articles -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <?php if ($page === 1): ?>
        <div class="flex items-center justify-between mb-10">
            <div class="flex space-x-2">
                 <a href="/news" class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-bold no-underline"><?php echo __('All', '全部'); ?></a>
                 <?php foreach (array_slice($categories ?? [], 0, 5) as $cat): ?>
                    <a href="/<?php echo htmlspecialchars($cat['slug']); ?>.html" class="px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-full text-sm font-bold transition-colors no-underline">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                 <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $post): ?>
                    <article class="flex flex-col h-full group">
                        <a href="/<?php echo htmlspecialchars($post['slug']); ?>.html" class="block overflow-hidden rounded-2xl mb-6 h-56 bg-gray-100 relative">
                            <img src="<?php echo htmlspecialchars(!empty($post['cover_image']) ? $post['cover_image'] : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=800'); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-xs font-bold text-gray-800 uppercase tracking-wider border border-gray-200">
                                    <?php echo htmlspecialchars($post['category_name'] ?? 'News'); ?>
                                </span>
                            </div>
                        </a>
                        <div class="flex-1 flex flex-col">
                            <div class="text-xs font-bold text-gray-400 mb-3 uppercase tracking-wider">
                                <?php echo date('M d, Y', strtotime($post['published_at'])); ?>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-indigo-600 transition-colors leading-snug">
                                <a href="/<?php echo htmlspecialchars($post['slug']); ?>.html" class="no-underline text-inherit">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4 flex-1">
                                <?php echo htmlspecialchars($post['summary'] ?? mb_substr(strip_tags($post['content']), 0, 100)); ?>
                            </p>
                            <a href="/<?php echo htmlspecialchars($post['slug']); ?>.html" class="inline-flex items-center text-indigo-600 font-bold text-sm tracking-widest hover:text-indigo-800 transition-colors no-underline">
                                <?php echo __('Read more', '阅读更多'); ?> <i class="bi bi-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-20">
                    <p class="text-gray-400"><?php echo __('No articles yet', '暂无文章'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($articles) && isset($totalPages) && $totalPages > 1): ?>
        <div class="mt-16">
            <?php 
            $baseUrl = '/news'; 
            include __DIR__ . '/partials/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
