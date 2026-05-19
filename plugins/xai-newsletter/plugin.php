<?php
/**
 * Plugin Name: 邮件订阅
 * Description: 访客邮件订阅，新文章自动推送通知
 * Version: 1.0.0
 * Author: xAI
 * Price: 0
 */
add_action('before_footer', function() {
    echo '<div class="newsletter-signup" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin:20px 0;text-align:center;">';
    echo '<h4 style="margin:0 0 8px;font-size:16px;"><i class="bi bi-envelope"></i> 订阅更新</h4>';
    echo '<p style="color:#64748b;font-size:14px;margin:0 0 12px;">新文章发布时邮件通知你</p>';
    echo '<div style="display:flex;gap:8px;justify-content:center;"><input type="email" placeholder="your@email.com" style="border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;width:240px;"><button style="background:#4f46e5;color:white;border:none;border-radius:8px;padding:8px 16px;cursor:pointer;font-weight:bold;">订阅</button></div>';
    echo '</div>';
});
