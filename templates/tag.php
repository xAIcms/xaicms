<?php
// templates/tag.php
$pageTitle = '#' . $tag['name'] . ' - ' . $settings['siteName'];
$pageDescription = $tag["name"];
$pageKeywords = $tag['name'];
include __DIR__ . '/partials/header.php';
?>

<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- 左侧内容区 -->
        <div class="flex-1 min-w-0">
            <!-- 标签头部信息 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-indigo-50 rounded-xl">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900"># <?php echo htmlspecialchars($tag['name']); ?></h1>
                        <p class="text-gray-500 mt-1"><?php echo __f("Found %d articles", $total); ?></p>
                    </div>
                </div>
            </div>

            <?php if (empty($articles)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo __("No articles with this tag yet.", "该标签下暂无文章"); ?></h3>
                    <p class="text-gray-500 mb-8"><?php echo __("Check out other tags.", "看看其他标签的文章吧"); ?></p>
                    <a href="/" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200">
                        <?php echo __("Back to home", "返回首页"); ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="grid gap-8">
                    <?php foreach ($articles as $article): ?>
                        <article class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 h-full flex flex-col group">
                            <div class="flex flex-col md:flex-row h-full">
                                <!-- 文章封面图 -->
                                <?php if (!empty($article['cover_image'])): ?>
                                <div class="md:w-72 h-48 md:h-auto relative overflow-hidden flex-shrink-0">
                                    <a href="/<?php echo $article['slug']; ?>.html" class="block h-full">
                                        <img src="<?php echo htmlspecialchars($article['cover_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($article['title']); ?>" 
                                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <div class="flex-1 p-6 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                                            <?php if (!empty($article['category_name'])): ?>
                                            <a href="/<?php echo $article['category_slug']; ?>.html" 
                                               class="text-indigo-600 font-medium hover:text-indigo-700 bg-indigo-50 px-2 py-1 rounded-md transition-colors">
                                                <?php echo htmlspecialchars($article['category_name']); ?>
                                            </a>
                                            <?php endif; ?>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <?php echo date('Y-m-d', strtotime($article['published_at'])); ?>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                <?php echo number_format($article['views'] ?? 0); ?>
                                            </span>
                                        </div>

                                        <h2 class="text-2xl font-bold text-gray-900 mb-3 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                                            <a href="/<?php echo $article['slug']; ?>.html">
                                                <?php echo htmlspecialchars($article['title']); ?>
                                            </a>
                                        </h2>
                                        
                                        <p class="text-gray-600 line-clamp-2 mb-4 leading-relaxed">
                                            <?php echo mb_substr(strip_tags($article['content']), 0, 150) . '...'; ?>
                                        </p>
                                    </div>

                                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                                        <div class="flex items-center gap-2">
                                            <?php if (!empty($article['author_name'])): ?>
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                                                <?php echo mb_substr($article['author_name'], 0, 1); ?>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($article['author_name']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="/<?php echo $article['slug']; ?>.html" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors group/link">
                                            <?php echo __("Read more", "阅读全文"); ?>
                                            <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    <?php include __DIR__ . '/partials/pagination.php'; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 右侧边栏 -->
        <div class="hidden lg:block w-80 flex-shrink-0">
            <div class="sticky top-8 space-y-8">
                 <!-- Author / About Widget -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-xl border border-indigo-200">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">xAI</h4>
                            <p class="text-xs text-gray-500">Intelligent Editor</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        <?php echo htmlspecialchars($settings['siteDescription'] ?? ''); ?>
                    </p>
                </div>

                 <!-- Trending Topics -->
                <?php 
                $limit = 10;
                include __DIR__ . '/partials/sidebar_topics.php'; 
                ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
