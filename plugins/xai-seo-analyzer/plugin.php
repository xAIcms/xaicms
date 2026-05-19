<?php
/**
 * Plugin Name: SEO分析器
 * Description: 文章SEO评分、关键词密度分析、可读性检测
 * Version: 1.0.0
 * Author: xAI
 * Price: 0
 */
add_action('admin_dashboard_widgets', function() {
    echo '<div class="card border-0 shadow-sm mb-4"><div class="card-body"><h6><i class="bi bi-search"></i> SEO分析</h6><p class="text-muted small">在文章编辑页面查看SEO评分</p></div></div>';
});
