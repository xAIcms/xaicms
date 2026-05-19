<?php
// xAI CMS — Front Controller
// All routes are registered below using Router, then dispatched at the bottom.

session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_samesite' => 'Strict'
]);

// ── Core Systems ──
require_once __DIR__ . '/../src/Core/Hooks.php';
require_once __DIR__ . '/../src/Core/Plugin.php';
require_once __DIR__ . '/../src/Core/Router.php';

// WordPress-style hook functions (global)
function add_action(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): void {
    Hooks::addAction($tag, $callback, $priority, $acceptedArgs);
}
function add_filter(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): void {
    Hooks::addFilter($tag, $callback, $priority, $acceptedArgs);
}
function do_action(string $tag, ...$args): void {
    Hooks::doAction($tag, ...$args);
}
function apply_filters(string $tag, $value, ...$args) {
    return Hooks::applyFilters($tag, $value, ...$args);
}

// Load active plugins
Plugin::loadActive();

// Scheduler tick (runs on every page load, lightweight)
require_once __DIR__ . '/../src/Core/Scheduler.php';
require_once __DIR__ . '/../src/Core/Plan.php';
Scheduler::tick();

// ── Models ──
require_once __DIR__ . '/../src/Models/Settings.php';
require_once __DIR__ . '/../src/Models/Article.php';
require_once __DIR__ . '/../src/Models/Category.php';
require_once __DIR__ . '/../src/Models/Tag.php';
require_once __DIR__ . '/../src/Models/ApiConfig.php';
require_once __DIR__ . '/../src/Models/AiScheme.php';
require_once __DIR__ . '/../src/Models/AiModel.php';
require_once __DIR__ . '/../src/Models/SpiderLog.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/MediaCategory.php';
require_once __DIR__ . '/../src/Models/MediaFile.php';
require_once __DIR__ . '/../src/Utils/Markdown.php';
require_once __DIR__ . '/../src/Utils/SitemapGenerator.php';
require_once __DIR__ . '/../src/Utils/Csrf.php';

// ── Bot Detection ──
$botPatterns = [
    'Googlebot' => ['/googlebot/i', 'search'],
    'Bingbot' => ['/bingbot/i', 'search'],
    'Baiduspider' => ['/baiduspider/i', 'search'],
    'Slurp' => ['/slurp/i', 'search'],
    'DuckDuckBot' => ['/duckduckbot/i', 'search'],
    'YandexBot' => ['/yandexbot/i', 'search'],
    'GPTBot' => ['/gptbot/i', 'ai'],
    'CCBot' => ['/ccbot|crawler/i', 'ai'],
    'AhrefsBot' => ['/ahrefsbot/i', 'seo'],
    'SemrushBot' => ['/semrushbot/i', 'seo'],
];
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$realIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$path = $_SERVER['REQUEST_URI'] ?? '/';
foreach ($botPatterns as $name => [$pattern, $type]) {
    if (preg_match($pattern, $userAgent)) {
        SpiderLog::log($name, $type, $realIp, $userAgent, $path, http_response_code());
        break;
    }
}

// ── Check installed ──
if (!file_exists(__DIR__ . '/../config.php')) {
    header('Location: /install/index.php');
    exit;
}
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';

// ── URI parsing ──
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);

// Strip .html suffix for pretty URLs
if (substr($uri, -5) === '.html') {
    $uri = substr($uri, 0, -5);
}

$method = $_SERVER['REQUEST_METHOD'];

// ── Load settings (needed by many routes) ──
try {
    $settings = Settings::getAll();
} catch (Exception $e) {
    die("Error loading settings: " . $e->getMessage());
}

// ── I18n Translation System ──
require_once __DIR__ . '/../src/Core/I18n.php';
I18n::load($settings['admin_language'] ?? 'zh-CN');

/**
 * Translate a string. Global shorthand.
 *
 * Usage: __('Dashboard') → '控制台' or 'Dashboard'
 *        __f('Created %d articles', 5) → '创建了 5 篇文章'
 */
function __(string $key, string $default = ''): string {
    return I18n::t($key, $default);
}
function __f(string $key, ...$args): string {
    return I18n::tf($key, ...$args);
}

// ═══════════════════════════════════════════════════════════════
// Router Setup
// ═══════════════════════════════════════════════════════════════

$router = new Router();

// ── RSS / Sitemap ──
$router->get('/rss.xml', function() {
    require_once __DIR__ . '/../src/Utils/RssGenerator.php';
    header('Content-Type: application/xml; charset=utf-8');
    echo RssGenerator::generate();
    exit;
});

$router->get('/sitemap.xml', function() {
    header('Content-Type: application/xml; charset=utf-8');
    echo SitemapGenerator::generate();
    exit;
});

$router->get('/robots.txt', function() use ($settings) {
    header('Content-Type: text/plain; charset=utf-8');
    if (!empty($settings['robotsTxt'])) {
        echo $settings['robotsTxt'];
    } else {
        $siteUrl = $settings['siteUrl'] ?? 'http://localhost';
        $siteUrl = rtrim($siteUrl, '/');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Sitemap: $siteUrl/sitemap.xml\n";
    }
    exit;
});

