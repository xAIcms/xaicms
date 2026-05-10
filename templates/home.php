<?php
// templates/home.php
$pageTitle = $settings['siteName'] . ' - ' . ($settings['siteDescription'] ?? '首页');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$categories = $categories ?? Category::getAll();

include __DIR__ . '/partials/header.php';
?>

<?php if ($page === 1): ?>
    <!-- Hero Section -->
    <section class="relative h-[400px] lg:h-[500px] flex items-center justify-center text-white overflow-hidden bg-gradient-to-br from-indigo-900 via-indigo-800 to-purple-900">
        <div class="relative z-10 max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 tracking-tight leading-tight">
                <?php echo htmlspecialchars($settings['siteName'] ?? 'xAI CMS'); ?>
            </h1>
            <p class="text-xl md:text-2xl text-indigo-200 mb-10 max-w-3xl mx-auto font-light">
                <?php echo htmlspecialchars($settings['siteDescription'] ?? ''); ?>
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/news" class="px-8 py-4 bg-white text-indigo-900 rounded-full font-semibold text-lg transition-all hover:bg-indigo-50 no-underline">
                    <?php echo __('Browse Articles', '浏览文章'); ?>
                </a>
                <a href="/about" class="px-8 py-4 bg-transparent border border-indigo-300 text-white rounded-full font-semibold text-lg transition-all hover:bg-white/10 no-underline">
                    <?php echo __('About Us', '关于我们'); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Strip -->
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-globe2 text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1"><?php echo __('Multi-language', '多语言内容'); ?></h3>
                    <p class="text-sm text-gray-500"><?php echo __('Create content in 14 languages for global SEO.', '14种语言创建内容，覆盖全球SEO。'); ?></p>
                </div>
                <div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-cpu text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1"><?php echo __('AI Powered', 'AI 驱动'); ?></h3>
                    <p class="text-sm text-gray-500"><?php echo __('Automated content generation with multiple AI models.', '多模型AI自动生成高质量内容。'); ?></p>
                </div>
                <div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-graph-up-arrow text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1"><?php echo __('SEO Optimized', 'SEO 优化'); ?></h3>
                    <p class="text-sm text-gray-500"><?php echo __('Built-in Schema.org, sitemap, hreflang and more.', '内置结构化数据、站点地图、多语言标签。'); ?></p>
                </div>
            </div>
        </div>
    </section><?php endif; ?>

    <?php $homeAbout = $settings['homepage_about_html'] ?? ''; ?>
    <?php if ($homeAbout): ?>
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <?php echo $homeAbout; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php $homeServices = $settings['homepage_services_html'] ?? ''; ?>
    <?php if ($homeServices): ?>
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <?php echo $homeServices; ?>
        </div>
    </section>
    <?php endif; ?>


<!-- Latest News / Articles Section -->
<section class="py-24 bg-white" id="news">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-3xl font-bold text-gray-900">
                <?php echo $page === 1 ? __('Latest News', '最新动态') : __f('Articles - Page %d', $page); ?>
            </h2>
            
            <!-- Category Filter (Simplified) -->
            <div class="hidden md:flex space-x-2">
                 <a href="/news" class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-bold no-underline"><?php echo __('All', '全部'); ?></a>
                 <?php foreach (array_slice($categories ?? [], 0, 4) as $cat): ?>
                    <a href="/<?php echo htmlspecialchars($cat['slug']); ?>.html" class="px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-full text-sm font-bold transition-colors no-underline">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                 <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $post): ?>
                    <article class="flex flex-col h-full group">
                        <a href="/<?php echo htmlspecialchars($post['slug']); ?>.html" class="block overflow-hidden rounded-2xl mb-6 h-64 bg-gray-100 relative">
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
                            <a href="/<?php echo htmlspecialchars($post['slug']); ?>.html" class="inline-flex items-center text-indigo-600 font-bold text-sm uppercase tracking-widest hover:text-indigo-800 transition-colors no-underline">
                                <?php echo __('Read more', '阅读更多'); ?> <i class="bi bi-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-20 border border-dashed border-gray-200 rounded-2xl bg-gray-50">
                    <i class="bi bi-file-text text-4xl text-gray-400 mb-4 block"></i>
                    <p class="text-gray-500 font-medium"><?php echo __('No articles yet', '暂无文章发布'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-16">
            <?php 
            $baseUrl = '/news'; 
            include __DIR__ . '/partials/pagination.php';
            ?>
        </div>
    </div>
</section>

<!-- CTA (Page 1 only) -->
<?php $homeCta = $settings['homepage_cta_html'] ?? ''; ?>
<?php if ($homeCta): ?>
<?php if ($page === 1): ?>
<section class="py-24 bg-gray-900 text-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <?php echo $homeCta; ?>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
