<?php
// php_backend/public/index.php
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_samesite' => 'Strict'
]);

// ── Plugin System: Hooks + Auto-loader ──
require_once __DIR__ . '/../src/Core/Hooks.php';
require_once __DIR__ . '/../src/Core/Plugin.php';

// Make hook functions globally available (WordPress-style)
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

// Load active plugins (must be before any hook execution)
Plugin::loadActive();

// 简单的路由分发器
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

// Spider/Bot Detection Middleware
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$botInfo = SpiderLog::identifyBot($userAgent);

if ($botInfo) {
    // Log it
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // Avoid logging admin pages or assets
    if (strpos($path, '/admin') === false && strpos($path, '/assets') === false) {
        $realIp = SpiderLog::getRealIp();
        SpiderLog::log($botInfo['name'], $botInfo['type'], $realIp, $userAgent, $path, http_response_code());
    }
}

// 检查是否安装
if (!file_exists(__DIR__ . '/../config.php')) {
    header('Location: /install/index.php');
    exit;
}
require_once __DIR__ . '/../config.php';

// 简单路由
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri); // 解码 URL，防止中文路径无法匹配

require_once __DIR__ . '/../src/Controllers/UserController.php';

// API Routes
if ($uri === '/api/send-sms') {
    require_once __DIR__ . '/api/send_sms.php';
    exit;
}

// RSS Feed
if ($uri === '/rss.xml') {
    require_once __DIR__ . '/../src/Utils/RssGenerator.php';
    header('Content-Type: application/xml; charset=utf-8');
    echo RssGenerator::generate();
    exit;
}

// Sitemap
if ($uri === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    echo SitemapGenerator::generate();
    exit;
}

// 获取系统设置
try {
    $settings = Settings::getAll();
} catch (Exception $e) {
    die("Error loading settings: " . $e->getMessage());
}

// 去除 .html 后缀以支持伪静态
if (substr($uri, -5) === '.html') {
    $uri = substr($uri, 0, -5);
}

$parts = explode('/', trim($uri, '/'));

// User Auth Routes
if ($uri === '/login') {
    UserController::login();
    exit;
}

if ($uri === '/forgot-password') {
    UserController::forgotPassword();
    exit;
}

if ($uri === '/register') {
    UserController::register();
    exit;
}

if ($uri === '/logout') {
    UserController::logout();
    exit;
}

// User Center Routes
if (strpos($uri, '/user') === 0) {
    if ($uri === '/user/center') {
        UserController::center();
        exit;
    }
    if ($uri === '/user/profile') {
        UserController::profile();
        exit;
    }
    if ($uri === '/user/bind-phone') {
        UserController::bindPhone();
        exit;
    }
    if ($uri === '/user/security') {
        UserController::security();
        exit;
    }
    
    // Point History
    if ($uri === '/user/point-history') {
        UserController::pointHistory();
        exit;
    }
    
    // AI Schemes
    if ($uri === '/user/ai-schemes') {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        UserAiSchemeController::index();
        exit;
    }
    
    // Recharge
    if ($uri === '/user/recharge') {
        require_once __DIR__ . '/../src/Controllers/UserRechargeController.php';
        UserRechargeController::index();
        exit;
    }
    if ($uri === '/user/recharge/create') {
        require_once __DIR__ . '/../src/Controllers/UserRechargeController.php';
        UserRechargeController::create();
        exit;
    }
    if ($uri === '/user/ai-schemes/create') {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            UserAiSchemeController::store();
        } else {
            UserAiSchemeController::create();
        }
        exit;
    }

    if (preg_match('#^/user/ai-schemes/edit/(\d+)$#', $uri, $matches)) {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            UserAiSchemeController::update($matches[1]);
        } else {
            UserAiSchemeController::edit($matches[1]);
        }
        exit;
    }

    if (preg_match('#^/user/ai-schemes/resubmit/(\d+)$#', $uri, $matches)) {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        UserAiSchemeController::resubmit($matches[1]);
        exit;
    }

    if (preg_match('#^/user/ai-schemes/delete/(\d+)$#', $uri, $matches)) {
        require_once __DIR__ . '/../src/Controllers/UserAiSchemeController.php';
        UserAiSchemeController::destroy($matches[1]);
        exit;
    }
}