// ── Auth Routes ──
$router->any('/login', function() use ($settings) {
    UserController::login();
    exit;
});

$router->any('/forgot-password', function() {
    UserController::forgotPassword();
    exit;
});

$router->any('/register', function() {
    UserController::register();
    exit;
});

$router->any('/logout', function() {
    UserController::logout();
    exit;
});

// ── User Center Routes ──
$router->group('/user', function(Router $r) {
    $r->any('/center', function() {
        UserController::center();
        exit;
    });
    $r->any('/profile', function() {
        UserController::profile();
        exit;
    });
    $r->any('/bind-phone', function() {
        UserController::bindPhone();
        exit;
    });
    $r->any('/security', function() {
        UserController::security();
        exit;
    });
    $r->any('/point-history', function() {
        UserController::pointHistory();
        exit;
    });
    $r->any('/ai-schemes', function() {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        UserAiSchemeController::index();
        exit;
    });
    $r->any('/ai-schemes/create', function() {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        UserAiSchemeController::create();
        exit;
    });
    $r->get('#^/user/ai-schemes/edit/(\d+)$#', function($id) {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        UserAiSchemeController::edit($id);
        exit;
    });
    $r->post('#^/user/ai-schemes/edit/(\d+)$#', function($id) {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        UserAiSchemeController::update($id);
        exit;
    });
    $r->any('#^/user/ai-schemes/resubmit/(\d+)$#', function($id) {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        UserAiSchemeController::resubmit($id);
        exit;
    });
    $r->any('#^/user/ai-schemes/delete/(\d+)$#', function($id) {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        UserAiSchemeController::destroy($id);
        exit;
    });
});

// ── Settings-Driven Pages ──
$router->any('/faq', function() use ($settings) {
    $pageTitle = __('FAQ', '常见问题') . ' - ' . $settings['siteName'];
    require __DIR__ . '/../templates/faq.php';
    exit;
});

$router->any('/about', function() use ($settings) {
    $pageTitle = __('About Us', '关于我们') . ' - ' . $settings['siteName'];
    require __DIR__ . '/../templates/about.php';
    exit;
});

$router->any('/privacy', function() use ($settings) {
    $pageTitle = __('Privacy Policy', '隐私政策') . ' - ' . $settings['siteName'];
    require __DIR__ . '/../templates/privacy.php';
    exit;
});

$router->any('/terms', function() use ($settings) {
    $pageTitle = __('Terms of Service', '服务条款') . ' - ' . $settings['siteName'];
    require __DIR__ . '/../templates/terms.php';
    exit;
});

