<?php
// templates/landing.php
$pageTitle = $settings['siteName'] . ' - ' . ($settings['siteDescription'] ?? '企业级全球化内容管理系统');
$hasHero = true; // Landing page has a full-height hero
include __DIR__ . '/partials/header.php';
?>

<!-- Hero Section -->
<section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-white -z-10"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-100 text-indigo-700 text-sm font-semibold mb-8">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                v2.0 现已发布
            </div>
            <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-8 tracking-tight leading-tight">
                下一代 <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">全球化 CMS</span> 系统
            </h1>
            <p class="text-xl text-gray-600 mb-12 leading-relaxed max-w-2xl mx-auto">
                xAI CMS 专为出海企业打造。集成了先进的 AI 内容生成引擎、多语言自动翻译与全球 SEO 优化策略，助您轻松构建全球品牌影响力。
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/contact" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-bold text-lg transition-all shadow-lg hover:shadow-indigo-200 no-underline">
                    立即免费试用
                </a>
                <a href="#features" class="w-full sm:w-auto px-8 py-4 bg-white border border-gray-200 hover:border-gray-300 text-gray-700 rounded-full font-bold text-lg transition-all hover:shadow-md no-underline">
                    了解核心功能
                </a>
            </div>
        </div>
        
        <!-- Dashboard Preview -->
        <div class="mt-20 relative">
            <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent z-10 h-20 bottom-0"></div>
            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=2426" alt="Dashboard" class="rounded-3xl shadow-2xl border border-gray-200/50 w-full object-cover h-[400px] lg:h-[600px] object-top">
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">为什么选择 xAI CMS？</h2>
            <p class="text-xl text-gray-600">专为增长而设计，每一个功能都为了提升转化。</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="p-8 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-xl transition-all duration-300 group border border-gray-100">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform">
                    <i class="bi bi-cpu text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">AI 智能引擎</h3>
                <p class="text-gray-600">内置 GPT-4 驱动的内容生成助手，一键生成高质量营销文案，效率提升 10 倍以上。</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="p-8 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-xl transition-all duration-300 group border border-gray-100">
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-6 group-hover:scale-110 transition-transform">
                    <i class="bi bi-translate text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">多语言自动翻译</h3>
                <p class="text-gray-600">支持 50+ 种语言自动翻译与本地化适配，轻松打破语言障碍，触达全球市场。</p>
            </div>

            <!-- Feature 3 -->
            <div class="p-8 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-xl transition-all duration-300 group border border-gray-100">
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center text-green-600 mb-6 group-hover:scale-110 transition-transform">
                    <i class="bi bi-graph-up-arrow text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">极致 SEO 优化</h3>
                <p class="text-gray-600">自定义 URL 结构、Meta 标签、Sitemap 自动生成，让搜索引擎更懂你的内容。</p>
            </div>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-3xl font-bold text-gray-900">最新动态</h2>
            <a href="/news" class="text-indigo-600 font-semibold hover:text-indigo-700 flex items-center gap-2 no-underline">
                查看更多 <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php 
            // Ensure Article model is available
            $latestArticles = Article::getLatest(3);
            if (!empty($latestArticles)): 
                foreach ($latestArticles as $article):
            ?>
                <a href="/<?php echo htmlspecialchars($article['slug']); ?>.html" class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 no-underline">
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo htmlspecialchars(!empty($article['cover_image']) ? $article['cover_image'] : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=800'); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 left-4">
                            <span class="bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-xs font-bold text-gray-800 uppercase tracking-wider">
                                <?php echo htmlspecialchars($article['category_name'] ?? 'News'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-xs font-bold text-gray-400 mb-3 uppercase tracking-wider">
                            <?php echo date('M d, Y', strtotime($article['published_at'])); ?>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-indigo-600 transition-colors line-clamp-2">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </h3>
                        <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                            <?php echo htmlspecialchars($article['summary'] ?? mb_substr(strip_tags($article['content']), 0, 100)); ?>
                        </p>
                    </div>
                </a>
            <?php 
                endforeach;
            else:
            ?>
                <div class="col-span-3 text-center py-12 text-gray-500">暂无文章</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 bg-indigo-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-8">准备好开启全球化之旅了吗？</h2>
        <p class="text-xl text-indigo-200 mb-12">立即加入 xAI CMS，与全球 1000+ 企业一起，用内容连接世界。</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/register" class="w-full sm:w-auto px-8 py-4 bg-white text-indigo-900 hover:bg-indigo-50 rounded-full font-bold text-lg transition-all no-underline">
                免费注册账户
            </a>
            <a href="/contact" class="w-full sm:w-auto px-8 py-4 bg-transparent border border-indigo-400 text-white hover:bg-indigo-800 rounded-full font-bold text-lg transition-all no-underline">
                联系销售团队
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