// Admin Routes
    if (isset($parts[0]) && $parts[0] === 'admin') {
        
        // Point Packages
        if ($uri === '/admin/point-packages') {
            require_once __DIR__ . '/../src/Controllers/AdminPointPackageController.php';
            AdminPointPackageController::index();
            exit;
        }
        if ($uri === '/admin/point-packages/create') {
            require_once __DIR__ . '/../src/Controllers/AdminPointPackageController.php';
            AdminPointPackageController::create();
            exit;
        }
        if ($uri === '/admin/point-packages/edit') {
            require_once __DIR__ . '/../src/Controllers/AdminPointPackageController.php';
            AdminPointPackageController::edit($_GET['id'] ?? 0);
            exit;
        }
        if ($uri === '/admin/point-packages/delete') {
            require_once __DIR__ . '/../src/Controllers/AdminPointPackageController.php';
            AdminPointPackageController::delete($_GET['id'] ?? 0);
            exit;
        }

        // Recharge Orders
        if ($uri === '/admin/recharge-orders') {
            require_once __DIR__ . '/../src/Controllers/AdminRechargeController.php';
            AdminRechargeController::index();
            exit;
        }
        if ($uri === '/admin/recharge-orders/approve') {
            require_once __DIR__ . '/../src/Controllers/AdminRechargeController.php';
            AdminRechargeController::approve();
            exit;
        }
        if ($uri === '/admin/recharge-orders/reject') {
            require_once __DIR__ . '/../src/Controllers/AdminRechargeController.php';
            AdminRechargeController::reject();
            exit;
        }
        if ($uri === '/admin/recharge-orders/update-remark') {
            require_once __DIR__ . '/../src/Controllers/AdminRechargeController.php';
            AdminRechargeController::updateRemark();
            exit;
        }    // Check Login
    $isLoggedIn = isset($_SESSION['user_id']);

    // Login Page
    if (isset($parts[1]) && $parts[1] === 'login') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = User::findByEmail($email);
            if ($user && User::verifyPassword($user, $password)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                User::updateLoginInfo($user['id'], $_SERVER['REMOTE_ADDR']);
                header('Location: /admin');
                exit;
            } else {
                $error = "邮箱或密码错误";
                require __DIR__ . '/../templates/admin/login.php';
                exit;
            }
        }
        
        // Show Login Form
        require __DIR__ . '/../templates/admin/login.php';
        exit;
    }

    // Logout
    if (isset($parts[1]) && $parts[1] === 'logout') {
        session_destroy();
        header('Location: /admin/login');
        exit;
    }

    // Auth Check for other admin pages
    if (!$isLoggedIn) {
        header('Location: /admin/login');
        exit;
    }

    // Role Check
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header('HTTP/1.1 403 Forbidden');
        echo "<h1>403 Access Denied</h1><p>You do not have administrator privileges.</p><p><a href='/'>Back to Home</a></p>";
        exit;
    }

    // Dashboard
    if (!isset($parts[1]) || $parts[1] === '') {
        // Fetch stats for dashboard
        $stats = [
            'total_articles' => Article::countAll(),
            'total_views' => Article::sumViews(),
            'api_calls' => ApiConfig::sumCallCounts()
        ];
        require __DIR__ . '/../templates/admin/dashboard.php';
        exit;
    }

    // User Management
    if ($parts[1] === 'users') {
        require_once __DIR__ . '/../src/Controllers/AdminUserController.php';
        
        $action = $parts[2] ?? 'index';
        
        // List
        if ($action === 'index' || $action === '') {
            AdminUserController::index();
            exit;
        }
        
        // Edit
        if ($action === 'edit') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                AdminUserController::edit($id);
                exit;
            }
        }
        
        // Update
        if ($action === 'update') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                AdminUserController::update($id);
                exit;
            }
        }
        
        // Delete
        if ($action === 'delete') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                AdminUserController::delete($id);
                exit;
            }
        }
        
        header('Location: /admin/users');
        exit;
    }

    // AI Scheme Management
    if ($parts[1] === 'ai-schemes') {
        require_once __DIR__ . '/../src/Controllers/AdminAiSchemeController.php';
        
        $action = $parts[2] ?? 'list';
        $id = $parts[3] ?? null;
        
        if ($action === 'list' || $action === '') {
            AdminAiSchemeController::index();
            exit;
        }
        
        if ($action === 'approve' && $id) {
            AdminAiSchemeController::approve($id);
            exit;
        }
        
        if ($action === 'reject' && $id) {
            AdminAiSchemeController::reject($id);
            exit;
        }
    }


    // Announcement Management
    if ($parts[1] === 'announcements') {
        require_once __DIR__ . '/../src/Controllers/AdminAnnouncementController.php';
        
        $action = $parts[2] ?? 'index';
        
        if ($action === 'index' || $action === '') {
            AdminAnnouncementController::index();
            exit;
        }
        
        if ($action === 'create') {
            AdminAnnouncementController::create();
            exit;
        }
        
        if ($action === 'edit') {
            $id = $parts[3] ?? null; // /admin/announcements/edit/123
            if ($id) {
                AdminAnnouncementController::edit($id);
                exit;
            }
        }
        
        if ($action === 'delete') {
            $id = $parts[3] ?? null; // /admin/announcements/delete/123
            if ($id) {
                AdminAnnouncementController::delete($id);
                exit;
            }
        }
    }

    // System Update Management
    if ($parts[1] === 'system-updates') {
        require_once __DIR__ . '/../src/Controllers/AdminSystemUpdateController.php';
        
        $action = $parts[2] ?? 'index';
        
        if ($action === 'index' || $action === '') {
            AdminSystemUpdateController::index();
            exit;
        }
        
        if ($action === 'create') {
            AdminSystemUpdateController::create();
            exit;
        }
        
        if ($action === 'edit') {
            $id = $parts[3] ?? null;
            if ($id) {
                AdminSystemUpdateController::edit($id);
                exit;
            }
        }
        
        if ($action === 'delete') {
            $id = $parts[3] ?? null;
            if ($id) {
                AdminSystemUpdateController::delete($id);
                exit;
            }
        }
    }

    // Plugin Management
    if ($parts[1] === 'plugins') {
        require __DIR__ . '/../templates/admin/plugins_list.php';
        exit;
    }

    // Template Management
    if ($parts[1] === 'templates') {
        require __DIR__ . '/../templates/admin/templates_list.php';
        exit;
    }

    // System Update
    if ($parts[1] === 'update') {
        require __DIR__ . '/../templates/admin/update.php';
        exit;
    }

    // Article Management
    if ($parts[1] === 'articles') {
        $action = $parts[2] ?? 'list';
        // Handle ID from GET or POST or Path (fallback)
        $id = $_GET['id'] ?? $_POST['id'] ?? ($parts[3] ?? null);

        // List
        if ($action === 'list' || $action === '') {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;
            
            $articles = Article::getAll($limit, $offset);
            $total = Article::countAll();
            $totalPages = ceil($total / $limit);
            
            require __DIR__ . '/../templates/admin/articles_list.php';
            exit;
        }
        
        // Create
        if ($action === 'create') {
            $isEdit = false;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                try {
                    $newId = Article::create($_POST);
                    do_action('article_saved', $newId, $_POST);
                    if (isset($_POST['tags'])) {
                        $tags = explode(',', $_POST['tags']);
                        Article::syncTags($newId, $tags);
                    }
                    header('Location: /admin/articles');
                    exit;
                } catch (Exception $e) {
                    $error = "创建失败: " . $e->getMessage();
                    $article = $_POST; // Preserve input
                }
            }
            require __DIR__ . '/../templates/admin/article_form.php';
            exit;
        }
        
        // Edit
        if ($action === 'edit') {
            $isEdit = true;
            if (!$id) {
                die("Article ID not provided");
            }
            
            $article = Article::find($id);
            if (!$article) {
                die("Article not found");
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                try {
                    Article::update($id, $_POST);
                    if (isset($_POST['tags'])) {
                        $tags = explode(',', $_POST['tags']);
                        Article::syncTags($id, $tags);
                    }
                    header('Location: /admin/articles');
                    exit;
                } catch (Exception $e) {
                    $error = "更新失败: " . $e->getMessage();
                    $article = array_merge($article, $_POST); // Preserve input
                }
            }
            require __DIR__ . '/../templates/admin/article_form.php';
            exit;
        }
        
        // Delete
        if ($action === 'delete') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                Csrf::validateOrDie();
                Article::delete($id);
            }
            header('Location: /admin/articles');
            exit;
        }
    }

    // Category Management
    if ($parts[1] === 'categories') {
        $action = $parts[2] ?? 'list';
        $id = $parts[3] ?? null;

        if ($action === 'list' || $action === '') {
            $categories = Category::getAll();
            require __DIR__ . '/../templates/admin/categories_list.php';
            exit;
        }

        if ($action === 'create') {
            $isEdit = false;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                try {
                    Category::create($_POST);
                    header('Location: /admin/categories');
                    exit;
                } catch (Exception $e) {
                    $error = "创建失败: " . $e->getMessage();
                    $category = $_POST;
                }
            }
            // Need all categories for parent selection
            $allCategories = Category::getAll();
            require __DIR__ . '/../templates/admin/category_form.php';
            exit;
        }

        if ($action === 'edit' && $id) {
            $isEdit = true;
            $category = Category::find($id);
            if (!$category) die("Category not found");

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                try {
                    Category::update($id, $_POST);
                    header('Location: /admin/categories');
                    exit;
                } catch (Exception $e) {
                    $error = "更新失败: " . $e->getMessage();
                    $category = array_merge($category, $_POST);
                }
            }
            $allCategories = Category::getAll();
            require __DIR__ . '/../templates/admin/category_form.php';
            exit;
        }

        if ($action === 'delete' && $id) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                try {
                    Category::delete($id);
                    header('Location: /admin/categories');
                } catch (Exception $e) {
                    echo "<script>alert('" . addslashes($e->getMessage()) . "'); window.location.href='/admin/categories';</script>";
                }
            }
            exit;
        }
    }

    // Tag Management
    if ($parts[1] === 'tags') {
        $action = $parts[2] ?? 'list';
        $id = $parts[3] ?? null;

        if ($action === 'list' || $action === '') {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;

            $tags = Tag::getAll($limit, $offset);
            $total = Tag::countAll();
            $totalPages = ceil($total / $limit);
            
            require __DIR__ . '/../templates/admin/tags_list.php';
            exit;
        }

        if ($action === 'create') {
            $isEdit = false;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    Tag::create($_POST);
                    header('Location: /admin/tags');
                    exit;
                } catch (Exception $e) {
                    $error = "创建失败: " . $e->getMessage();
                    $tag = $_POST;
                }
            }
            require __DIR__ . '/../templates/admin/tag_form.php';
            exit;
        }

        if ($action === 'edit' && $id) {
            $isEdit = true;
            $tag = Tag::find($id);
            if (!$tag) die("Tag not found");

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    Tag::update($id, $_POST);
                    header('Location: /admin/tags');
                    exit;
                } catch (Exception $e) {
                    $error = "更新失败: " . $e->getMessage();
                    $tag = array_merge($tag, $_POST);
                }
            }
            require __DIR__ . '/../templates/admin/tag_form.php';
            exit;
        }

        if ($action === 'delete' && $id) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                Tag::delete($id);
                header('Location: /admin/tags');
            }
            exit;
        }
    }

    // AI Model Management
    if ($parts[1] === 'ai-models') {
        $action = $parts[2] ?? 'list';
        $id = $parts[3] ?? null;

        if ($action === 'list' || $action === '') {
            $models = AiModel::getAll();
            require __DIR__ . '/../templates/admin/ai_models_list.php';
            exit;
        }

        if ($action === 'create') {
            $isEdit = false;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                try {
                    AiModel::create($_POST);
                    header('Location: /admin/ai-models');
                    exit;
                } catch (Exception $e) {
                    $error = "创建失败: " . $e->getMessage();
                    $aiModel = $_POST;
                }
            }
            require __DIR__ . '/../templates/admin/ai_model_form.php';
            exit;
        }

        if ($action === 'edit' && $id) {
            $isEdit = true;
            $aiModel = AiModel::find($id);
            if (!$aiModel) die("AI Model not found");

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                try {
                    AiModel::update($id, $_POST);
                    header('Location: /admin/ai-models');
                    exit;
                } catch (Exception $e) {
                    $error = "更新失败: " . $e->getMessage();
                    $aiModel = array_merge($aiModel, $_POST);
                }
            }
            require __DIR__ . '/../templates/admin/ai_model_form.php';
            exit;
        }

        if ($action === 'delete') {
            $deleteId = $id ?? $_POST['id'] ?? null;
            if ($deleteId && $_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                AiModel::delete($deleteId);
                header('Location: /admin/ai-models');
            }
            exit;
        }
    }

    // API Management
    if ($parts[1] === 'api') {
        $action = $parts[2] ?? 'list';
        $id = $parts[3] ?? null;

        // AI Publish Page
        if ($action === 'publish') {
            $apiConfigs = ApiConfig::getAll();
            require __DIR__ . '/../templates/admin/api_publish.php';
            exit;
        }

        // AI Generation Handler
        if ($action === 'generate') {
            // Disable error display to prevent HTML pollution
            ini_set('display_errors', 0);
            
            // Clean output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            header('Content-Type: application/json; charset=utf-8');
            
            // Register shutdown function to catch fatal errors
            register_shutdown_function(function() {
                $error = error_get_last();
                if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
                    // If headers not sent, send JSON header
                    if (!headers_sent()) {
                        header('Content-Type: application/json; charset=utf-8');
                        http_response_code(500);
                    }
                    echo json_encode(['success' => false, 'error' => 'Fatal Error: ' . $error['message']]);
                }
            });

            try {
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    throw new Exception("Method not allowed");
                }
                
                Csrf::validateOrDie();

                $input = json_decode(file_get_contents('php://input'), true);
                if (!$input || empty($input['api_id']) || empty($input['selected_keywords'])) {
                    throw new Exception("Invalid input parameters");
                }
                
                $apiId = $input['api_id'];
                $keywords = $input['selected_keywords']; // Array
                
                $api = ApiConfig::find($apiId);
                if (!$api) {
                    throw new Exception("API Config not found");
                }

                // Determine AI Model Config
                $geminiKey = '';
                $geminiBaseUrl = '';
                $geminiModelName = '';
                $isLyApi = false;
                $usedModelSource = 'unknown'; // Debug info

                // 1. Check if specific AI Model is assigned to this API Config
                if (!empty($api['ai_model_id'])) {
                    $aiModel = AiModel::find($api['ai_model_id']);
                    
                    if ($aiModel && $aiModel['is_active']) {
                        $geminiKey = $aiModel['api_key'];
                        $geminiBaseUrl = $aiModel['base_url'];
                        $geminiModelName = $aiModel['model_name'];
                        $isLyApi = !empty($aiModel['is_ly_api']);
                        $usedModelSource = 'assigned_model: ' . $aiModel['name'];
                    }
                }

                // 2. Fallback to System Settings if no valid model found
                if (empty($geminiKey)) {
                    $geminiKey = $settings['geminiApiKey'] ?? '';
                    $geminiBaseUrl = !empty($settings['geminiBaseUrl']) ? $settings['geminiBaseUrl'] : 'https://generativelanguage.googleapis.com';
                    $geminiModelName = !empty($settings['geminiModel']) ? $settings['geminiModel'] : 'gemini-2.0-flash-exp';
                    $usedModelSource = 'system_fallback';
                }

                if (empty($geminiKey) && !$isLyApi) {
                    throw new Exception("未配置有效的 AI API Key。请在“AI 模型管理”中配置模型并分配给此 API 方案，或在“系统设置”中配置默认 Gemini API。");
                }
                
                require_once __DIR__ . '/../src/Services/GeminiService.php';
                $service = new GeminiService($geminiKey, $geminiBaseUrl, $geminiModelName, $isLyApi);
                
                // Use promotion info from request if available, otherwise use config
                $promotionInfo = isset($input['promotion_info']) ? $input['promotion_info'] : ($api['promotion_info'] ?? '');

                $articleData = $service->generateArticle(
                    $keywords, 
                    $api['geo_region'], 
                    $api['language'], 
                    $api['category_id'],
                    $promotionInfo
                );
                
                // Inject metadata from config into article data to ensure correct mapping
                $articleData['geo_region'] = $api['geo_region'];
                $articleData['language'] = $api['language'];
                $articleData['category_id'] = $api['category_id'];
                $articleData['api_config_id'] = $apiId;
                
                // Override status if provided in request
                if (isset($input['article_status'])) {
                    $articleData['status'] = (int)$input['article_status'];
                    // If draft, clear published_at
                    if ($articleData['status'] === 0) {
                        $articleData['published_at'] = null;
                    } else {
                         $articleData['published_at'] = date('Y-m-d H:i:s');
                    }
                }

                // Create Article
                $articleId = Article::create($articleData);
                
                // Reload article to get the final slug (in case of deduplication)
                $savedArticle = Article::find($articleId);
                if ($savedArticle) {
                    // Preserve tags from input as they are not in articles table yet
                    $tags = $articleData['tags'] ?? [];
                    $articleData = $savedArticle;
                    $articleData['tags'] = $tags;
                } else {
                    $articleData['id'] = $articleId;
                }
                
                // Sync Tags if available
                if (!empty($articleData['tags']) && is_array($articleData['tags'])) {
                    Article::syncTags($articleId, $articleData['tags']);
                }

                // Increment call count
                ApiConfig::incrementCallCount($apiId);

                // Handle User AI Scheme Progress
                if (!empty($api['scheme_id']) && !empty($api['user_id'])) {
                    require_once __DIR__ . '/../src/Models/AiScheme.php';
                    require_once __DIR__ . '/../src/Models/User.php';
                    
                    $scheme = AiScheme::find($api['scheme_id']);
                    if ($scheme) {
                        $cost = $scheme['cost_per_post'] ?? 1;
                        $newGeneratedCount = $scheme['generated_count'] + 1;
                        
                        // Consume frozen points in User table
                        $articlePath = !empty($articleData['slug']) ? '/' . $articleData['slug'] . '.html' : '';
                        $reason = !empty($articleData['title']) ? "生成文章: {$articleData['title']}" : "生成文章";
                        if (!empty($articlePath)) {
                            $reason .= " ({$articlePath})";
                        }
                        User::consumeFrozenPoints($scheme['user_id'], $cost, $reason);
                        
                        // Update Scheme Progress (updates generated_count and reduces frozen_points in scheme table)
                        AiScheme::updateProgress($scheme['id'], $newGeneratedCount, $cost);
                        
                        // Check completion
                        if ($newGeneratedCount >= $scheme['target_count']) {
                            AiScheme::updateStatus($scheme['id'], 'completed');
                            // Disable API Config
                            ApiConfig::update($apiId, ['status' => 0]);
                        } else {
                            // Ensure status is running if it was approved (just in case)
                            if ($scheme['status'] === 'approved') {
                                AiScheme::updateStatus($scheme['id'], 'running');
                            }
                        }
                    }
                }

                // Attach debug info about model used
                $articleData['debug_model_source'] = $usedModelSource;
                $articleData['debug_model_name'] = $geminiModelName;
                $articleData['debug_api_ai_model_id'] = $api['ai_model_id'] ?? 'null';

                echo json_encode(['success' => true, 'article' => $articleData]);

            } catch (Throwable $e) {
                // Clean output buffer again before sending error
                while (ob_get_level()) {
                    ob_end_clean();
                }
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'list' || $action === '') {
            $apis = ApiConfig::getAll();
            require __DIR__ . '/../templates/admin/api_list.php';
            exit;
        }

        if ($action === 'create') {
            $isEdit = false;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                try {
                    ApiConfig::create($_POST);
                    header('Location: /admin/api');
                    exit;
                } catch (Exception $e) {
                    $error = "创建失败: " . $e->getMessage();
                    $api = $_POST;
                }
            }
            $categories = Category::getAll();
            $mediaCategories = MediaCategory::getAll(); // Fetch Media Categories
            $aiModels = AiModel::getActive(); // Fetch Active AI Models
            require __DIR__ . '/../templates/admin/api_form.php';
            exit;
        }

        if ($action === 'edit' && $id) {
            $isEdit = true;
            $api = ApiConfig::find($id);
            if (!$api) die("API Config not found");

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                try {
                    ApiConfig::update($id, $_POST);
                    
                    // Sync updates to AiScheme if this API config belongs to a user scheme
                    if (!empty($api['scheme_id'])) {
                        $scheme = AiScheme::find($api['scheme_id']);
                        if ($scheme) {
                            $currentConfig = $scheme['config'] ?? []; // find() already decodes json
                            $newConfig = array_merge($currentConfig, [
                                'region' => $_POST['geo_region'] ?? ($currentConfig['region'] ?? 'CN'),
                                'language' => $_POST['language'] ?? ($currentConfig['language'] ?? 'zh-CN'),
                                'keywords' => $_POST['keywords'] ?? ($currentConfig['keywords'] ?? ''),
                                'prompt' => $_POST['promotion_info'] ?? ($currentConfig['prompt'] ?? '')
                            ]);
                            
                            $syncName = $_POST['name'];
                            $suffix = ' (User Scheme)';
                            if (substr($syncName, -strlen($suffix)) === $suffix) {
                                $syncName = substr($syncName, 0, -strlen($suffix));
                            }
                            
                            AiScheme::update($api['scheme_id'], [
                                'name' => $syncName,
                                'daily_limit' => $_POST['daily_limit'] ?? 0,
                                'config' => $newConfig
                            ]);
                            
                            // Auto-approve scheme if API is enabled
                            $newApiStatus = (int)($_POST['status'] ?? 0);
                            if ($newApiStatus === 1 && in_array($scheme['status'], ['pending', 'rejected'])) {
                                AiScheme::updateStatus($api['scheme_id'], 'approved');
                            }
                        }
                    }

                    header('Location: /admin/api');
                    exit;
                } catch (Exception $e) {
                    $error = "更新失败: " . $e->getMessage();
                    $api = array_merge($api, $_POST);
                }
            }
            $categories = Category::getAll();
            $mediaCategories = MediaCategory::getAll(); // Fetch Media Categories
            $aiModels = AiModel::getActive(); // Fetch Active AI Models
            require __DIR__ . '/../templates/admin/api_form.php';
            exit;
        }

        if ($action === 'delete' && $id) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                
                // Check for user scheme and handle cleanup
                $api = ApiConfig::find($id);
                if ($api && !empty($api['scheme_id'])) {
                    $scheme = AiScheme::find($api['scheme_id']);
                    if ($scheme) {
                        // Refund points
                        require_once __DIR__ . '/../src/Models/User.php';
                        User::unfreezePoints($scheme['user_id'], $scheme['frozen_points']);
                        
                        // Delete scheme
                        AiScheme::delete($api['scheme_id']);
                    }
                }
                
                ApiConfig::delete($id);
            }
            header('Location: /admin/api');
            exit;
        }
    }

    

    // Spider Logs
    if ($parts[1] === 'spider-logs') {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $logs = SpiderLog::getAll($limit, $offset);
        $total = SpiderLog::countAll();
        $totalPages = ceil($total / $limit);
        
        $stats = SpiderLog::getStats(7); // Get stats for last 7 days

        require __DIR__ . '/../templates/admin/spider_logs.php';
        exit;
    }

    // Media Library
    if ($parts[1] === 'media') {
        $action = $parts[2] ?? 'list';
        
        // List Media
        if ($action === 'list' || $action === '') {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 24;
            $offset = ($page - 1) * $limit;
            $currentCategoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
            
            $files = MediaFile::getAll($limit, $offset, $currentCategoryId);
            $total = MediaFile::countAll($currentCategoryId);
            $totalPages = ceil($total / $limit);
            $categories = MediaCategory::getAll();
            
            require __DIR__ . '/../templates/admin/media_library.php';
            exit;
        }
        
        // Upload
        if ($action === 'upload') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
                $uploadedFiles = $_FILES['files'];
                $count = count($uploadedFiles['name']);
                
                for ($i = 0; $i < $count; $i++) {
                    $file = [
                        'name' => $uploadedFiles['name'][$i],
                        'type' => $uploadedFiles['type'][$i],
                        'tmp_name' => $uploadedFiles['tmp_name'][$i],
                        'error' => $uploadedFiles['error'][$i],
                        'size' => $uploadedFiles['size'][$i]
                    ];
                    
                    if ($file['error'] === UPLOAD_ERR_OK) {
                        MediaFile::handleUpload($file, $categoryId);
                    }
                }
                
                header('Location: /admin/media' . ($categoryId ? "?category=$categoryId" : ''));
                exit;
            }
        }
        
        // Delete File
        if ($action === 'delete') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                Csrf::validateOrDie();
                MediaFile::delete((int)$_POST['id']);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }
        
        // Category Management
        if ($action === 'category') {
            $subAction = $parts[3] ?? '';
            
            if ($subAction === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::validateOrDie();
                MediaCategory::create($_POST);
                header('Location: /admin/media');
                exit;
            }
            
            if ($subAction === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                Csrf::validateOrDie();
                MediaCategory::delete((int)$_POST['id']);
                header('Location: /admin/media');
                exit;
            }
        }
        
        exit;
    }

    // Settings Page
    if ($parts[1] === 'settings') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validateOrDie();
            $data = $_POST['settings'] ?? [];
            try {
                Settings::updateMany($data);
                // Redirect to avoid form resubmission
                header('Location: /admin/settings?success=1');
                exit;
            } catch (Exception $e) {
                $error = "保存失败: " . $e->getMessage();
            }
        }
        
        // Check for success param
        if (isset($_GET['success'])) {
            $success = true;
        }
        
        require __DIR__ . '/../templates/admin/settings.php';
        exit;
    }
    
    // Placeholder for other admin pages
    echo "Admin Page: " . htmlspecialchars($parts[1]);
    exit;
}