// ── Admin Routes ──
$router->group('/admin', function(Router $r) use ($settings) {

    // Admin auth middleware
    $authCheck = function() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /admin/login');
            exit;
        }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            echo "<h1>403 Access Denied</h1><p>You do not have administrator privileges.</p><p><a href='/'>Back to Home</a></p>";
            exit;
        }
    };

    // Login (no auth required)
    $r->any('/login', function() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = User::findByEmail($email);
            if ($user && User::verifyPassword($user, $password)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                User::updateLoginInfo($user['id'], $_SERVER['REMOTE_ADDR']);
                header('Location: /admin/articles');
                exit;
            } else {
                $error = "邮箱或密码错误";
            }
        }
        require __DIR__ . '/../templates/admin/login.php';
        exit;
    });

    // Logout
    $r->any('/logout', function() {
        session_destroy();
        header('Location: /admin/login');
        exit;
    });

    // All other admin routes need auth
    $r->group('', function(Router $r2) use ($settings) {
        // Dashboard
        $r2->any('/', function() {
            $stats = [
                'total_articles' => Article::countAll(),
                'total_views' => Article::sumViews(),
                'api_calls' => ApiConfig::sumCallCounts()
            ];
            require __DIR__ . '/../templates/admin/dashboard.php';
            exit;
        });

        // Users
        $r2->group('/users', function(Router $r3) {
            $r3->any('/', function() { require_once __DIR__ . '/../src/Controllers/AdminUserController.php'; AdminUserController::index(); exit; });
            $r3->any('/index', function() { require_once __DIR__ . '/../src/Controllers/AdminUserController.php'; AdminUserController::index(); exit; });
            $r3->any('/edit', function() { require_once __DIR__ . '/../src/Controllers/AdminUserController.php'; $id = $_GET['id'] ?? null; if ($id) { AdminUserController::edit($id); exit; } header('Location: /admin/users'); exit; });
            $r3->any('/update', function() { require_once __DIR__ . '/../src/Controllers/AdminUserController.php'; $id = $_GET['id'] ?? null; if ($id) { AdminUserController::update($id); exit; } header('Location: /admin/users'); exit; });
            $r3->any('/delete', function() { require_once __DIR__ . '/../src/Controllers/AdminUserController.php'; $id = $_GET['id'] ?? null; if ($id) { AdminUserController::delete($id); exit; } header('Location: /admin/users'); exit; });
        });

        // AI Schemes
        $r2->group('/ai-schemes', function(Router $r3) {
            $r3->any('/', function() { require_once __DIR__ . '/../src/Controllers/AdminAiSchemeController.php'; AdminAiSchemeController::index(); exit; });
            $r3->any('/list', function() { require_once __DIR__ . '/../src/Controllers/AdminAiSchemeController.php'; AdminAiSchemeController::index(); exit; });
            $r3->any('#^/ai-schemes/approve/(\d+)$#', function($id) { require_once __DIR__ . '/../src/Controllers/AdminAiSchemeController.php'; AdminAiSchemeController::approve($id); exit; });
            $r3->any('#^/ai-schemes/reject/(\d+)$#', function($id) { require_once __DIR__ . '/../src/Controllers/AdminAiSchemeController.php'; AdminAiSchemeController::reject($id); exit; });
        });

        // Plugins, Templates, AI Settings, Schedules, Update
        $r2->any('/plugins', function() { require __DIR__ . '/../templates/admin/plugins_list.php'; exit; });
        $r2->any('/templates', function() { require __DIR__ . '/../templates/admin/templates_list.php'; exit; });
        $r2->any('/ai-settings', function() { require __DIR__ . '/../templates/admin/ai_settings.php'; exit; });
        $r2->any('/schedules', function() { require __DIR__ . '/../templates/admin/schedules.php'; exit; });
        $r2->any('/upgrade', function() { require __DIR__ . '/../templates/admin/upgrade.php'; exit; });
        $r2->any('/update', function() { require __DIR__ . '/../templates/admin/update.php'; exit; });

        // Articles CRUD
        $r2->group('/articles', function(Router $r3) {
            $r3->any('/', function() { $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; $limit = 20; $offset = ($page - 1) * $limit; $articles = Article::getAll($limit, $offset); $total = Article::countAll(); $totalPages = ceil($total / $limit); require __DIR__ . '/../templates/admin/articles_list.php'; exit; });
            $r3->any('/list', function() { $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; $limit = 20; $offset = ($page - 1) * $limit; $articles = Article::getAll($limit, $offset); $total = Article::countAll(); $totalPages = ceil($total / $limit); require __DIR__ . '/../templates/admin/articles_list.php'; exit; });
            $r3->any('/create', function() { $isEdit = false; if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); try { $newId = Article::create($_POST); do_action('article_saved', $newId, $_POST); if (isset($_POST['tags'])) { $tags = explode(',', $_POST['tags']); Article::syncTags($newId, $tags); } header('Location: /admin/articles'); exit; } catch (Exception $e) { $error = "创建失败: " . $e->getMessage(); $article = $_POST; } } require __DIR__ . '/../templates/admin/article_form.php'; exit; });
            $r3->any('/edit', function() use ($r3) { $id = $_GET['id'] ?? null; if (!$id) die("Article ID not provided"); $isEdit = true; $article = Article::find($id); if (!$article) die("Article not found"); if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); try { Article::update($id, $_POST); if (isset($_POST['tags'])) { $tags = explode(',', $_POST['tags']); Article::syncTags($id, $tags); } header('Location: /admin/articles'); exit; } catch (Exception $e) { $error = "更新失败: " . $e->getMessage(); $article = array_merge($article, $_POST); } } require __DIR__ . '/../templates/admin/article_form.php'; exit; });
            $r3->any('/delete', function() { $id = $_GET['id'] ?? $_POST['id'] ?? null; if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) { Csrf::validateOrDie(); Article::delete($id); } header('Location: /admin/articles'); exit; });
        });

        // Categories CRUD
        $r2->group('/categories', function(Router $r3) {
            $r3->any('/', function() { $categories = Category::getAll(); require __DIR__ . '/../templates/admin/categories_list.php'; exit; });
            $r3->any('/list', function() { $categories = Category::getAll(); require __DIR__ . '/../templates/admin/categories_list.php'; exit; });
            $r3->any('/create', function() { $isEdit = false; if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); try { Category::create($_POST); header('Location: /admin/categories'); exit; } catch (Exception $e) { $error = "创建失败: " . $e->getMessage(); $category = $_POST; } } $allCategories = Category::getAll(); require __DIR__ . '/../templates/admin/category_form.php'; exit; });
            $r3->any('#^/edit/(\d+)$#', function($id) { $isEdit = true; $category = Category::find($id); if (!$category) die("Category not found"); if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); try { Category::update($id, $_POST); header('Location: /admin/categories'); exit; } catch (Exception $e) { $error = "更新失败: " . $e->getMessage(); $category = array_merge($category, $_POST); } } $allCategories = Category::getAll(); require __DIR__ . '/../templates/admin/category_form.php'; exit; });
            $r3->any('#^/delete/(\d+)$#', function($id) { if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); try { Category::delete($id); header('Location: /admin/categories'); } catch (Exception $e) { echo "<script>alert('" . addslashes($e->getMessage()) . "'); window.location.href='/admin/categories';</script>"; } } exit; });
        });

        // Tags CRUD
        $r2->group('/tags', function(Router $r3) {
            $r3->any('/', function() { $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; $limit = 20; $offset = ($page - 1) * $limit; $tags = Tag::getAll($limit, $offset); $total = Tag::countAll(); $totalPages = ceil($total / $limit); require __DIR__ . '/../templates/admin/tags_list.php'; exit; });
            $r3->any('/list', function() { $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; $limit = 20; $offset = ($page - 1) * $limit; $tags = Tag::getAll($limit, $offset); $total = Tag::countAll(); $totalPages = ceil($total / $limit); require __DIR__ . '/../templates/admin/tags_list.php'; exit; });
            $r3->any('/create', function() { $isEdit = false; if ($_SERVER['REQUEST_METHOD'] === 'POST') { try { Tag::create($_POST); header('Location: /admin/tags'); exit; } catch (Exception $e) { $error = "创建失败: " . $e->getMessage(); $tag = $_POST; } } require __DIR__ . '/../templates/admin/tag_form.php'; exit; });
            $r3->any('#^/edit/(\d+)$#', function($id) { $isEdit = true; $tag = Tag::find($id); if (!$tag) die("Tag not found"); if ($_SERVER['REQUEST_METHOD'] === 'POST') { try { Tag::update($id, $_POST); header('Location: /admin/tags'); exit; } catch (Exception $e) { $error = "更新失败: " . $e->getMessage(); $tag = array_merge($tag, $_POST); } } require __DIR__ . '/../templates/admin/tag_form.php'; exit; });
            $r3->any('#^/delete/(\d+)$#', function($id) { if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); Tag::delete($id); header('Location: /admin/tags'); } exit; });
        });

        // AI Models CRUD
        $r2->group('/ai-models', function(Router $r3) {
            $r3->any('/', function() { $models = AiModel::getAll(); require __DIR__ . '/../templates/admin/ai_models_list.php'; exit; });
            $r3->any('/list', function() { $models = AiModel::getAll(); require __DIR__ . '/../templates/admin/ai_models_list.php'; exit; });
            $r3->any('/create', function() { $isEdit = false; if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); try { AiModel::create($_POST); header('Location: /admin/ai-models'); exit; } catch (Exception $e) { $error = "创建失败: " . $e->getMessage(); $aiModel = $_POST; } } require __DIR__ . '/../templates/admin/ai_model_form.php'; exit; });
            $r3->any('#^/edit/(\d+)$#', function($id) { $isEdit = true; $aiModel = AiModel::find($id); if (!$aiModel) die("AI Model not found"); if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); try { AiModel::update($id, $_POST); header('Location: /admin/ai-models'); exit; } catch (Exception $e) { $error = "更新失败: " . $e->getMessage(); $aiModel = array_merge($aiModel, $_POST); } } require __DIR__ . '/../templates/admin/ai_model_form.php'; exit; });
            $r3->any('/delete', function() { $deleteId = $_GET['id'] ?? $_POST['id'] ?? null; if ($deleteId && $_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); AiModel::delete($deleteId); header('Location: /admin/ai-models'); } exit; });
        });

        // API Config CRUD + Generate
        $r2->group('/api', function(Router $r3) use ($settings) {
            $r3->any('/', function() { $apis = ApiConfig::getAll(); require __DIR__ . '/../templates/admin/api_list.php'; exit; });
            $r3->any('/list', function() { $apis = ApiConfig::getAll(); require __DIR__ . '/../templates/admin/api_list.php'; exit; });
            $r3->any('/publish', function() { $apiConfigs = ApiConfig::getAll(); require __DIR__ . '/../templates/admin/api_publish.php'; exit; });
            $r3->any('/create', function() { $isEdit = false; if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); try { ApiConfig::create($_POST); header('Location: /admin/api'); exit; } catch (Exception $e) { $error = "创建失败: " . $e->getMessage(); $api = $_POST; } } $categories = Category::getAll(); $mediaCategories = MediaCategory::getAll(); $aiModels = AiModel::getActive(); require __DIR__ . '/../templates/admin/api_form.php'; exit; });
            $r3->any('#^/edit/(\d+)$#', function($id) use ($settings) { $isEdit = true; $api = ApiConfig::find($id); if (!$api) die("API Config not found"); if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); try { ApiConfig::update($id, $_POST); if (!empty($api['scheme_id'])) { $scheme = AiScheme::find($api['scheme_id']); if ($scheme) { $currentConfig = $scheme['config'] ?? []; $newConfig = array_merge($currentConfig, [ 'region' => $_POST['geo_region'] ?? ($currentConfig['region'] ?? 'CN'), 'language' => $_POST['language'] ?? ($currentConfig['language'] ?? 'zh-CN'), 'keywords' => $_POST['keywords'] ?? ($currentConfig['keywords'] ?? ''), 'prompt' => $_POST['promotion_info'] ?? ($currentConfig['prompt'] ?? '') ]); AiScheme::update($api['scheme_id'], [ 'name' => $_POST['name'] ?? $scheme['name'], 'config' => $newConfig, 'target_count' => $_POST['target_count'] ?? $scheme['target_count'], 'daily_limit' => $_POST['daily_limit'] ?? $scheme['daily_limit'] ]); $newApiStatus = (int)($_POST['status'] ?? 0); if ($newApiStatus === 1 && in_array($scheme['status'], ['pending', 'rejected'])) { AiScheme::updateStatus($api['scheme_id'], 'approved'); } } } header('Location: /admin/api'); exit; } catch (Exception $e) { $error = "更新失败: " . $e->getMessage(); $api = array_merge($api, $_POST); } } $categories = Category::getAll(); $mediaCategories = MediaCategory::getAll(); $aiModels = AiModel::getActive(); require __DIR__ . '/../templates/admin/api_form.php'; exit; });
            $r3->any('/delete', function() { $id = $_GET['id'] ?? $_POST['id'] ?? null; if ($id && $_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); $api = ApiConfig::find($id); if ($api && !empty($api['scheme_id'])) { require_once __DIR__ . '/../src/Controllers/AdminAiSchemeController.php'; AiScheme::updateStatus($api['scheme_id'], 'rejected'); } ApiConfig::delete($id); header('Location: /admin/api'); } exit; });

            // AI Generate (admin side)
            $r3->post('/generate', function() use ($settings) {
                ini_set('display_errors', 0);
                while (ob_get_level()) { ob_end_clean(); }
                header('Content-Type: application/json; charset=utf-8');
                register_shutdown_function(function() { $error = error_get_last(); if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) { if (!headers_sent()) { header('Content-Type: application/json; charset=utf-8'); http_response_code(500); } echo json_encode(['success' => false, 'error' => 'Fatal Error: ' . $error['message']]); } });
                try {
                    Csrf::validateOrDie();
                    $input = json_decode(file_get_contents('php://input'), true);
                    if (!$input || empty($input['api_id']) || empty($input['selected_keywords'])) { throw new Exception("Invalid input parameters"); }
                    $apiId = $input['api_id']; $keywords = $input['selected_keywords'];
                    $api = ApiConfig::find($apiId); if (!$api) throw new Exception("API Config not found");
                    // Model selection (same logic as public API)
                    $geminiKey = ''; $geminiBaseUrl = ''; $geminiModelName = ''; $isLyApi = false;
                    if (!empty($api['ai_model_id'])) { $aiModel = AiModel::find($api['ai_model_id']); if ($aiModel && $aiModel['is_active']) { $geminiKey = $aiModel['api_key']; $geminiBaseUrl = $aiModel['base_url']; $geminiModelName = $aiModel['model_name']; $isLyApi = !empty($aiModel['is_ly_api']); } }
                    if (empty($geminiKey)) { $geminiKey = $settings['geminiApiKey'] ?? ''; $geminiBaseUrl = !empty($settings['geminiBaseUrl']) ? $settings['geminiBaseUrl'] : 'https://generativelanguage.googleapis.com'; $geminiModelName = !empty($settings['geminiModel']) ? $settings['geminiModel'] : 'gemini-2.0-flash-exp'; }
                    if (empty($geminiKey) && !$isLyApi) throw new Exception("未配置有效的 AI API Key");
                    require_once __DIR__ . '/../src/Services/GeminiService.php';
                    $service = new GeminiService($geminiKey, $geminiBaseUrl, $geminiModelName, $isLyApi);
                    $promotionInfo = $input['promotion_info'] ?? ($api['promotion_info'] ?? '');
                    $articleData = $service->generateArticle($keywords, $api['geo_region'], $api['language'], $api['category_id'], $promotionInfo);
                    $articleData['geo_region'] = $api['geo_region']; $articleData['language'] = $api['language']; $articleData['category_id'] = $api['category_id']; $articleData['api_config_id'] = $apiId;
                    if (isset($input['article_status'])) { $articleData['status'] = (int)$input['article_status']; if ($articleData['status'] === 0) $articleData['published_at'] = null; else $articleData['published_at'] = date('Y-m-d H:i:s'); }
                    $articleId = Article::create($articleData);
                    if (!empty($articleData['tags']) && is_array($articleData['tags'])) { Article::syncTags($articleId, $articleData['tags']); }
                    ApiConfig::incrementCallCount($apiId);
                    // Scheme progress
                    if (!empty($api['scheme_id']) && !empty($api['user_id'])) {
                        $scheme = AiScheme::find($api['scheme_id']); if ($scheme) { $cost = $scheme['cost_per_post'] ?? 1; $newGeneratedCount = $scheme['generated_count'] + 1; $reason = !empty($articleData['title']) ? "生成文章: {$articleData['title']}" : "生成文章"; User::consumeFrozenPoints($scheme['user_id'], $cost, $reason); AiScheme::updateProgress($scheme['id'], $newGeneratedCount, $cost); if ($newGeneratedCount >= $scheme['target_count']) { AiScheme::updateStatus($scheme['id'], 'completed'); ApiConfig::update($apiId, ['status' => 0]); } elseif ($scheme['status'] === 'approved') { AiScheme::updateStatus($scheme['id'], 'running'); } }
                    }
                    echo json_encode(['success' => true, 'article' => $articleData]);
                } catch (Throwable $e) { while (ob_get_level()) { ob_end_clean(); } header('Content-Type: application/json; charset=utf-8'); http_response_code(500); echo json_encode(['success' => false, 'error' => $e->getMessage()]); }
                exit;
            });
        });

        // Media Library
        $r2->group('/media', function(Router $r3) {
            $r3->any('/', function() { $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; $limit = 24; $offset = ($page - 1) * $limit; $currentCategoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null; $files = MediaFile::getAll($limit, $offset, $currentCategoryId); $total = MediaFile::countAll($currentCategoryId); $totalPages = ceil($total / $limit); $categories = MediaCategory::getAll(); require __DIR__ . '/../templates/admin/media_library.php'; exit; });
            $r3->post('/upload', function() { Csrf::validateOrDie(); $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null; $uploadedFiles = $_FILES['files']; $count = count($uploadedFiles['name']); for ($i = 0; $i < $count; $i++) { $file = ['name' => $uploadedFiles['name'][$i], 'type' => $uploadedFiles['type'][$i], 'tmp_name' => $uploadedFiles['tmp_name'][$i], 'error' => $uploadedFiles['error'][$i], 'size' => $uploadedFiles['size'][$i]]; if ($file['error'] === UPLOAD_ERR_OK) { MediaFile::handleUpload($file, $categoryId); } } header('Location: /admin/media' . ($categoryId ? "?category=$categoryId" : '')); exit; });
            $r3->post('/delete', function() { if (isset($_POST['id'])) { Csrf::validateOrDie(); MediaFile::delete((int)$_POST['id']); header('Location: ' . $_SERVER['HTTP_REFERER']); } exit; });
            $r3->post('/category/create', function() { Csrf::validateOrDie(); MediaCategory::create($_POST); header('Location: /admin/media'); exit; });
            $r3->post('/category/delete', function() { if (isset($_POST['id'])) { Csrf::validateOrDie(); MediaCategory::delete((int)$_POST['id']); header('Location: /admin/media'); } exit; });
        });

        // Settings
        $r2->any('/settings', function() { if ($_SERVER['REQUEST_METHOD'] === 'POST') { Csrf::validateOrDie(); $data = $_POST['settings'] ?? []; try { Settings::updateMany($data); header('Location: /admin/settings?success=1'); exit; } catch (Exception $e) { $error = "保存失败: " . $e->getMessage(); } } if (isset($_GET['success'])) { $success = true; } require __DIR__ . '/../templates/admin/settings.php'; exit; });
    }, $authCheck);
});

