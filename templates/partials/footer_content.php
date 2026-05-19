<footer class="bg-white border-t border-gray-200 mt-auto py-12">
    <div class="max-w-screen-2xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="relative w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center overflow-hidden">
                         <?php 
                        $logoUrl = '/assets/images/logo.svg';
                        if (!empty($settings['siteFavicon']) && $settings['siteFavicon'] !== '/favicon.svg') {
                            $logoUrl = $settings['siteFavicon'];
                        } elseif (!empty($settings['siteLogo'])) {
                            $logoUrl = $settings['siteLogo'];
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($logoUrl); ?>" class="w-6 h-6 object-contain" alt="<?php echo htmlspecialchars($settings['siteName']); ?>">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tighter"><?php echo htmlspecialchars($settings['siteName'] ?? 'xAI CMS'); ?></h3>
                </div>
                <p class="text-gray-600 leading-relaxed max-w-sm">
                    <?php echo htmlspecialchars($settings['globalSeoDescription'] ?? '专注于内容生成与全球化 SEO 策略，助您在任何地理区域建立强大的品牌影响力。'); ?>
                </p>
                
                <!-- Social Links -->
                <div class="flex flex-wrap gap-4 mt-6">
                    <?php 
                    $socials = [
                        'Facebook' => ['key' => 'socialFacebook', 'icon' => 'bi-facebook'],
                        'Twitter' => ['key' => 'socialTwitter', 'icon' => 'bi-twitter'],
                        'X' => ['key' => 'socialX', 'icon' => 'bi-twitter-x'],
                        'LinkedIn' => ['key' => 'socialLinkedIn', 'icon' => 'bi-linkedin'],
                        'Instagram' => ['key' => 'socialInstagram', 'icon' => 'bi-instagram'],
                        'YouTube' => ['key' => 'socialYouTube', 'icon' => 'bi-youtube'],
                        'TikTok' => ['key' => 'socialTikTok', 'icon' => 'bi-tiktok'],
                        'QQ' => ['key' => 'socialQQ', 'icon' => 'bi-tencent-qq'],
                        'WeChat' => ['key' => 'socialWeChat', 'icon' => 'bi-wechat'],
                        'Weibo' => ['key' => 'socialWeibo', 'icon' => 'bi-sina-weibo'],
                        'Bilibili' => ['key' => 'socialBilibili', 'icon' => 'bi-play-btn'],
                        'Toutiao' => ['key' => 'socialToutiao', 'icon' => 'bi-newspaper'],
                        'Kuaishou' => ['key' => 'socialKuaishou', 'icon' => 'bi-camera-video'],
                        'Douyin' => ['key' => 'socialDouyin', 'icon' => 'bi-tiktok'],
                    ];
                    
                    foreach ($socials as $name => $config) {
                        $key = $config['key'];
                        $icon = $config['icon'];
                        $showKey = 'show' . ucfirst($key); 
                        
                        if (!empty($settings[$showKey]) && $settings[$showKey] == 1 && !empty($settings[$key])) {
                            echo '<a href="' . htmlspecialchars($settings[$key]) . '" target="_blank" class="text-gray-400 hover:text-indigo-600 transition-colors" title="' . $name . '">';
                            echo '<i class="bi ' . $icon . ' text-xl"></i>';
                            echo '</a>';
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Product Column -->
            <div class="col-span-1">
                <h4 class="text-gray-900 font-bold mb-6"><?php echo __('Products', '产品'); ?></h4>
                <ul class="space-y-4">
                    <li><a href="/" class="text-gray-600 hover:text-indigo-600 transition-colors text-sm"><?php echo __('Home', '首页'); ?></a></li>
                    <li><a href="/news" class="text-gray-600 hover:text-indigo-600 transition-colors text-sm"><?php echo __('News', '新闻动态'); ?></a></li>
                </ul>
            </div>

            <!-- Support Column -->
            <div class="col-span-1">
                <h4 class="text-gray-900 font-bold mb-6"><?php echo __('Support', '支持'); ?></h4>
                <ul class="space-y-4">
                    <li><a href="/faq" class="text-gray-600 hover:text-indigo-600 transition-colors text-sm"><?php echo __('FAQ', '常见问题'); ?></a></li>
                    <li><a href="/privacy" class="text-gray-600 hover:text-indigo-600 transition-colors text-sm"><?php echo __('Privacy Policy', '隐私政策'); ?></a></li>
                    <li><a href="/terms" class="text-gray-600 hover:text-indigo-600 transition-colors text-sm"><?php echo __('Terms of Service', '服务条款'); ?></a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
            <div class="flex flex-col md:flex-row items-center gap-4">
                <p>
                <?php 
                if (!empty($settings['footerCopyright'])) {
                    echo htmlspecialchars($settings['footerCopyright']);
                } else {
                    echo '&copy; ' . date('Y') . ' ' . htmlspecialchars($settings['siteName']) . '. All rights reserved.';
                }
                ?>
                </p>
                <a href="/sitemap.xml" target="_blank" class="hover:text-indigo-600 transition-colors flex items-center">
                    <i class="bi bi-map mr-1"></i> <?php echo __('Sitemap', '网站地图'); ?>
                </a>
                <a href="/rss.xml" target="_blank" class="hover:text-indigo-600 transition-colors flex items-center">
                    <i class="bi bi-rss mr-1"></i> <?php echo __('RSS Feed', 'RSS 订阅'); ?>
                </a>
            </div>
            <div class="flex flex-col md:flex-row items-center gap-4 mt-4 md:mt-0">
                <?php if (!empty($settings['icpBeian'])): ?>
                    <a href="https://beian.miit.gov.cn/" target="_blank" class="ignore hover:text-indigo-600 transition-colors" translate="no">
                        <?php echo htmlspecialchars($settings['icpBeian']); ?>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($settings['gonganBeian'])): ?>
                    <a href="http://www.beian.gov.cn/portal/registerSystemInfo" target="_blank" class="hover:text-indigo-600 transition-colors flex items-center">
                        <i class="bi bi-shield-check mr-1"></i>
                        <?php echo htmlspecialchars($settings['gonganBeian']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>