// API Routes (Public)
if (isset($parts[0]) && $parts[0] === 'api') {
    // Disable error display to prevent HTML pollution
    ini_set('display_errors', 0);
    
    // Clean output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: application/json; charset=utf-8');
    
    // Auth Check
    $apiKey = $_GET['key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? '';
    if (!$apiKey) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Missing API Key (key param or X-API-Key header)']);
        exit;
    }
    
    $apiConfig = ApiConfig::findByKey($apiKey);
    if (!$apiConfig) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid or disabled API Key']);
        exit;
    }

    $endpoint = $parts[1] ?? '';
    $subId = $parts[2] ?? null;

    // GET/POST /api/generate
    if ($endpoint === 'generate' && ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET')) {
        // No body required, use stored config
        
        // Parse stored keywords
        $keywordsPool = [];
        $storedKeywords = $apiConfig['keywords'] ?? '';
        
        // Try JSON decode first (legacy)
        $decoded = json_decode($storedKeywords, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $keywordsPool = $decoded;
        } else {
            // Split by newline
            $keywordsPool = array_filter(array_map('trim', explode("\n", $storedKeywords)));
        }
        
        if (empty($keywordsPool)) {
             http_response_code(400);
             echo json_encode(['success' => false, 'error' => 'No keywords configured in this API scheme']);
             exit;
        }

        // Select random keywords based on keyword_count
        $count = (int)($apiConfig['keyword_count'] ?? 1);
        if ($count < 1) $count = 1;
        
        // Ensure we don't request more than available
        $numToSelect = min($count, count($keywordsPool));
        
        $keys = array_rand($keywordsPool, $numToSelect);
        
        $keywords = [];
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $keywords[] = $keywordsPool[$key];
            }
        } else {
            $keywords[] = $keywordsPool[$keys];
        }

        try {
            // Increase timeout
            if (function_exists('set_time_limit')) {
                set_time_limit(600); // Set to 600s as requested by user
            }

            // Determine AI Model Config
            $geminiKey = '';
            $geminiBaseUrl = '';
            $geminiModelName = '';
            $isLyApi = false;
            $usedModelSource = 'unknown'; // Debug info

            // 1. Check if specific AI Model is assigned to this API Config
            if (!empty($apiConfig['ai_model_id'])) {
                $aiModel = AiModel::find($apiConfig['ai_model_id']);
                
                if ($aiModel && $aiModel['is_active']) {
                    $geminiKey = $aiModel['api_key'];
                    $geminiBaseUrl = $aiModel['base_url'];
                    $geminiModelName = $aiModel['model_name'];
                    $isLyApi = !empty($aiModel['is_ly_api']);
                    $usedModelSource = 'assigned_model: ' . $aiModel['name'];
                }
            }

            // 2. Fallback to System Settings if no valid model found
            if (empty($geminiKey)) {
                $geminiKey = $settings['geminiApiKey'] ?? '';
                $geminiBaseUrl = !empty($settings['geminiBaseUrl']) ? $settings['geminiBaseUrl'] : 'https://generativelanguage.googleapis.com';
                $geminiModelName = !empty($settings['geminiModel']) ? $settings['geminiModel'] : 'gemini-2.0-flash-exp';
                $usedModelSource = 'system_fallback';
            }

            if (empty($geminiKey) && !$isLyApi) {
                throw new Exception("System Gemini API Key not configured.");
            }

            require_once __DIR__ . '/../src/Services/GeminiService.php';
            
            $service = new GeminiService($geminiKey, $geminiBaseUrl, $geminiModelName, $isLyApi);
            $articleData = $service->generateArticle(
                $keywords, 
                $apiConfig['geo_region'], 
                $apiConfig['language'], 
                $apiConfig['category_id'],
                $apiConfig['promotion_info'] ?? ''
            );

            // Attach debug info
            $articleData['debug_model_source'] = $usedModelSource;
            $articleData['debug_model_name'] = $geminiModelName;
            $articleData['debug_api_ai_model_id'] = $apiConfig['ai_model_id'] ?? 'null';

            // Fix: Enforce Category and Auto Link settings from API Config
            $articleData['category_id'] = !empty($apiConfig['category_id']) ? (int)$apiConfig['category_id'] : 0;
            $articleData['auto_link'] = !empty($apiConfig['auto_link']) ? (int)$apiConfig['auto_link'] : 0;
            
            // Fix: Enforce Geo Region and Language
            $articleData['geo_region'] = $apiConfig['geo_region'];
            $articleData['language'] = $apiConfig['language'];
            
            // --- Image Insertion Logic ---
            $imageCount = (int)($apiConfig['insert_image_count'] ?? 0);
            if ($imageCount > 0) {
                $position = $apiConfig['insert_image_position'] ?? 'random';
                $sourceType = $apiConfig['image_source_type'] ?? 'picsum';
                $content = $articleData['content'];
                
                // Prepare Image Pool
                $imagePool = [];
                if ($sourceType === 'custom_url' && !empty($apiConfig['custom_image_urls'])) {
                    // Split by newline and filter empty
                    $imagePool = array_filter(array_map('trim', explode("\n", $apiConfig['custom_image_urls'])));
                } elseif ($sourceType === 'media_library' && !empty($apiConfig['media_category_id'])) {
                    // Fetch images from media library category
                    $mediaFiles = MediaFile::getAll(100, 0, (int)$apiConfig['media_category_id']);
                    if (!empty($mediaFiles)) {
                        $imagePool = array_column($mediaFiles, 'path');
                    }
                }

                // Helper to generate image markdown
                $getImageMd = function($keyword) use ($sourceType, $imagePool) {
                    $url = '';
                    $alt = "Image related to $keyword";
                    
                    if (($sourceType === 'custom_url' || $sourceType === 'media_library') && !empty($imagePool)) {
                        // Pick random from pool
                        $url = $imagePool[array_rand($imagePool)];
                    } else {
                        // Use picsum.photos seeded with keyword hash
                        $seed = crc32($keyword . uniqid()); 
                        $url = "https://picsum.photos/800/600?random=$seed";
                    }
                    
                    return "\n\n![{$alt}]({$url})\n\n";
                };

                if ($position === 'head') {
                    for ($i = 0; $i < $imageCount; $i++) {
                        $content = $getImageMd($keywords[0]) . $content;
                    }
                } elseif ($position === 'tail') {
                    for ($i = 0; $i < $imageCount; $i++) {
                        $content .= $getImageMd($keywords[0]);
                    }
                } elseif ($position === 'average') {
                     // Split content by paragraphs
                     $paragraphs = explode("\n\n", $content);
                     $totalParagraphs = count($paragraphs);
                     
                     if ($totalParagraphs > $imageCount) {
                         $interval = floor($totalParagraphs / ($imageCount + 1));
                         for ($i = 1; $i <= $imageCount; $i++) {
                             $insertPos = $i * $interval;
                             if (isset($paragraphs[$insertPos])) {
                                 $paragraphs[$insertPos] .= $getImageMd($keywords[0]);
                             }
                         }
                         $content = implode("\n\n", $paragraphs);
                     } else {
                         // Not enough paragraphs, fallback to random
                         $position = 'random'; 
                     }
                }
                
                if ($position === 'random') { // random (fallback or explicit)
                    // Split content by paragraphs
                    $paragraphs = explode("\n\n", $content);
                    $totalParagraphs = count($paragraphs);
                    
                    if ($totalParagraphs > 2) {
                        for ($i = 0; $i < $imageCount; $i++) {
                            // Insert at random position, avoiding first and last if possible
                            $insertPos = rand(1, max(1, $totalParagraphs - 1));
                            // Add image to that paragraph
                            if (isset($paragraphs[$insertPos])) {
                                $paragraphs[$insertPos] .= $getImageMd($keywords[0]);
                            } else {
                                $paragraphs[] = $getImageMd($keywords[0]);
                            }
                        }
                        $content = implode("\n\n", $paragraphs);
                    } else {
                        // Content too short, just append
                        $content .= $getImageMd($keywords[0]);
                    }
                }
                
                $articleData['content'] = $content;
                
                // Set cover image if not set
                if (empty($articleData['cover_image'])) {
                     // Generate a cover image using the same logic
                     if (($sourceType === 'custom_url' || $sourceType === 'media_library') && !empty($imagePool)) {
                        $articleData['cover_image'] = $imagePool[array_rand($imagePool)];
                     } else {
                        $articleData['cover_image'] = "https://picsum.photos/800/600?random=" . time();
                     }
                }
            }
            
            // Set Article Status from Config
            $articleData['status'] = isset($apiConfig['article_status']) ? (int)$apiConfig['article_status'] : 1;

            // Create Article
            $articleId = Article::create($articleData);
            $articleData['id'] = $articleId;
             
             // Sync Tags
            if (!empty($articleData['tags']) && is_array($articleData['tags'])) {
                Article::syncTags($articleId, $articleData['tags']);
            }

            // Increment call count
            ApiConfig::incrementCallCount($apiConfig['id']);
            
            // Scheme Progress Tracking
            if (!empty($apiConfig['scheme_id']) && !empty($apiConfig['user_id'])) {
                require_once __DIR__ . '/../src/Models/AiScheme.php';
                require_once __DIR__ . '/../src/Models/User.php';
                
                $scheme = AiScheme::find($apiConfig['scheme_id']);
                if ($scheme) {
                    $cost = $scheme['cost_per_post'] ?? 1;
                    $newGeneratedCount = $scheme['generated_count'] + 1;
                    
                    // Consume frozen points (deduct from user's frozen balance)
                    $reason = !empty($articleData['title']) ? "生成文章: {$articleData['title']}" : "生成文章";
                    User::consumeFrozenPoints($scheme['user_id'], $cost, $reason);
                    AiScheme::updateProgress($scheme['id'], $newGeneratedCount, $cost);
                    
                    if ($newGeneratedCount >= $scheme['target_count']) {
                        AiScheme::updateStatus($scheme['id'], 'completed');
                        // Disable API to prevent over-generation
                        ApiConfig::update($apiConfig['id'], ['status' => 0]);
                    } else {
                        if ($scheme['status'] === 'approved') {
                            AiScheme::updateStatus($scheme['id'], 'running');
                        }
                    }
                }
            }
            
            echo json_encode(['success' => true, 'data' => $articleData]);

        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // GET /api/articles
    if ($endpoint === 'articles' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        if ($subId) {
            // Get Single
            $article = Article::getBySlug($subId); // Assuming subId is slug or uuid? API usually uses UUID.
            // But Article::find($id) uses ID.
            // Let's search by UUID first? Article model doesn't have findByUuid explicitly shown but database has uuid.
            // Let's assume standard retrieval for now. 
            // Ideally we should look up by UUID.
            // Let's use getBySlug for now as it's available, or check if we can query by UUID.
            
            // Quick check if it's a UUID (simplified check)
            if (preg_match('/^[0-9a-f]{8}-/', $subId)) {
                // It's likely a UUID, but we don't have findByUuid in Article class shown earlier.
                // We'll stick to listing logic or implement getByUuid if needed. 
                // For now, let's just use existing logic or fail gracefully.
                // Actually, let's implement a simple direct query if needed, or just return 404 for now if not found.
                // Wait, I can use a simple query here.
                $pdo = Database::getInstance()->getConnection();
                $stmt = $pdo->prepare("SELECT * FROM articles WHERE uuid = ?");
                $stmt->execute([$subId]);
                $article = $stmt->fetch();
            } else {
                $article = Article::getBySlug($subId);
            }

            if ($article) {
                echo json_encode(['success' => true, 'data' => $article]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Article not found']);
            }
        } else {
            // List
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            $articles = Article::getLatest($limit, $offset); // This gets published ones
            echo json_encode(['success' => true, 'data' => $articles]);
        }
        exit;
    }

    // 404 for API
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'API endpoint not found']);
    exit;
}

