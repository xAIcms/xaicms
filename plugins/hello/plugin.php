<?php
/**
 * Plugin Name: Hello xAI
 * Description: Example plugin — demonstrates all available hooks
 * Version: 1.1.0
 * Author: xAIcms
 *
 * This plugin shows how to use every available hook in xAI CMS.
 * Use it as a template for building your own plugins.
 */

// ── HOOK: Before footer (frontend) ──
add_action('before_footer', function () {
    echo '<div style="text-align:center;padding:10px;font-size:12px;color:#999;">';
    echo 'Powered by <a href="https://github.com/xAIcms/xaicms" style="color:#6366f1;">xAI CMS</a>';
    echo '</div>';
});

// ── HOOK: Before page output ends ──
add_action('before_output', function () {
    // Inject analytics or tracking scripts here
    // echo '<script>console.log("xAI CMS loaded");</script>';
});

// ── HOOK: Admin dashboard widgets ──
add_action('admin_dashboard_widgets', function () {
    global $settings;
    echo '<div class="col-md-6 mb-4"><div class="card"><div class="card-body">';
    echo '<h5>System Info</h5>';
    echo '<p>PHP ' . phpversion() . ' | MySQL | Plugins loaded: ' . count(Plugin::loaded());
    echo '</p></div></div></div>';
});

// ── HOOK: Article saved ──
add_action('article_saved', function ($articleId, $data) {
    // Log or process after article is created/updated
    // error_log("Article #$articleId saved: " . ($data['title'] ?? 'Untitled'));
});

// ── HOOK: User registered ──
add_action('user_registered', function ($userId, $data) {
    // Send welcome email, assign default role, etc.
    // error_log("New user #$userId registered: " . ($data['email'] ?? ''));
});

// ── HOOK: Admin menu (add custom sidebar items) ──
add_action('admin_menu', function () {
    global $uri;
    $uri = $uri ?? ($_SERVER['REQUEST_URI'] ?? '');
    ?>
    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-muted text-uppercase small fw-bold">
        <span>Plugins</span>
    </h6>
    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a class="nav-link <?php echo (strpos($uri, '/admin/plugins') !== false) ? 'active' : ''; ?>" href="/admin/plugins">
                <i class="bi bi-puzzle me-2"></i>
                Manage Plugins
            </a>
        </li>
    </ul>
    <?php
});

// ── FILTER: Article title ──
add_filter('article_title', function ($title) {
    if (stripos($title, 'ai') !== false || stripos($title, 'geo') !== false) {
        return $title;
    }
    return $title;
});
