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

    <!-- About Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=800" alt="Team" class="rounded-2xl shadow-2xl">
                </div>
                <div class="lg:w-1/2">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">以技术驱动商业变革</h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        xAI 成立于 2023 年，我们是一支充满激情的全球化团队。我们的使命是通过前沿的 AI 技术和精准的 GEO 数据，帮助企业打破地域界限，实现真正的全球化运营。
                    </p>
                    <a href="/about" class="inline-flex items-center text-indigo-600 font-bold hover:text-indigo-800 transition-colors no-underline">
                        关于我们 <i class="bi bi-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">核心解决方案</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">为您提供一站式的全球化数字增长服务</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="bg-white p-10 rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300">
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center mb-6 text-blue-600">
                        <i class="bi bi-globe text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">全球化布局</h3>
                    <p class="text-gray-600 leading-relaxed">
                        依托强大的 GEO 数据引擎，精准定位目标市场，提供本地化的内容策略和分发渠道。
                    </p>
                </div>
                <!-- Service 2 -->
                <div class="bg-white p-10 rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300">
                    <div class="w-14 h-14 bg-indigo-50 rounded-xl flex items-center justify-center mb-6 text-indigo-600">
                        <i class="bi bi-cpu text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">AI 智能中台</h3>
                    <p class="text-gray-600 leading-relaxed">
                        集成 Gemini 等顶尖大模型，实现内容的自动化生产、优化与分发，大幅降低运营成本。
                    </p>
                </div>
                <!-- Service 3 -->
                <div class="bg-white p-10 rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300">
                    <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center mb-6 text-purple-600">
                        <i class="bi bi-graph-up text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">数据驱动增长</h3>
                    <p class="text-gray-600 leading-relaxed">
                        全链路数据监控与分析，提供深度的 SEO 洞察和业务报表，让每一次决策都基于真实数据。
                    </p>
            </div>
        </div>
    </section>


<!-- Latest News / Articles Section -->
<section class="py-24 bg-white" id="news">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-3xl font-bold text-gray-900">
                <?php echo $page === 1 ? '最新动态' : '文章列表 - 第 ' . $page . ' 页'; ?>
            </h2>
            
            <!-- Category Filter (Simplified) -->
            <div class="hidden md:flex space-x-2">
                 <a href="/news" class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-bold no-underline">全部</a>
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
                                阅读更多 <i class="bi bi-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-20 border border-dashed border-gray-200 rounded-2xl bg-gray-50">
                    <i class="bi bi-file-text text-4xl text-gray-400 mb-4 block"></i>
                    <p class="text-gray-500 font-medium">暂无文章发布</p>
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
<?php if ($page === 1): ?>
<section class="py-24 bg-gray-900 text-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-5xl font-bold mb-8">携手共创全球未来</h2>
        <p class="text-xl text-gray-400 mb-12 max-w-2xl mx-auto">
            无论您是寻求增长的企业，还是寻找合作伙伴的机构，我们都期待与您交流。
        </p>
        <a href="/contact" class="px-10 py-5 bg-white text-gray-900 rounded-full font-bold text-lg hover:bg-gray-100 transition-colors no-underline">
            联系我们
        </a>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