// Check if we should show landing page as homepage
$showLandingAsHomepage = isset($settings['showLandingAsHomepage']) ? $settings['showLandingAsHomepage'] : true;

// Homepage Logic
if ($uri === '/' || $uri === '/index.php') {
    if ($showLandingAsHomepage) {
        // Show Landing Page as Homepage
        // 获取分类用于导航栏
        $categories = Category::getAll();
        // 获取最新新闻文章 (例如 6 篇)
        $latestArticles = Article::getLatest(6);
        require __DIR__ . '/../templates/landing.php';
    } else {
        // Show Blog as Homepage
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

// Redirect /blog to /news
if ($uri === '/blog') {
    header("Location: /news", true, 301);
    exit;
}
if (strpos($uri, '/blog/') === 0) {
    $newUri = '/news' . substr($uri, 5);
    header("Location: " . $newUri, true, 301);
    exit;
}

// News Page (formerly Blog)
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

// Article Detail (Root Path)
if (count($parts) === 1 && !empty($parts[0])) {
    $slug = $parts[0];
    
    // 1. Try Article
    $article = Article::getBySlug($slug);
    if ($article) {
        // Increment views
        Article::incrementViews($article['id']);
        
        // Fetch Category
        $category = null;
        if ($article['category_id']) {
            $category = Category::find($article['category_id']);
        }
        
        // Fetch Tags
        $tags = Article::getTags($article['id']);
        
        require __DIR__ . '/../templates/article.php';
        exit;
    }

    // 2. Try Category
    $category = Category::getBySlug($slug);
    if ($category) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($settings['articlesPerPage']) ? (int)$settings['articlesPerPage'] : 10;
        $offset = ($page - 1) * $limit;
        
        $articles = Article::getByCategory($category['id'], $limit, $offset);
        $total = Article::countByCategory($category['id']);
        $totalPages = ceil($total / $limit);
        
        // Fetch all categories for sidebar
        $categories = Category::getAll();
        
        require __DIR__ . '/../templates/category.php';
        exit;
    }
}

// Old routes for backward compatibility (Optional, redirect to new)
if ($parts[0] === 'article' && !empty($parts[1])) {
    header("Location: /" . $parts[1] . ".html", true, 301);
    exit;
}
if ($parts[0] === 'category' && !empty($parts[1])) {
    header("Location: /" . $parts[1] . ".html", true, 301);
    exit;
}

// Tag Page
if ($parts[0] === 'tag' && !empty($parts[1])) {
    $slug = $parts[1];
    $tag = Tag::getBySlug($slug);
    
    if ($tag) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($settings['articlesPerPage']) ? (int)$settings['articlesPerPage'] : 10;
        $offset = ($page - 1) * $limit;
        
        $articles = Article::getByTag($tag['id'], $limit, $offset);
        $total = Article::countByTag($tag['id']);
        $totalPages = ceil($total / $limit);
        
        require __DIR__ . '/../templates/tag.php';
    } else {
        http_response_code(404);
        echo "404 Not Found";
    }
    exit;
}

