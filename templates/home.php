<?php
// templates/home.php — Product landing page
$pageTitle = $settings['siteName'] . ' - ' . ($settings['siteDescription'] ?? '');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$categories = $categories ?? Category::getAll();

include __DIR__ . '/partials/header.php';
?>

<?php if ($page === 1): ?>
<!-- Hero -->
<section class="relative min-h-[520px] flex items-center justify-center text-white overflow-hidden bg-gradient-to-br from-indigo-900 via-indigo-800 to-purple-900">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-20 w-96 h-96 bg-purple-400 rounded-full blur-3xl"></div>
    </div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center py-20">
        <div class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur rounded-full text-sm mb-6 border border-white/20">
            🚀 开源免费 · MIT 协议
        </div>
        <h1 class="text-4xl md:text-6xl font-extrabold mb-6 tracking-tight leading-tight">
            <?php echo htmlspecialchars($settings['siteName'] ?? 'xAI CMS'); ?>
        </h1>
        <p class="text-xl md:text-2xl text-indigo-200 mb-4 font-light">
            <?php echo htmlspecialchars($settings['siteDescription'] ?? ''); ?>
        </p>
        <p class="text-indigo-300/70 mb-10 max-w-2xl mx-auto">
            对接 DeepSeek / OpenAI / Claude，输入主题 → AI 批量写作 → 自动发布上线
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="https://github.com/xAIcms/xaicms" target="_blank" class="px-8 py-4 bg-white text-indigo-900 rounded-full font-bold text-lg transition-all hover:bg-indigo-50 no-underline inline-flex items-center gap-2">
                <i class="bi bi-github"></i> GitHub
            </a>
            <a href="#how" class="px-8 py-4 bg-indigo-600 text-white rounded-full font-bold text-lg transition-all hover:bg-indigo-500 no-underline border border-indigo-500">
                怎么用 →
            </a>
        </div>
    </div>
</section>

<!-- Feature Cards -->
<section class="py-20 bg-white border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
            <div class="p-6">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-cpu text-green-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">AI 批量写作</h3>
                <p class="text-sm text-gray-500">一个指令，N篇多语言文章</p>
            </div>
            <div class="p-6">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-globe2 text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">14种语言</h3>
                <p class="text-sm text-gray-500">中英日德法西俄韩…全覆盖</p>
            </div>
            <div class="p-6">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-graph-up-arrow text-orange-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">SEO 全自动</h3>
                <p class="text-sm text-gray-500">Sitemap/RSS/hreflang 内置</p>
            </div>
            <div class="p-6">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-shield-check text-purple-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">数据自控</h3>
                <p class="text-sm text-gray-500">开源自托管，不按量收费</p>
            </div>
        </div>
    </div>
</section>

<!-- Who is this for -->
<section class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">谁在用？</h2>
            <p class="text-lg text-gray-500">一个人管内容的中小团队，用 AI 替代编辑团队</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-2xl border border-gray-100">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-cart3 text-green-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">跨境电商卖家</h3>
                <p class="text-gray-500 text-sm">独立站需要大量英文/日文产品博客做SEO，AI批量生成，省掉海外写手成本</p>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-gray-100">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-building text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">外贸B2B企业</h3>
                <p class="text-gray-500 text-sm">用行业关键词文章获取Google询盘，十几篇多语言内容一键生成</p>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-gray-100">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-globe2 text-purple-600 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">海外产品创业者</h3>
                <p class="text-gray-500 text-sm">产品博客、更新日志、多语言落地页，用AI自动化内容营销</p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Pain Points / About (admin editable via homepage_about_html) -->
<?php $homeAbout = $settings['homepage_about_html'] ?? ''; ?>
<?php if ($homeAbout && $page === 1): ?>
<section class="py-24 bg-white" id="about">
    <div class="max-w-7xl mx-auto px-6">
        <?php echo $homeAbout; ?>
    </div>
</section>
<?php endif; ?>

<!-- How it works (admin editable via homepage_services_html) -->
<?php $homeServices = $settings['homepage_services_html'] ?? ''; ?>
<?php if ($homeServices && $page === 1): ?>
<section class="py-24 bg-gray-50" id="how">
    <div class="max-w-7xl mx-auto px-6">
        <?php echo $homeServices; ?>
    </div>
</section>
<?php endif; ?>

<!-- Latest Articles / News -->
<?php if ($page === 1): ?>
<section class="py-24 bg-white" id="news">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4"><?php echo __('Latest News', '最新动态'); ?></h2>
        </div>
<?php else: ?>
<section class="py-24 bg-white" id="news">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-3xl font-bold text-gray-900"><?php echo __f('Articles - Page %d', $page); ?></h2>
            <div class="hidden md:flex space-x-2">
                 <a href="/news" class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-bold no-underline"><?php echo __('All', '全部'); ?></a>
                 <?php foreach (array_slice($categories ?? [], 0, 4) as $cat): ?>
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
                <div class="col-span-3 text-center py-20">
                    <p class="text-gray-400"><?php echo __('Stay tuned...', '内容即将上线'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($articles)): ?>
        <div class="mt-16">
            <?php 
            $baseUrl = '/news'; 
            include __DIR__ . '/partials/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<?php $homeCta = $settings['homepage_cta_html'] ?? ''; ?>
<?php if ($homeCta && $page === 1): ?>
<section class="py-24 bg-gray-900 text-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <?php echo $homeCta; ?>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
