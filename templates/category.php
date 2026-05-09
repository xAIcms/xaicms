<?php
// templates/category.php
$pageTitle = $category['name'] . ' - ' . $settings['siteName'];
$pageDescription = $category['description'] ?: '分类 ' . $category['name'] . ' 下的文章列表';
$pageKeywords = $category['name'];

// Get Parent Categories Chain
$parentCategories = [];
$currentCat = $category;
while (!empty($currentCat['parent_id'])) {
    $parentCat = Category::find($currentCat['parent_id']);
    if ($parentCat) {
        array_unshift($parentCategories, $parentCat);
        $currentCat = $parentCat;
    } else {
        break;
    }
}

include __DIR__ . '/partials/header.php';
?>

<!-- Modern Header Section -->
<div class="bg-gray-50 border-b border-gray-200 py-12 md:py-20 relative overflow-hidden">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-3xl">
                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4 font-medium">
                    <a href="/" class="hover:text-indigo-600 transition-colors no-underline text-gray-600">首页</a>
                    
                    <?php foreach ($parentCategories as $parent): ?>
                        <i class="bi bi-chevron-right text-xs text-gray-400"></i>
                        <a href="/<?php echo htmlspecialchars($parent['slug']); ?>.html" class="hover:text-indigo-600 transition-colors no-underline text-gray-600">
                            <?php echo htmlspecialchars($parent['name']); ?>
                        </a>
                    <?php endforeach; ?>

                    <i class="bi bi-chevron-right text-xs text-gray-400"></i>
                    <span class="text-indigo-600"><?php echo htmlspecialchars($category['name']); ?></span>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-4">
                    <?php echo htmlspecialchars($category['name']); ?>
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl leading-relaxed">
                    <?php echo htmlspecialchars($category['description'] ?: '浏览该分类下的所有精选内容，探索深度见解与全球视角。'); ?>
                </p>
            </div>
             
            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center -space-x-2">
                   <!-- Fake Avatars matching React design -->
                   <div class="w-10 h-10 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center overflow-hidden">
                        <i class="bi bi-person-fill text-gray-500"></i>
                   </div>
                   <div class="w-10 h-10 rounded-full border-2 border-white bg-gray-300 flex items-center justify-center overflow-hidden">
                        <i class="bi bi-person-fill text-gray-600"></i>
                   </div>
                   <div class="w-10 h-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs text-indigo-600 font-bold">
                      +2k
                   </div>
                </div>
                <span class="text-sm text-gray-500 font-medium hidden md:block">每日读者</span>
            </div>
        </div>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-12">
        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <?php if (!empty($articles)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($articles as $article): ?>
                        <article class="group bg-white rounded-2xl shadow-lg border border-gray-200 hover:border-indigo-300 transition-all duration-300 flex flex-col h-full overflow-hidden hover:-translate-y-1 hover:shadow-xl">
                            <a href="/<?php echo htmlspecialchars($article['slug']); ?>.html" class="block w-full relative overflow-hidden aspect-video">
                                <img src="<?php echo htmlspecialchars(!empty($article['cover_image']) ? $article['cover_image'] : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=800'); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                            </a>
                            
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <?php if (!empty($article['category_name'])): ?>
                                        <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md uppercase tracking-wide border border-indigo-100">
                                            <?php echo htmlspecialchars($article['category_name']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="text-xs text-gray-500"><?php echo date('M d, Y', strtotime($article['published_at'])); ?></span>
                                </div>
                                
                                <h2 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                    <a href="/<?php echo htmlspecialchars($article['slug']); ?>.html" class="no-underline text-gray-900 group-hover:text-indigo-600">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </a>
                                </h2>
                                
                                <p class="text-gray-600 text-sm line-clamp-3 mb-4 flex-1">
                                    <?php echo htmlspecialchars($article['summary']); ?>
                                </p>
                                
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="bi bi-eye mr-1"></i> <?php echo $article['views']; ?> 阅读
                                    </div>
                                    <a href="/<?php echo htmlspecialchars($article['slug']); ?>.html" class="text-indigo-600 text-sm font-medium group-hover:underline">阅读全文 &rarr;</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    <?php include __DIR__ . '/partials/pagination.php'; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-20 bg-white rounded-3xl border border-gray-200 shadow-sm">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-6">
                        <i class="bi bi-journal-x text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">暂无文章</h3>
                    <p class="text-gray-500">该分类下暂时没有发布任何文章。</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <aside class="w-full lg:w-80 space-y-8 shrink-0">
            <!-- Search Widget -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="bi bi-search mr-2 text-indigo-600"></i> 搜索
                </h3>
                <form action="/search" method="GET" class="relative">
                    <input type="text" name="q" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all placeholder-gray-400" placeholder="输入关键词...">
                    <i class="bi bi-search absolute left-3 top-3.5 text-gray-400"></i>
                </form>
            </div>

            <!-- Categories Widget -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="bi bi-grid mr-2 text-indigo-600"></i> 分类导航
                </h3>
                <div class="space-y-2">
                    <?php foreach ($categories as $cat): ?>
                        <a href="/<?php echo htmlspecialchars($cat['slug']); ?>.html" class="flex items-center justify-between p-3 rounded-xl hover:bg-indigo-50 transition-colors group <?php echo ($cat['id'] == $category['id']) ? 'bg-indigo-50 border border-indigo-100' : 'border border-transparent hover:border-indigo-100'; ?>">
                            <span class="text-gray-700 font-medium group-hover:text-indigo-600 transition-colors <?php echo ($cat['id'] == $category['id']) ? 'text-indigo-600' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </span>
                            <i class="bi bi-chevron-right text-xs text-gray-400 group-hover:text-indigo-600"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Trending Topics -->
            <?php include __DIR__ . '/partials/sidebar_topics.php'; ?>

        </aside>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>