// Search Page
if ($parts[0] === 'search') {
    $keyword = $_GET['q'] ?? '';
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($settings['articlesPerPage']) ? (int)$settings['articlesPerPage'] : 10;
    $offset = ($page - 1) * $limit;
    
    $articles = [];
    $total = 0;
    $totalPages = 0;
    
    if ($keyword) {
        $articles = Article::search($keyword, $limit, $offset);
        $total = Article::countSearch($keyword);
        $totalPages = ceil($total / $limit);
    }
    
    require __DIR__ . '/../templates/search.php';
    exit;
}

// Categories Page
if ($uri === '/categories' || $uri === '/categories/') {
    $categories = Category::getAll();
    require __DIR__ . '/../templates/categories.php';
    exit;
}

// About Page
if ($uri === '/about') {
    require __DIR__ . '/../templates/about.php';
    exit;
}

// Privacy Policy
if ($uri === '/privacy' || $uri === '/privacy-policy') {
    require __DIR__ . '/../templates/privacy.php';
    exit;
}

// Terms of Service
if ($uri === '/terms' || $uri === '/terms-of-service') {
    require __DIR__ . '/../templates/terms.php';
    exit;
}

// Sitemap
if ($uri === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    echo SitemapGenerator::generate();
    exit;
}

// Robots.txt
if ($uri === '/robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    $robots = isset($settings['robotsTxt']) && !empty($settings['robotsTxt']) 
        ? $settings['robotsTxt'] 
        : "User-agent: *\nDisallow: /admin/\nSitemap: " . ($settings['siteUrl'] ?? '') . "/sitemap.xml";
    echo $robots;
    exit;
}

