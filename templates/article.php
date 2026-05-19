<?php
// templates/article.php
require_once __DIR__ . '/partials/language_map.php';

$pageTitle = ($article['seo_title'] ?: $article['title']) . ' - ' . $settings['siteName'];
$pageDescription = $article['seo_description'] ?: $article['summary'];
$pageKeywords = $article['seo_keywords'] ?? '';

// Auto-generate keywords from tags if empty
if (empty($pageKeywords) && !empty($tags)) {
    $pageKeywords = implode(',', array_column($tags, 'name'));
}

// Get Parent Categories Chain
$parentCategories = [];
if (!empty($category) && is_array($category)) {
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
}

$hasHero = true; // This page has a full-width hero image, so we don't need the navbar spacer.
include __DIR__ . '/partials/header.php';
?>

<!-- Reading Progress Bar -->
<div id="progress-container" class="fixed top-0 left-0 w-full h-1 bg-transparent z-50 pointer-events-none">
    <div id="progress-bar" class="h-full bg-indigo-600 w-0 transition-all duration-150 ease-out shadow-[0_0_10px_#4f46e5]"></div>
</div>
<script>
window.addEventListener('scroll', () => {
    const totalScroll = document.documentElement.scrollTop;
    const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scroll = totalScroll / windowHeight;
    document.getElementById('progress-bar').style.width = `${scroll * 100}%`;
});
</script>

