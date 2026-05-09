<?php
/**
 * Plugin Name: Hello xAI
 * Description: Example plugin — adds a welcome message to the footer
 * Version: 1.0.0
 * Author: xAIcms
 */

// Add a copyright notice to every page
add_action('before_footer', function () {
    echo '<div style="text-align:center;padding:10px;font-size:12px;color:#999;">';
    echo 'Powered by <a href="https://xaicms.com" style="color:#6366f1;">xAI CMS</a>';
    echo '</div>';
});

// Add a custom admin dashboard widget
add_action('admin_dashboard_widgets', function () {
    echo '<div class="card" style="margin-bottom:20px;">';
    echo '<h3>🟢 System Status</h3>';
    echo '<p>PHP ' . phpversion() . ' | MySQL | All systems operational.</p>';
    echo '</div>';
});

// Filter: modify article titles
add_filter('article_title', function ($title) {
    // Add emoji prefix to trending articles
    if (stripos($title, 'ai') !== false || stripos($title, 'geo') !== false) {
        return '🔥 ' . $title;
    }
    return $title;
});
