<?php
// templates/landing.php — Landing page, driven by settings.landing_content
$pageTitle = $settings['siteName'] ?? 'xAI CMS';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$landingContent = $settings['landing_content'] ?? '';

include __DIR__ . '/partials/header.php';

if ($landingContent):
    // Admin-customized landing page
    echo htmlspecialchars($landingContent);
else:
    // Default: show latest articles
    $latestArticles = Article::getLatest(6);
?>
<div class="max-w-7xl mx-auto px-6 py-16">
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-4">
            <?php echo htmlspecialchars($settings['siteName'] ?? 'xAI CMS'); ?>
        </h1>
        <p class="text-xl text-gray-500 max-w-2xl mx-auto">
            <?php echo htmlspecialchars($settings['siteDescription'] ?? ''); ?>
        </p>
        <div class="flex justify-center gap-4 mt-8">
            <a href="/news" class="px-8 py-3 bg-indigo-600 text-white rounded-full font-bold hover:bg-indigo-700 transition-colors no-underline">
                <?php echo __('Browse Articles', '浏览文章'); ?>
            </a>
            <a href="/about" class="px-8 py-3 border border-indigo-300 text-indigo-600 rounded-full font-bold hover:bg-indigo-50 transition-colors no-underline">
                <?php echo __('About Us', '关于我们'); ?>
            </a>
        </div>
    </div>

    <?php if (!empty($latestArticles)): ?>
    <h2 class="text-2xl font-bold text-gray-900 mb-8"><?php echo __('Latest News', '最新动态'); ?></h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ($latestArticles as $post): ?>
        <article class="group">
            <a href="/<?php echo htmlspecialchars($post['slug']); ?>.html" class="block overflow-hidden rounded-2xl mb-4 h-48 bg-gray-100">
                <img src="<?php echo htmlspecialchars(!empty($post['cover_image']) ? $post['cover_image'] : ''); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            </a>
            <a href="/<?php echo htmlspecialchars($post['slug']); ?>.html" class="no-underline">
                <h3 class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h3>
            </a>
            <p class="text-sm text-gray-500 mt-2"><?php echo htmlspecialchars(mb_substr(strip_tags($post['summary'] ?? $post['content'] ?? ''), 0, 100)); ?></p>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