<!-- Hero Section -->
<div class="relative w-full min-h-[40vh] flex items-center bg-gray-100">
    <div class="absolute inset-0 w-full h-full">
        <img src="<?php echo htmlspecialchars(!empty($article['cover_image']) ? $article['cover_image'] : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=800'); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/10"></div>
    </div>

    <div class="relative w-full pt-32 pb-16">
        <div class="max-w-[1400px] mx-auto w-full px-4 sm:px-6 lg:px-8">
            <nav class="mb-8 text-sm font-medium" aria-label="breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-white/70">
                    <li>
                        <a href="/" class="hover:text-indigo-300 transition-colors"><?php echo __('Home', '首页'); ?></a>
                    </li>

                    <?php if (!empty($parentCategories)): ?>
                        <?php foreach ($parentCategories as $parent): ?>
                            <li class="text-white/40">/</li>
                            <li>
                                <a href="/<?php echo htmlspecialchars($parent['slug']); ?>.html" class="hover:text-indigo-300 transition-colors">
                                    <?php echo htmlspecialchars($parent['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($category) && is_array($category) && !empty($category['slug'])): ?>
                        <li class="text-white/40">/</li>
                        <li>
                            <a href="/<?php echo htmlspecialchars($category['slug']); ?>.html" class="hover:text-indigo-300 transition-colors">
                                <?php echo htmlspecialchars($category['name'] ?? ''); ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="text-white/40">/</li>
                        <li>
                            <a href="/news" class="hover:text-indigo-300 transition-colors"><?php echo __("News", "新闻"); ?></a>
                        </li>
                    <?php endif; ?>

                    <li class="text-white/40">/</li>
                    <li class="text-white/90">
                        <?php echo htmlspecialchars($article['title']); ?>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-wrap items-center gap-3 mb-6">
                <?php if (!empty($article['geo_region'])): ?>
                <span class="px-3 py-1 bg-indigo-600/80 text-white text-xs font-bold rounded-full uppercase tracking-wider">
                    <?php echo htmlspecialchars($article['geo_region']); ?>
                </span>
                <?php endif; ?>
                
                <?php if (!empty($article['language'])): ?>
                <span class="px-3 py-1 bg-gray-200 text-gray-800 text-xs font-bold rounded-full uppercase tracking-wider border border-gray-300">
                    <?php echo htmlspecialchars(getLanguageName($article['language'])); ?>
                </span>
                <?php endif; ?>
            </div>

            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white mb-8 leading-tight max-w-3xl">
                <?php echo htmlspecialchars($article['title']); ?>
            </h1>

            <div class="flex flex-wrap items-center gap-6 text-white/90 text-sm font-medium border-t border-white/10 pt-6">
                <div class="flex items-center">
                    <i class="bi bi-person w-4 h-4 mr-2 text-indigo-600"></i>
                    <span><?php echo htmlspecialchars($article['author'] ?? 'xAI'); ?></span>
                </div>
                <div class="flex items-center">
                    <i class="bi bi-calendar3 w-4 h-4 mr-2 text-indigo-600"></i>
                    <span><?php echo date('Y/m/d', strtotime($article['published_at'])); ?></span>
                </div>
                <div class="flex items-center">
                    <i class="bi bi-book w-4 h-4 mr-2 text-indigo-600"></i>
                    <span><?php echo ceil(mb_strlen($article["content"]) / 500); ?> <?php echo __("min read", "分钟阅读"); ?></span>
                </div>
                <div class="flex items-center">
                    <i class="bi bi-eye w-4 h-4 mr-2 text-indigo-600"></i>
                    <span><?php echo $article["views"]; ?> <?php echo __("views", "次阅读"); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10 mb-16">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-t-3xl p-8 md:p-12 shadow-lg border border-gray-200">
                
                <!-- Summary Box -->
                <?php if (!empty($article['summary'])): ?>
                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-6 mb-10 rounded-r-lg">
                    <h3 class="text-indigo-600 font-bold mb-3 text-sm uppercase tracking-wider flex items-center">
                        <i class="bi bi-book w-4 h-4 mr-2"></i>
                        <?php echo __("Summary", "文章摘要"); ?>
                    </h3>
                    <p class="text-gray-700 italic leading-relaxed text-lg font-serif">
                        <?php echo htmlspecialchars($article['summary']); ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Article Body -->
                <article class="prose prose-lg max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-p:text-gray-700 prose-a:text-indigo-600 prose-img:rounded-2xl prose-img:shadow-lg prose-blockquote:border-l-indigo-500 prose-blockquote:bg-indigo-50 prose-blockquote:py-2 prose-blockquote:px-4 prose-blockquote:not-italic prose-strong:text-gray-900 prose-code:text-indigo-600">
                    <?php echo Markdown::parse($article['content']); ?>
                </article>

                <!-- Tags -->
                <?php if (!empty($tags)): ?>
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($tags as $tag): ?>
                            <a href="/tag/<?php echo $tag['slug']; ?>.html" class="flex items-center px-4 py-1.5 bg-gray-100 text-gray-600 rounded-full text-sm font-medium hover:bg-indigo-100 hover:text-indigo-600 transition-colors cursor-pointer no-underline border border-gray-200 hover:border-indigo-300">
                                <i class="bi bi-tag w-3 h-3 mr-1.5"></i> <?php echo htmlspecialchars($tag['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- SEO Metadata Card (Optional, matching React) -->
                <div class="mt-12 bg-gray-100 rounded-2xl p-6 border border-gray-200">
                     <h4 class="flex items-center text-sm font-bold text-gray-600 mb-4 pb-3 border-b border-gray-200">
                        <i class="bi bi-robot w-4 h-4 mr-2 text-indigo-600"></i>
                        SEO Metadata
                     </h4>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-600 font-mono">
                        <div>
                           <span class="block text-gray-600 mb-1">Slug</span>
                           <span class="text-gray-400 break-all"><?php echo htmlspecialchars($article['slug']); ?></span>
                        </div>
                        <div>
                           <span class="block text-gray-600 mb-1">UUID</span>
                           <span class="text-gray-400"><?php echo htmlspecialchars($article['uuid'] ?? 'N/A'); ?></span>
                        </div>
                     </div>
                </div>

                <!-- Related Articles -->
                <?php 
                $relatedArticles = Article::getRelated($article['id'], $article['category_id'], 3);
                if (!empty($relatedArticles)): 
                ?>
                <div class="mt-12 pt-12 border-t border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
                        <i class="bi bi-collection-play w-6 h-6 mr-3 text-indigo-600"></i>
                        <?php echo __("Related", "相关推荐"); ?>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php foreach ($relatedArticles as $related): ?>
                        <a href="/<?php echo $related['slug']; ?>.html" class="group block bg-gray-100 rounded-xl overflow-hidden border border-gray-200 hover:border-indigo-300 transition-all duration-300 hover:shadow-lg">
                            <div class="aspect-video w-full overflow-hidden relative">
                                <img src="<?php echo htmlspecialchars(!empty($related['cover_image']) ? $related['cover_image'] : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=400'); ?>" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>" 
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-60"></div>
                            </div>
                            <div class="p-4">
                                <h4 class="text-gray-900 font-bold text-sm mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </h4>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span><?php echo date('M d, Y', strtotime($related['published_at'])); ?></span>
                                    <span class="flex items-center"><i class="bi bi-eye mr-1"></i> <?php echo $related['views']; ?></span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>


        <!-- Sidebar -->
        <aside class="lg:w-80 flex-shrink-0 space-y-8 mt-8 lg:mt-0">
            <!-- Author / About Widget -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl border border-blue-200">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($settings['article_sidebar_name'] ?? 'xAI CMS'); ?></h4>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($settings['article_sidebar_role'] ?? ''); ?></p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($settings['article_sidebar_bio'] ?? '')); ?>
                </p>
            </div>

            <!-- Recent Articles Widget -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center text-sm uppercase tracking-wider">
                    <i class="bi bi-lightning-charge w-4 h-4 mr-2 text-[#00f58d]"></i>
                    <?php echo __("Latest", "最新发布"); ?>
                </h3>
                <div class="space-y-4">
                    <?php 
                    // Fetch recent articles for sidebar (fetch more to handle self-exclusion)
                    $sidebarArticles = Article::getLatest(6); 
                    $shownCount = 0;
                    foreach ($sidebarArticles as $sa): 
                        if ($sa['id'] == $article['id']) continue;
                        if ($shownCount >= 10) break; // Limit to 10 items
                        $shownCount++;
                    ?>
                    <a href="/<?php echo $sa['slug']; ?>.html" class="group block">
                        <h4 class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition-colors line-clamp-2 mb-1">
                            <?php echo htmlspecialchars($sa['title']); ?>
                        </h4>
                        <span class="text-xs text-gray-500"><?php echo date('M d', strtotime($sa['published_at'])); ?></span>
                    </a>
                    <?php endforeach; ?>
                    
                    <?php if ($shownCount === 0): ?>
                        <p class="text-xs text-gray-500 italic"><?php echo __("No more articles", "暂无更多文章"); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Trending Topics -->
            <?php 
            $limit = 10;
            include __DIR__ . '/partials/sidebar_topics.php'; 
            ?>

        </aside>

    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
