<?php
// templates/about.php
$pageTitle = '关于我们 - ' . ($settings['siteName'] ?? '新闻中心');
include __DIR__ . '/partials/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="text-center mb-16">
        <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
            关于 <span class="text-indigo-600"><?php echo htmlspecialchars($settings['siteName'] ?? '新闻中心'); ?></span>
        </h1>
        <p class="mt-5 max-w-xl mx-auto text-xl text-gray-500">
            连接世界，传递真实声音。我们致力于为您提供最及时、最深度的新闻报道。
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
        <!-- Main Content -->
        <div>
            <div class="prose prose-lg text-gray-600 max-w-none">
                <h3 class="text-2xl font-bold text-gray-900">我们的使命</h3>
                <p>
                    在这个信息爆炸的时代，我们深知获取真实、客观新闻的重要性。我们的平台不仅是一个新闻发布中心，更是一个连接思想、激发讨论的社区。我们利用先进的 AI 技术与专业的人工编辑团队相结合，为您筛选最有价值的全球资讯。
                </p>
                
                <h3 class="text-2xl font-bold text-gray-900 mt-8">我们的团队</h3>
                <p>
                    我们是一群充满激情的新闻工作者、技术极客和数据分析师。
                </p>
                <ul class="list-none pl-0 space-y-4 mt-4">
                    <li class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-4 text-indigo-600 font-bold">AC</div>
                        <div>
                            <span class="block font-bold text-gray-900">Alex Chen</span>
                            <span class="text-sm text-gray-500">创始人 & 主编</span>
                        </div>
                    </li>
                    <li class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-pink-100 flex items-center justify-center mr-4 text-pink-600 font-bold">SW</div>
                        <div>
                            <span class="block font-bold text-gray-900">Sarah Wu</span>
                            <span class="text-sm text-gray-500">资深编辑</span>
                        </div>
                    </li>
                    <li class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-4 text-blue-600 font-bold">DL</div>
                        <div>
                            <span class="block font-bold text-gray-900">David Lee</span>
                            <span class="text-sm text-gray-500">技术总监</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contact & Info Sidebar -->
        <div class="space-y-8">
            <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200">
                <h3 class="text-xl font-bold mb-6 flex items-center text-gray-900">
                    <i class="bi bi-person-lines-fill mr-2 text-indigo-600"></i> 联系方式
                </h3>
                <ul class="space-y-6">
                    <li class="flex items-start">
                        <div class="bg-indigo-50 p-3 rounded-xl mr-4">
                            <i class="bi bi-envelope text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">商务合作</p>
                            <a href="mailto:<?php echo htmlspecialchars($settings['contactEmail'] ?? 'contact@example.com'); ?>" class="text-gray-600 mt-1 hover:text-indigo-600 transition-colors block">
                                <?php echo htmlspecialchars($settings['contactEmail'] ?? 'contact@example.com'); ?>
                            </a>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="bg-indigo-50 p-3 rounded-xl mr-4">
                            <i class="bi bi-telephone text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">联系电话</p>
                            <p class="text-gray-600 mt-1">+86 123 4567 8900</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="bg-indigo-50 p-3 rounded-xl mr-4">
                            <i class="bi bi-geo-alt text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">公司地址</p>
                            <p class="text-gray-600 mt-1">
                                上海市浦东新区<br>
                                科技创新园区 88 号
                            </p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="bg-indigo-600 rounded-2xl p-8 shadow-lg text-white">
                <h3 class="text-xl font-bold mb-4">加入我们</h3>
                <p class="mb-6 text-indigo-100">我们一直在寻找优秀的人才加入我们的团队。如果你对新闻和技术充满热情，请联系我们。</p>
                <a href="mailto:hr@example.com" class="inline-block bg-white text-indigo-600 font-bold py-3 px-6 rounded-lg hover:bg-indigo-50 transition-colors">
                    投递简历
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
