<?php
if (!function_exists('buildMenuTree')) {
    function buildMenuTree(array $elements, $parentId = 0) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = buildMenuTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
}

// Ensure categories exist
if (!isset($categories)) {
    // If we are here, it means the controller didn't pass categories.
    // We should try to fetch them if the Category class exists.
    if (class_exists('Category')) {
        $categories = Category::getAll();
    } else {
        $categories = [];
    }
}

$menuTree = buildMenuTree($categories);

// Determine current path for active state
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($currentPath === '/index.php') $currentPath = '/';
?>

<!-- Navbar -->
<nav id="navbar" class="fixed top-0 left-0 w-full z-[100] bg-white/90 backdrop-blur-md border-b border-gray-200 transition-all duration-500 shadow-sm">
    <div id="navbar-inner" class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3 md:px-6">
        <div class="flex items-center space-x-6 md:space-x-10">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-3 group">
                <div class="relative group cursor-pointer">
                    <div class="relative w-9 h-9 md:w-10 md:h-10 bg-indigo-600 rounded-lg flex items-center justify-center overflow-hidden transition-transform duration-500 group-hover:scale-105">
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
                </div>
                <div class="flex flex-col items-start">
                    <span class="text-gray-900 font-bold text-lg md:text-xl tracking-tighter leading-none group-hover:text-indigo-600 transition-colors">
                        <?php echo htmlspecialchars($settings['siteName'] ?? 'xAI'); ?>
                    </span>
                </div>
            </a>
            
            <!-- Desktop Nav -->
            <div class="hidden lg:flex items-center space-x-6">
                <a href="/" class="text-sm font-medium uppercase tracking-widest transition-all <?php echo ($currentPath === '/') ? 'text-gray-900' : 'text-gray-600'; ?> hover:text-indigo-600">首页</a>
                <!-- <a href="/news" class="text-sm font-medium uppercase tracking-widest transition-all <?php echo (strpos($currentPath, '/news') === 0) ? 'text-gray-900' : 'text-gray-600'; ?> hover:text-indigo-600">新闻</a> -->
                <?php foreach (array_slice($menuTree, 0, 6) as $item): ?>
                    <div class="relative group">
                        <?php 
                        $catLink = '/' . $item['slug'] . '.html';
                        $isActive = (strpos($currentPath, '/' . $item['slug'] . '.html') === 0);
                        ?>
                        <a href="<?php echo htmlspecialchars($catLink); ?>" class="text-sm font-medium uppercase tracking-widest transition-all <?php echo $isActive ? 'text-gray-900' : 'text-gray-600'; ?> hover:text-indigo-600 flex items-center py-2">
                            <?php echo htmlspecialchars($item['name']); ?>
                            <?php if (isset($item['children'])): ?>
                                <i data-lucide="chevron-down" class="w-3 h-3 ml-1 opacity-50"></i>
                            <?php endif; ?>
                        </a>
                        <?php if (isset($item['children'])): ?>
                            <div class="nav-dropdown absolute top-full left-0 w-48 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden pt-2 z-50">
                                <?php foreach ($item['children'] as $child): ?>
                                    <?php 
                                    $childLink = '/' . $child['slug'] . '.html';
                                    $isChildActive = (strpos($currentPath, '/' . $child['slug'] . '.html') === 0);
                                    ?>
                                    <a href="<?php echo htmlspecialchars($childLink); ?>" class="block px-4 py-3 text-sm font-medium <?php echo $isChildActive ? 'text-gray-900' : 'text-gray-600'; ?> hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                                        <?php echo htmlspecialchars($child['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <a href="/about" class="text-sm font-medium uppercase tracking-widest transition-all <?php echo ($currentPath === '/about') ? 'text-gray-900' : 'text-gray-600'; ?> hover:text-indigo-600">关于我们</a>
            </div>
        </div>

        <!-- Right Side: Search & Language & Mobile Menu Toggle -->
        <div class="flex items-center space-x-4">
            <form action="/search" method="GET" class="hidden md:flex items-center relative group">
                <input type="text" name="q" class="pl-9 pr-4 py-2 bg-gray-100 border border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:border-indigo-500 focus:outline-none transition-all w-32 focus:w-48 placeholder-gray-500" placeholder="搜索...">
                <i data-lucide="search" class="absolute left-3 w-3 h-3 text-gray-500 group-focus-within:text-indigo-600"></i>
            </form>

            <!-- Language Switcher (Desktop) -->
            <div class="relative group hidden md:block">
                <button class="flex items-center justify-center w-9 h-9 rounded-xl transition-all border border-gray-200 bg-white hover:border-indigo-300 hover:bg-gray-50 shadow-sm">
                    <i data-lucide="languages" class="w-4 h-4 text-indigo-600 transition-colors"></i>
                </button>
                <!-- Dropdown -->
                <div class="absolute right-0 top-full pt-2 w-48 hidden group-hover:block z-50">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                        <div class="py-2 max-h-[300px] overflow-y-auto custom-scrollbar">
                            <a href="javascript:void(0)" onclick="changeLang('en')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">English</a>
                            <a href="javascript:void(0)" onclick="changeLang('zh-CN')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">中文 (简体)</a>
                            <a href="javascript:void(0)" onclick="changeLang('zh-TW')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">中文 (繁體)</a>
                            <a href="javascript:void(0)" onclick="changeLang('ja')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">日本語</a>
                            <a href="javascript:void(0)" onclick="changeLang('ko')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">한국어</a>
                            <a href="javascript:void(0)" onclick="changeLang('es')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Español</a>
                            <a href="javascript:void(0)" onclick="changeLang('fr')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Français</a>
                            <a href="javascript:void(0)" onclick="changeLang('de')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Deutsch</a>
                            <a href="javascript:void(0)" onclick="changeLang('ru')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Русский</a>
                            <a href="javascript:void(0)" onclick="changeLang('ar')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">العربية</a>
                            <a href="javascript:void(0)" onclick="changeLang('pt')" class="block px-4 py-2 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Português</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Menu (Desktop) -->
            <div class="relative group hidden md:block ml-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="flex items-center justify-center w-9 h-9 rounded-xl transition-all border border-gray-200 bg-white hover:border-indigo-300 hover:bg-gray-50 shadow-sm">
                        <?php if (!empty($_SESSION['user_avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>" class="w-full h-full rounded-xl object-cover" alt="User">
                        <?php else: ?>
                            <i data-lucide="user" class="w-4 h-4 text-indigo-600"></i>
                        <?php endif; ?>
                    </button>
                    <!-- Dropdown -->
                    <div class="absolute right-0 top-full pt-2 w-56 hidden group-hover:block z-50">
                        <div class="bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden py-2">
                            <div class="px-4 py-2 border-b border-gray-100 mb-2">
                                <p class="text-xs text-gray-500">已登录</p>
                                <p class="text-sm font-bold text-gray-900 truncate"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
                            </div>
                            <a href="/user/center" class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2"></i>用户中心
                            </a>
                            <a href="/user/profile" class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                <i data-lucide="user" class="w-4 h-4 mr-2"></i>个人资料
                            </a>
                             <a href="/user/security" class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                <i data-lucide="shield" class="w-4 h-4 mr-2"></i>账户安全
                            </a>
                            <div class="border-t border-gray-100 mt-2 pt-2">
                                <a href="/logout" class="flex items-center px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>退出登录
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center space-x-3">
                        <a href="/login" class="text-sm font-bold text-gray-600 hover:text-indigo-600 transition-colors">登录</a>
                        <a href="/register" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200">注册</a>
                    </div>
                <?php endif; ?>
            </div>

            <button class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden absolute top-full left-0 w-full bg-white border-b border-gray-200 p-4 shadow-xl z-50 max-h-[calc(100vh-100%)] overflow-y-auto">
        <div class="flex flex-col space-y-4">
            <a href="/" class="text-sm font-black uppercase tracking-widest <?php echo ($currentPath === '/') ? 'text-indigo-600' : 'text-gray-600'; ?> hover:text-indigo-600">首页</a>
            <!-- <a href="/news" class="text-sm font-black uppercase tracking-widest <?php echo (strpos($currentPath, '/news') === 0) ? 'text-indigo-600' : 'text-gray-600'; ?> hover:text-indigo-600">新闻</a> -->
            
            <!-- Mobile Categories -->
            <?php foreach ($menuTree as $item): ?>
                <div class="space-y-1">
                    <?php 
                    $catLink = '/' . $item['slug'] . '.html';
                    $isActive = (strpos($currentPath, '/' . $item['slug'] . '.html') === 0);
                    ?>
                    <a href="<?php echo htmlspecialchars($catLink); ?>" class="block text-sm font-black uppercase tracking-widest <?php echo $isActive ? 'text-indigo-600' : 'text-gray-600'; ?> hover:text-indigo-600">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </a>
                    
                    <?php if (!empty($item['children'])): ?>
                        <div class="pl-4 space-y-2 border-l-2 border-gray-100 ml-1 mt-2 mb-2">
                            <?php foreach ($item['children'] as $child): ?>
                                <?php 
                                $childLink = '/' . $child['slug'] . '.html';
                                $isChildActive = (strpos($currentPath, '/' . $child['slug'] . '.html') === 0);
                                ?>
                                <a href="<?php echo htmlspecialchars($childLink); ?>" class="block text-xs font-bold <?php echo $isChildActive ? 'text-indigo-600' : 'text-gray-500'; ?> hover:text-indigo-600">
                                    <?php echo htmlspecialchars($child['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <a href="/about" class="text-sm font-black uppercase tracking-widest <?php echo ($currentPath === '/about') ? 'text-indigo-600' : 'text-gray-600'; ?> hover:text-indigo-600">
                关于我们
            </a>

            <!-- Mobile User Auth -->
            <div class="pt-4 border-t border-gray-100">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Account</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                            <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <a href="/user/center" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">用户中心</a>
                        <a href="/user/profile" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">个人资料</a>
                        <a href="/logout" class="block px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">退出登录</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="/login" class="flex items-center justify-center px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">登录</a>
                        <a href="/register" class="flex items-center justify-center px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">注册</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Mobile Language Switcher -->
            <div class="pt-4 border-t border-gray-100">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Language</p>
                <div class="grid grid-cols-3 gap-2">
                    <button onclick="changeLang('en')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">English</button>
                    <button onclick="changeLang('zh-CN')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">中文</button>
                    <button onclick="changeLang('zh-TW')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">繁體</button>
                    <button onclick="changeLang('ja')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">日本語</button>
                    <button onclick="changeLang('ko')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">한국어</button>
                    <button onclick="changeLang('es')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">Español</button>
                    <button onclick="changeLang('fr')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">Français</button>
                    <button onclick="changeLang('de')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">Deutsch</button>
                    <button onclick="changeLang('ru')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">Русский</button>
                    <button onclick="changeLang('ar')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">العربية</button>
                    <button onclick="changeLang('pt')" class="text-left text-xs font-bold text-gray-500 hover:text-indigo-600">Português</button>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Google Translate Logic -->
<div id="google_translate_element" style="display:none;"></div>

<?php
// Spacer to prevent content from being hidden behind the fixed navbar
// We don't show this on pages with a Hero section (overlay header).
// 1. Check if the page explicitly declares a hero section.
$pageHasHero = isset($hasHero) && $hasHero;

// 2. Fallback: Check if it's the Home Page (page 1).
if (!isset($hasHero)) {
    $isHomePage = ($currentPath === '/' || $currentPath === '/index.php');
    $isFirstPage = !isset($_GET['page']) || (int)$_GET['page'] <= 1;
    if ($isHomePage && $isFirstPage) {
        $pageHasHero = true;
    }
}

if (!$pageHasHero):
?>
    <div class="h-[60px] lg:h-[80px] w-full bg-transparent"></div>
<?php endif; ?>