// ── Public API Routes ──
$router->group('/api', function(Router $r) use ($settings) {
    // Public API auth check
    $apiAuth = function() {
        $apiKey = $_GET['key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? '';
        if (!$apiKey) { http_response_code(401); echo json_encode(['success' => false, 'error' => 'Missing API Key']); exit; }
        $apiConfig = ApiConfig::findByKey($apiKey);
        if (!$apiConfig) { http_response_code(403); echo json_encode(['success' => false, 'error' => 'Invalid or disabled API Key']); exit; }
        return $apiConfig; // Pass to route handler
    };

    $r->any('/generate', function($apiConfig = null) use ($settings) {
        if (!$apiConfig) { $apiConfig = ApiConfig::findByKey($_GET['key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? ''); }
        ini_set('display_errors', 0); while (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        try {
            $keywordsPool = []; $storedKeywords = $apiConfig['keywords'] ?? '';
            $decoded = json_decode($storedKeywords, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) { $keywordsPool = $decoded; } else { $keywordsPool = array_filter(array_map('trim', explode("\n", $storedKeywords))); }
            if (empty($keywordsPool)) { http_response_code(400); echo json_encode(['success' => false, 'error' => 'No keywords configured']); exit; }
            $count = (int)($apiConfig['keyword_count'] ?? 1); if ($count < 1) $count = 1; $numToSelect = min($count, count($keywordsPool));
            $keys = array_rand($keywordsPool, $numToSelect); $keywords = []; if (is_array($keys)) { foreach ($keys as $key) $keywords[] = $keywordsPool[$key]; } else { $keywords[] = $keywordsPool[$keys]; }
            if (function_exists('set_time_limit')) set_time_limit(600);
            // Model selection
            $geminiKey = ''; $geminiBaseUrl = ''; $geminiModelName = ''; $isLyApi = false;
            if (!empty($apiConfig['ai_model_id'])) { $aiModel = AiModel::find($apiConfig['ai_model_id']); if ($aiModel && $aiModel['is_active']) { $geminiKey = $aiModel['api_key']; $geminiBaseUrl = $aiModel['base_url']; $geminiModelName = $aiModel['model_name']; $isLyApi = !empty($aiModel['is_ly_api']); } }
            if (empty($geminiKey)) { $geminiKey = $settings['geminiApiKey'] ?? ''; $geminiBaseUrl = !empty($settings['geminiBaseUrl']) ? $settings['geminiBaseUrl'] : 'https://generativelanguage.googleapis.com'; $geminiModelName = !empty($settings['geminiModel']) ? $settings['geminiModel'] : 'gemini-2.0-flash-exp'; }
            if (empty($geminiKey) && !$isLyApi) throw new Exception("System Gemini API Key not configured.");
            require_once __DIR__ . '/../src/Services/GeminiService.php';
            $service = new GeminiService($geminiKey, $geminiBaseUrl, $geminiModelName, $isLyApi);
            $articleData = $service->generateArticle($keywords, $apiConfig['geo_region'], $apiConfig['language'], $apiConfig['category_id'], $apiConfig['promotion_info'] ?? '');
            $articleData['category_id'] = !empty($apiConfig['category_id']) ? (int)$apiConfig['category_id'] : 0;
            $articleData['auto_link'] = !empty($apiConfig['auto_link']) ? (int)$apiConfig['auto_link'] : 0;
            $articleData['geo_region'] = $apiConfig['geo_region']; $articleData['language'] = $apiConfig['language'];
            // Image insertion
            $imageCount = (int)($apiConfig['insert_image_count'] ?? 0);
            if ($imageCount > 0) { /* ... same image insertion logic preserved ... */
                $position = $apiConfig['insert_image_position'] ?? 'random'; $sourceType = $apiConfig['image_source_type'] ?? 'picsum'; $content = $articleData['content']; $imagePool = [];
                if ($sourceType === 'custom_url' && !empty($apiConfig['custom_image_urls'])) { $imagePool = array_filter(array_map('trim', explode("\n", $apiConfig['custom_image_urls']))); }
                elseif ($sourceType === 'media_library' && !empty($apiConfig['media_category_id'])) { $mediaFiles = MediaFile::getAll(100, 0, (int)$apiConfig['media_category_id']); if (!empty($mediaFiles)) $imagePool = array_column($mediaFiles, 'path'); }
                $getImageMd = function($keyword) use ($sourceType, $imagePool) { $url = ''; if (($sourceType === 'custom_url' || $sourceType === 'media_library') && !empty($imagePool)) { $url = $imagePool[array_rand($imagePool)]; } else { $seed = crc32($keyword . uniqid()); $url = "https://picsum.photos/800/600?random=$seed"; } return "\n\n![Image related to $keyword]({$url})\n\n"; };
                if ($position === 'head') { for ($i = 0; $i < $imageCount; $i++) $content = $getImageMd($keywords[0]) . $content; }
                elseif ($position === 'tail') { for ($i = 0; $i < $imageCount; $i++) $content .= $getImageMd($keywords[0]); }
                elseif ($position === 'average') { $paragraphs = explode("\n\n", $content); $totalParagraphs = count($paragraphs); if ($totalParagraphs > $imageCount) { $interval = floor($totalParagraphs / ($imageCount + 1)); for ($i = 1; $i <= $imageCount; $i++) { $insertPos = $i * $interval; if (isset($paragraphs[$insertPos])) $paragraphs[$insertPos] .= $getImageMd($keywords[0]); } $content = implode("\n\n", $paragraphs); } else { $position = 'random'; } }
                if ($position === 'random') { $paragraphs = explode("\n\n", $content); $totalParagraphs = count($paragraphs); if ($totalParagraphs > 2) { for ($i = 0; $i < $imageCount; $i++) { $insertPos = rand(1, max(1, $totalParagraphs - 1)); if (isset($paragraphs[$insertPos])) $paragraphs[$insertPos] .= $getImageMd($keywords[0]); else $paragraphs[] = $getImageMd($keywords[0]); } $content = implode("\n\n", $paragraphs); } else { $content .= $getImageMd($keywords[0]); } }
                $articleData['content'] = $content;
                if (empty($articleData['cover_image'])) { if (($sourceType === 'custom_url' || $sourceType === 'media_library') && !empty($imagePool)) { $articleData['cover_image'] = $imagePool[array_rand($imagePool)]; } else { $articleData['cover_image'] = "https://picsum.photos/800/600?random=" . time(); } }
            }
            $articleData['status'] = isset($apiConfig['article_status']) ? (int)$apiConfig['article_status'] : 1;
            $articleId = Article::create($articleData); $articleData['id'] = $articleId;
            if (!empty($articleData['tags']) && is_array($articleData['tags'])) Article::syncTags($articleId, $articleData['tags']);
            ApiConfig::incrementCallCount($apiConfig['id']);
            // Scheme progress
            if (!empty($apiConfig['scheme_id']) && !empty($apiConfig['user_id'])) { $scheme = AiScheme::find($apiConfig['scheme_id']); if ($scheme) { $cost = $scheme['cost_per_post'] ?? 1; $newGeneratedCount = $scheme['generated_count'] + 1; $reason = !empty($articleData['title']) ? "生成文章: {$articleData['title']}" : "生成文章"; User::consumeFrozenPoints($scheme['user_id'], $cost, $reason); AiScheme::updateProgress($scheme['id'], $newGeneratedCount, $cost); if ($newGeneratedCount >= $scheme['target_count']) { AiScheme::updateStatus($scheme['id'], 'completed'); ApiConfig::update($apiConfig['id'], ['status' => 0]); } elseif ($scheme['status'] === 'approved') { AiScheme::updateStatus($scheme['id'], 'running'); } } }
            echo json_encode(['success' => true, 'data' => $articleData]);
        } catch (Throwable $e) { http_response_code(500); echo json_encode(['success' => false, 'error' => $e->getMessage()]); }
        exit;
    });

    $r->get('/articles', function($subId = null) {
        $apiConfig = ApiConfig::findByKey($_GET['key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? '');
        if (!$apiConfig) { http_response_code(403); echo json_encode(['success' => false, 'error' => 'Invalid API Key']); exit; }
        ini_set('display_errors', 0); while (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        if ($subId) { /* single article lookup */ $article = null; if (preg_match('/^[0-9a-f]{8}-/', $subId)) { $pdo = Database::getInstance()->getConnection(); $stmt = $pdo->prepare("SELECT * FROM articles WHERE uuid = ?"); $stmt->execute([$subId]); $article = $stmt->fetch(); } else { $article = Article::getBySlug($subId); } if ($article) { echo json_encode(['success' => true, 'data' => $article]); } else { http_response_code(404); echo json_encode(['success' => false, 'error' => 'Article not found']); } } else { $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; $offset = ($page - 1) * $limit; $articles = Article::getLatest($limit, $offset); echo json_encode(['success' => true, 'data' => $articles]); }
        exit;
    });
    $r->get('#^/articles/(.+)$#', function($subId) { /* reroutes to /api/articles handler */ $apiConfig = ApiConfig::findByKey($_GET['key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? ''); if (!$apiConfig) { http_response_code(403); echo json_encode(['success' => false, 'error' => 'Invalid API Key']); exit; } ini_set('display_errors', 0); while (ob_get_level()) { ob_end_clean(); } header('Content-Type: application/json; charset=utf-8'); $article = null; if (preg_match('/^[0-9a-f]{8}-/', $subId)) { $pdo = Database::getInstance()->getConnection(); $stmt = $pdo->prepare("SELECT * FROM articles WHERE uuid = ?"); $stmt->execute([$subId]); $article = $stmt->fetch(); } else { $article = Article::getBySlug($subId); } if ($article) { echo json_encode(['success' => true, 'data' => $article]); } else { http_response_code(404); echo json_encode(['success' => false, 'error' => 'Article not found']); } exit; });

    // API 404 fallback
    $r->any('/*', function() { http_response_code(404); echo json_encode(['success' => false, 'error' => 'API endpoint not found']); exit; });
});

// ═══════════════════════════════════════════════════════════════
// Dispatch
// ═══════════════════════════════════════════════════════════════

$result = $router->dispatch($method, $uri);

if ($result !== false && $result !== null) {
    exit;
}

// ── If Router didn't handle it, fall through to frontend routes ──

// Blog redirects
if ($uri === '/blog') { header("Location: /news", true, 301); exit; }
if (strpos($uri, '/blog/') === 0) { $newUri = '/news' . substr($uri, 5); header("Location: " . $newUri, true, 301); exit; }

// Homepage
$showLandingAsHomepage = $settings['showLandingAsHomepage'] ?? true;
if ($uri === '/' || $uri === '/index.php') {
    if ($showLandingAsHomepage) {
        $categories = Category::getAll();
        $latestArticles = Article::getLatest(6);
        require __DIR__ . '/../templates/landing.php';
    } else {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($settings['articlesPerPage']) ? (int)$settings['articlesPerPage'] : 10;
        $offset = ($page - 1) * $limit;
        $articles = Article::getLatest($limit, $offset);
        $total = Article::countPublished();
        $totalPages = ceil($total / $limit);
        require __DIR__ . '/../templates/home.php';
    }
    exit;
}

// News page
if ($uri === '/news') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($settings['articlesPerPage']) ? (int)$settings['articlesPerPage'] : 10;
    $offset = ($page - 1) * $limit;
    $articles = Article::getLatest($limit, $offset);
    $total = Article::countPublished();
    $totalPages = ceil($total / $limit);
    require __DIR__ . '/../templates/home.php';
    exit;
}

// Article detail (root path)
$parts = explode('/', trim($uri, '/'));
if (count($parts) === 1 && !empty($parts[0])) {
    $slug = $parts[0];
    $article = Article::getBySlug($slug);
    if ($article) {
        Article::incrementViews($article['id']);
        $category = null;
        if (!empty($article['category_id'])) { $category = Category::find($article['category_id']); }
        $tags = Tag::getByArticle($article['id']);
        require __DIR__ . '/../templates/article.php';
        exit;
    }
    // Not an article — 404
}

// Category page
if (count($parts) === 2 && $parts[0] === 'category') {
    $slug = $parts[1];
    $category = Category::getBySlug($slug);
    if ($category) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($settings['articlesPerPage']) ? (int)$settings['articlesPerPage'] : 10;
        $offset = ($page - 1) * $limit;
        $articles = Article::getByCategory($category['id'], $limit, $offset);
        $total = Article::countByCategory($category['id']);
        $totalPages = ceil($total / $limit);
        require __DIR__ . '/../templates/category.php';
        exit;
    }
}

// Search
if ($uri === '/search') {
    $query = $_GET['q'] ?? '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($settings['articlesPerPage']) ? (int)$settings['articlesPerPage'] : 10;
    $offset = ($page - 1) * $limit;
    $articles = Article::search($query, $limit, $offset);
    $total = Article::countSearch($query);
    $totalPages = ceil($total / $limit);
    require __DIR__ . '/../templates/search.php';
    exit;
}

// 404
http_response_code(404);
header('HTTP/1.0 404 Not Found');
require __DIR__ . '/../templates/404.php';
