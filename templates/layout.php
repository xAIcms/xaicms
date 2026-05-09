<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'xAI CMS Admin'; ?></title>
    
    <!-- Favicon -->
    <?php 
    // Ensure settings are available
    if (!isset($settings) && class_exists('Settings')) {
        $settings = Settings::getAll();
    }
    $favicon = !empty($settings['siteFavicon']) ? $settings['siteFavicon'] : '/favicon.svg';
    $faviconType = (strpos($favicon, '.svg') !== false) ? 'image/svg+xml' : 'image/x-icon';
    ?>
    <link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>" type="<?php echo $faviconType; ?>">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($favicon); ?>" type="<?php echo $faviconType; ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
<!-- UEditor Plus CSS (Local) -->
<!-- <link href="/assets/vendor/ueditor/themes/default/css/ueditor.css" rel="stylesheet"> -->
<link href="/assets/css/admin.css" rel="stylesheet">
</head>
<body style="padding-top: 0 !important;">
    
<header class="navbar navbar-expand-md navbar-light bg-white fixed-top border-bottom shadow-sm" style="z-index: 1030;">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fw-bold text-primary" href="/admin">
        <i class="bi bi-globe2 me-2"></i>xAI CMS
    </a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav w-100 justify-content-end px-3">
        <div class="nav-item text-nowrap">
            <a class="nav-link px-3 text-secondary" href="/admin/logout">
                <i class="bi bi-box-arrow-right me-1"></i> 退出登录
            </a>
        </div>
    </div>
</header>

<div class="container-fluid p-0">
    <div>
        <nav id="sidebarMenu" class="d-md-block bg-white sidebar collapse border-end">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin' || $_SERVER['REQUEST_URI'] === '/admin/') ? 'active' : ''; ?>" href="/admin">
                            <i class="bi bi-speedometer2 me-2"></i>
                            控制台
                        </a>
                    </li>

                    <!-- Content Management -->
                    <?php 
                    $uri = $_SERVER['REQUEST_URI'];
                    $contentActive = (
                        strpos($uri, '/admin/articles') !== false || 
                        strpos($uri, '/admin/categories') !== false || 
                        strpos($uri, '/admin/tags') !== false || 
                        strpos($uri, '/admin/media') !== false
                    );
                    ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center justify-content-between <?php echo $contentActive ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#menu-content" role="button" aria-expanded="<?php echo $contentActive ? 'true' : 'false'; ?>">
                            <span><i class="bi bi-collection me-2"></i> 内容管理</span>
                            <i class="bi bi-chevron-down small transition-transform"></i>
                        </a>
                        <div class="collapse <?php echo $contentActive ? 'show' : ''; ?>" id="menu-content">
                            <ul class="nav flex-column ms-3 border-start ps-2 my-1">
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/articles') !== false) ? 'active' : ''; ?>" href="/admin/articles">文章管理</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/categories') !== false) ? 'active' : ''; ?>" href="/admin/categories">分类管理</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/tags') !== false) ? 'active' : ''; ?>" href="/admin/tags">标签管理</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/media') !== false) ? 'active' : ''; ?>" href="/admin/media">媒体库</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- User & Finance -->
                    <?php 
                    $userActive = (
                        strpos($uri, '/admin/users') !== false || 
                        strpos($uri, '/admin/point-packages') !== false || 
                        strpos($uri, '/admin/recharge-orders') !== false
                    );
                    ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center justify-content-between <?php echo $userActive ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#menu-user" role="button" aria-expanded="<?php echo $userActive ? 'true' : 'false'; ?>">
                            <span><i class="bi bi-people me-2"></i> 用户与财务</span>
                            <i class="bi bi-chevron-down small transition-transform"></i>
                        </a>
                        <div class="collapse <?php echo $userActive ? 'show' : ''; ?>" id="menu-user">
                            <ul class="nav flex-column ms-3 border-start ps-2 my-1">
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/users') !== false) ? 'active' : ''; ?>" href="/admin/users">用户管理</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/point-packages') !== false) ? 'active' : ''; ?>" href="/admin/point-packages">积分套餐</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/recharge-orders') !== false) ? 'active' : ''; ?>" href="/admin/recharge-orders">充值审核</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- API & AI -->
                    <?php 
                    $apiActive = (
                        strpos($uri, '/admin/api') !== false || 
                        strpos($uri, '/admin/ai-models') !== false
                    );
                    ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center justify-content-between <?php echo $apiActive ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#menu-api" role="button" aria-expanded="<?php echo $apiActive ? 'true' : 'false'; ?>">
                            <span><i class="bi bi-cpu me-2"></i> 接口与模型</span>
                            <i class="bi bi-chevron-down small transition-transform"></i>
                        </a>
                        <div class="collapse <?php echo $apiActive ? 'show' : ''; ?>" id="menu-api">
                            <ul class="nav flex-column ms-3 border-start ps-2 my-1">
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/api') !== false && strpos($uri, '/admin/ai-models') === false) ? 'active' : ''; ?>" href="/admin/api">API 管理</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/ai-models') !== false) ? 'active' : ''; ?>" href="/admin/ai-models">AI 模型管理</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Operations -->
                    <?php 
                    $opsActive = (
                        strpos($uri, '/admin/plugins') !== false ||
                        strpos($uri, '/admin/announcements') !== false || 
                        strpos($uri, '/admin/system-updates') !== false || 
                        strpos($uri, '/admin/spider-logs') !== false
                    );
                    ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center justify-content-between <?php echo $opsActive ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#menu-ops" role="button" aria-expanded="<?php echo $opsActive ? 'true' : 'false'; ?>">
                            <span><i class="bi bi-activity me-2"></i> 运营与维护</span>
                            <i class="bi bi-chevron-down small transition-transform"></i>
                        </a>
                        <div class="collapse <?php echo $opsActive ? 'show' : ''; ?>" id="menu-ops">
                            <ul class="nav flex-column ms-3 border-start ps-2 my-1">
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/announcements') !== false) ? 'active' : ''; ?>" href="/admin/announcements">平台公告</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/templates') !== false) ? 'active' : ''; ?>" href="/admin/templates">🎨 模板管理</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/update') !== false) ? 'active' : ''; ?>" href="/admin/update">🔄 系统更新</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/plugins') !== false) ? 'active' : ''; ?>" href="/admin/plugins">🔌 插件管理</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/system-updates') !== false) ? 'active' : ''; ?>" href="/admin/system-updates">系统更新</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 <?php echo (strpos($uri, '/admin/spider-logs') !== false) ? 'active' : ''; ?>" href="/admin/spider-logs">爬虫日志</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Settings -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($uri, '/admin/settings') !== false) ? 'active' : ''; ?>" href="/admin/settings">
                            <i class="bi bi-gear me-2"></i>
                            系统设置
                        </a>
                    </li>
                </ul>
                
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-muted text-uppercase small fw-bold">
                    <span>快捷入口</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="/" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-2"></i>
                            查看站点
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="bg-light">
            <div class="container-fluid p-0" style="max-width: 1600px;">
                <?php echo $content; ?>
            </div>
        </main>
    </div>
</div>

<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- UEditor Plus JS (Local) -->
<script src="/assets/vendor/ueditor/ueditor.config.js"></script>
<script src="/assets/vendor/ueditor/ueditor.all.min.js"></script>
<script src="/assets/vendor/ueditor/lang/zh-cn/zh-cn.js"></script>
<script>
    // Initialize Bootstrap Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
</body>
</html>