// 404
http_response_code(404);

// Get some DB info for debugging
$debugDbInfo = "";
try {
    $pdo = Database::getInstance()->getConnection();
    
    // Check Articles
    $stmt = $pdo->query("SELECT slug, title FROM articles ORDER BY id DESC LIMIT 5");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $debugDbInfo .= "<h3>Latest Articles in DB:</h3><ul>";
    foreach ($articles as $a) {
        $debugDbInfo .= "<li>Slug: <strong>" . htmlspecialchars($a['slug']) . "</strong> (Title: " . htmlspecialchars($a['title']) . ")</li>";
    }
    $debugDbInfo .= "</ul>";
    
    // Check Categories
    $stmt = $pdo->query("SELECT slug, name FROM categories LIMIT 5");
    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $debugDbInfo .= "<h3>Categories in DB:</h3><ul>";
    foreach ($cats as $c) {
        $debugDbInfo .= "<li>Slug: <strong>" . htmlspecialchars($c['slug']) . "</strong> (Name: " . htmlspecialchars($c['name']) . ")</li>";
    }
    $debugDbInfo .= "</ul>";
} catch (Exception $e) {
    $debugDbInfo .= "<p>DB Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>404 Not Found - Debug Mode</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; line-height: 1.5; }
        .debug-box { background: #f0f0f0; padding: 1rem; border-radius: 8px; margin-top: 2rem; border: 1px solid #ccc; }
        code { background: #e0e0e0; padding: 0.2rem 0.4rem; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>404 Not Found</h1>
    <p>您访问的页面不存在。</p>
    <p><a href='/'>返回首页</a></p>
    
    <div class='debug-box'>
        <h2>🔍 调试信息 (Debug Info)</h2>
        <p><strong>Request URI:</strong> <code>" . htmlspecialchars($_SERVER['REQUEST_URI']) . "</code></p>
        <p><strong>Processed URI (decoded):</strong> <code>" . htmlspecialchars($uri) . "</code></p>
        <p><strong>Parsed Slug:</strong> <code>" . htmlspecialchars($slug ?? 'null') . "</code></p>
        
        <hr>
        " . $debugDbInfo . "
    </div>
</body>
</html>";
